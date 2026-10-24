@extends('layouts.global')

@section('title', 'Manajemen Hak Akses')

@section('content')
<div class="admin-page-header">
    <h1>Manajemen Hak Akses</h1>
    <p class="text-muted">Kelola peran (role) setiap pengguna sistem. Perubahan langsung tersinkron dengan sistem hak akses (RBAC).</p>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="border-radius: 0.5rem;">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 0.5rem;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Buat Akun Pengurus (tanpa NIK, login via username) --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-body">
        <h5 class="fw-bold mb-1"><i class="bi bi-person-badge me-2 text-success"></i>Buat Akun Pengurus</h5>
        <p class="text-muted small mb-3">Akun pengurus/petugas tidak memerlukan NIK dan masuk menggunakan username.</p>
        <form method="POST" action="{{ route('admin.manajemen-hak-akses.pengurus.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control admin-input" placeholder="mis. ketuart02rw12" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control admin-input" placeholder="Min. 8 karakter" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control admin-input" placeholder="Ulangi password" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100" style="border-radius: 0.5rem;">
                        <i class="bi bi-plus-lg"></i> Buat
                    </button>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold d-block">Peran (boleh lebih dari satu)</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($pengurusRoles as $role)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role }}" id="newrole_{{ $loop->index }}"
                                    {{ collect(old('roles', []))->contains($role) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="newrole_{{ $loop->index }}">{{ $role }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Search --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.manajemen-hak-akses') }}">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="admin-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Cari nama pengguna atau NIK..." id="searchUser" class="admin-input form-control" value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="submit" class="btn btn-success px-4" style="border-radius: 0.5rem;">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Users Table --}}
<div class="admin-card mb-4 animate-in delay-2">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>NIK</th>
                        <th>Status Akun</th>
                        <th style="width: 320px;">Role / Hak Akses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <span class="fw-semibold">{{ $user->name }}</span>
                                <small class="d-block text-muted">{{ $user->username }}</small>
                            </div>
                        </td>
                        <td><span class="text-muted">{{ $user->nik }}</span></td>
                        <td>
                            @if($user->status_akun === 'Active')
                                <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $user->status_akun }}</span>
                            @endif
                        </td>
                        <td>
                            @php $assigned = $userRoles[$user->id] ?? []; @endphp
                            <form method="POST" action="{{ route('admin.manajemen-hak-akses.update', $user->id) }}" class="d-flex align-items-start gap-2">
                                @csrf
                                @method('PUT')
                                <select name="roles[]" multiple size="4" class="form-select admin-input" style="min-height: 110px;">
                                    @foreach($roles as $role)
                                        @php $disabledWarga = ($role === 'Warga' && empty($user->nik)); @endphp
                                        <option value="{{ $role }}" {{ in_array($role, $assigned, true) ? 'selected' : '' }} {{ $disabledWarga ? 'disabled' : '' }}>
                                            {{ $role }}{{ $disabledWarga ? ' (butuh NIK)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success" style="border-radius: 0.5rem;" title="Simpan peran">
                                    <i class="bi bi-floppy"></i>
                                </button>
                            </form>
                            <small class="text-muted d-block mt-1">Ctrl/Cmd + klik untuk memilih lebih dari satu.</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="admin-card-footer">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
            <div>{{ $users->links() }}</div>
        </div>
    </div>
    @endif
</div>
@endsection
