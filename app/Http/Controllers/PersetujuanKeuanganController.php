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
            $rtId = $user->rt_id;
            if (!$rtId) {
                // Fallback: cari rt_id dari relasi Penduduk -> Keluarga jika belum terisi di users
                $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first();
                if ($penduduk && $penduduk->keluarga) {
                    $rtId = $penduduk->keluarga->rt_id;
                }
                if (!$rtId) {
                    $rt01 = \App\Models\MasterRt::where('kode_rt', '01')->first() ?? \App\Models\MasterRt::first();
                    $rtId = $rt01 ? $rt01->id : null;
                }
            }

            return [
                'type' => 'RT',
                'rt_id' => $rtId,
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
            // Ketua RT: see RT reports for their RT (and legacy reports where rt_id is null) that are Submitted
            $pendingQuery->where('status', 'Submitted')
                         ->where('unit', 'RT');
            if ($scope['rt_id']) {
                $pendingQuery->where(function ($q) use ($scope) {
                    $q->where('rt_id', $scope['rt_id'])
                      ->orWhereNull('rt_id');
                });
            }
        } else {
            // Pimpinan RW: see Submitted for RW & DKM
            $pendingQuery->whereIn('unit', ['RW', 'DKM'])->where('status', 'Submitted');
        }

        $pending = $pendingQuery->paginate(10, ['*'], 'pending_page');

        // Also show recently processed (Approved/Rejected) for history
        $historyQuery = LaporanKeuangan::with(['rt', 'pembuat', 'penyetuju'])
            ->whereIn('status', ['Approved', 'Rejected'])
            ->orderBy('tanggal_disetujui', 'desc');

        if ($scope['type'] === 'RT') {
            $historyQuery->where('unit', 'RT');
            if ($scope['rt_id']) {
                $historyQuery->where(function ($q) use ($scope) {
                    $q->where('rt_id', $scope['rt_id'])
                      ->orWhereNull('rt_id');
                });
            }
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

        if ($laporan->status !== 'Submitted') {
            return back()->with('error', 'Laporan ini tidak dalam status Submitted.');
        }

        $updateData = [
            'status' => 'Approved',
            'disetujui_oleh' => Auth::id(),
            'tanggal_disetujui' => Carbon::now(),
            'catatan' => $request->input('catatan'),
        ];

        // Auto-heal rt_id jika sebelumnya tersimpan null
        if (!$laporan->rt_id && $scope['rt_id']) {
            $updateData['rt_id'] = $scope['rt_id'];
        }

        $laporan->update($updateData);

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

        if ($laporan->status !== 'Submitted') {
            return back()->with('error', 'Laporan ini tidak dalam status Submitted.');
        }

        $updateData = [
            'status' => 'Rejected',
            'disetujui_oleh' => Auth::id(),
            'tanggal_disetujui' => Carbon::now(),
            'catatan' => $request->input('catatan'),
        ];

        // Auto-heal rt_id jika sebelumnya tersimpan null
        if (!$laporan->rt_id && $scope['rt_id']) {
            $updateData['rt_id'] = $scope['rt_id'];
        }

        $laporan->update($updateData);

        return back()->with('success', 'Laporan berhasil ditolak.');
    }

    /**
     * Authorize that the current user can act on this report.
     */
    private function authorizeAction(LaporanKeuangan $laporan, array $scope)
    {
        if ($scope['type'] === 'RT') {
            if ($laporan->unit !== 'RT') {
                abort(403, 'Anda tidak memiliki akses untuk laporan ini.');
            }
            if ($scope['rt_id'] && $laporan->rt_id && $laporan->rt_id !== $scope['rt_id']) {
                abort(403, 'Anda tidak memiliki akses untuk laporan RT lain.');
            }
        } else {
            if (!in_array($laporan->unit, ['RW', 'DKM'])) {
                abort(403, 'Anda tidak memiliki akses untuk laporan ini.');
            }
        }
    }
}
