<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKeuangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PersetujuanKeuanganController extends Controller
{
    /**
     * Determine the current user's approval scope.
     * Ketua RT  → approves RT reports for their own RT
     * Pimpinan RW / Admin RW → approves RW & DKM reports
     */
    private function getApprovalScope()
    {
        $user = Auth::user();

        if ($user->role === 'Ketua RT') {
            return [
                'type' => 'RT',
                'rt_id' => $user->rt_id,
                'label' => 'Ketua RT',
            ];
        }

        // Pimpinan RW or Admin RW
        return [
            'type' => 'RW',
            'rt_id' => null,
            'label' => 'Pimpinan RW',
        ];
    }

    /**
     * Display the list of reports awaiting approval.
     */
    public function index()
    {
        $scope = $this->getApprovalScope();
        $user = Auth::user();

        $pendingQuery = LaporanKeuangan::with(['rt', 'pembuat'])
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc');

        if ($scope['type'] === 'RT') {
            // Ketua RT: only see RT reports from their RT that are Submitted
            $pendingQuery->where('status', 'Submitted')
                         ->where('unit', 'RT')
                         ->where('rt_id', $scope['rt_id']);
        } else {
            // Pimpinan RW: see Submitted for RW & DKM, and Disetujui RT for RT
            $pendingQuery->where(function ($q) {
                $q->whereIn('unit', ['RW', 'DKM'])->where('status', 'Submitted')
                  ->orWhere(function ($q2) {
                      $q2->where('unit', 'RT')->where('status', 'Disetujui RT');
                  });
            });
        }

        $pending = $pendingQuery->paginate(10, ['*'], 'pending_page');

        // Also show recently processed (Approved/Rejected) for history
        $historyQuery = LaporanKeuangan::with(['rt', 'pembuat', 'penyetuju'])
            ->whereIn('status', ['Approved', 'Rejected'])
            ->orderBy('tanggal_disetujui', 'desc');

        if ($scope['type'] === 'RT') {
            $historyQuery->where('unit', 'RT')->where('rt_id', $scope['rt_id']);
        } else {
            $historyQuery->whereIn('unit', ['RW', 'DKM']);
        }

        $history = $historyQuery->paginate(10, ['*'], 'history_page');

        // Determine route prefix based on role
        $routePrefix = $scope['type'] === 'RT' ? 'rt.persetujuan-keuangan' : 'rw.persetujuan-keuangan';

        return view('opKeuangan.persetujuan.index', [
            'pending' => $pending,
            'history' => $history,
            'scope' => $scope,
            'routePrefix' => $routePrefix,
        ]);
    }

    /**
     * Approve a submitted report.
     */
    public function approve(Request $request, $id)
    {
        $scope = $this->getApprovalScope();
        $laporan = LaporanKeuangan::findOrFail($id);

        $this->authorizeAction($laporan, $scope);

        if ($scope['type'] === 'RT' && $laporan->status !== 'Submitted') {
            return back()->with('error', 'Laporan ini tidak dalam status Submitted.');
        }

        if ($scope['type'] === 'RW' && !in_array($laporan->status, ['Submitted', 'Disetujui RT'])) {
            return back()->with('error', 'Laporan ini tidak dapat diproses.');
        }

        $newStatus = $scope['type'] === 'RT' ? 'Disetujui RT' : 'Approved';

        $laporan->update([
            'status' => $newStatus,
            'disetujui_oleh' => Auth::id(),
            'tanggal_disetujui' => Carbon::now(),
            'catatan' => $request->input('catatan'),
        ]);

        return back()->with('success', 'Laporan berhasil disetujui.');
    }

    /**
     * Reject a submitted report.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ], [
            'catatan.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $scope = $this->getApprovalScope();
        $laporan = LaporanKeuangan::findOrFail($id);

        $this->authorizeAction($laporan, $scope);

        if ($scope['type'] === 'RT' && $laporan->status !== 'Submitted') {
            return back()->with('error', 'Laporan ini tidak dalam status Submitted.');
        }

        if ($scope['type'] === 'RW' && !in_array($laporan->status, ['Submitted', 'Disetujui RT'])) {
            return back()->with('error', 'Laporan ini tidak dapat diproses.');
        }

        $laporan->update([
            'status' => 'Rejected',
            'disetujui_oleh' => Auth::id(),
            'tanggal_disetujui' => Carbon::now(),
            'catatan' => $request->input('catatan'),
        ]);

        return back()->with('success', 'Laporan berhasil ditolak.');
    }

    /**
     * Authorize that the current user can act on this report.
     */
    private function authorizeAction(LaporanKeuangan $laporan, array $scope)
    {
        if ($scope['type'] === 'RT') {
            if ($laporan->unit !== 'RT' || $laporan->rt_id !== $scope['rt_id']) {
                abort(403, 'Anda tidak memiliki akses untuk laporan ini.');
            }
        } else {
            // RW can access RW, DKM, and RT (if RT has been approved by Ketua RT)
            if (!in_array($laporan->unit, ['RW', 'DKM', 'RT'])) {
                abort(403, 'Anda tidak memiliki akses untuk laporan ini.');
            }
            if ($laporan->unit === 'RT' && $laporan->status === 'Submitted') {
                abort(403, 'Laporan RT ini harus disetujui oleh Ketua RT terlebih dahulu.');
            }
        }
    }
}
