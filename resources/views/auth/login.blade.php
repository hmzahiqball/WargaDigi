@extends('layouts.app')

@section('title', 'Masuk ke Akun')

@section('content')
<section class="py-0 py-md-5" style="background-color: #f4f7f6; min-height: calc(100vh - 76px - 280px);">
    <div class="container h-100">
        <div class="row h-100 bg-white shadow-sm overflow-hidden" style="border-radius: 1rem; min-height: 600px;">
            {{-- Left Side: Hero Image --}}
            <div class="col-lg-6 d-none d-lg-flex p-0 position-relative">
                <img src="{{ asset('images/auth-hero.jpg') }}" alt="WargaDigi Community" class="w-100 h-100" style="object-fit: cover;">
                <div class="position-absolute bottom-0 start-0 w-100 p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                    <h2 class="text-white fw-bold mb-2">WargaDigi 21</h2>
                    <p class="text-white-50 mb-0">Digital Gotong Royong. Membangun lingkungan yang lebih baik, terhubung, dan sejahtera bersama-sama.</p>
                </div>
            </div>

            {{-- Right Side: Login Form --}}
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="w-100" style="max-width: 400px;">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; background-color: #e8f5e9; color: #2E7D32; border-radius: 50%;">
                            <i class="bi bi-person-lock fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Masuk ke Akun</h3>
                        <p class="text-muted small">Silakan masukkan kredensial Anda untuk mengakses dashboard.</p>
                    </div>

                    {{-- Error Alert --}}
                    <div id="loginError" class="alert p-2 d-none align-items-center gap-2 mb-3" style="background-color: #fce4ec; color: #c62828; border-radius: 0.5rem; border: none; font-size: 0.85rem;">
                        <i class="bi bi-exclamation-circle"></i>
                        <span id="loginErrorText">Username atau password salah.</span>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Nomor Induk Kependudukan (NIK)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 0.5rem 0 0 0.5rem;">
                                    <i class="bi bi-person-vcard"></i>
                                </span>
                                <input type="text" name="nik" class="form-control" placeholder="Masukkan 16 digit NIK" required value="{{ old('nik') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 0.5rem 0 0 0.5rem;">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" id="loginPassword" class="form-control border-start-0 border-end-0" placeholder="Masukkan password" required autocomplete="current-password">
                                <span class="input-group-text bg-white border-start-0 text-success" id="togglePassword" style="border-radius: 0 0.5rem 0.5rem 0; cursor: pointer;">
                                    <i class="bi bi-eye-fill"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" id="rememberMe">
                                <label class="form-check-label small text-muted" for="rememberMe">Ingat saya</label>
                            </div>
                            <a href="#" class="small text-decoration-none" style="color: #2E7D32; font-weight: 600;">Lupa password?</a>
                        </div>

                        <button type="submit" id="loginBtn" class="btn w-100 fw-bold py-2" style="background-color: #2E7D32; color: white; border-radius: 0.5rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#1B5E20'" onmouseout="this.style.backgroundColor='#2E7D32'">
                            <span id="loginBtnText">Masuk</span>
                            <span id="loginBtnSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Memverifikasi...
                            </span>
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/register" class="text-decoration-none small" style="color: #3949ab; font-weight: 600;">Belum punya akun? Daftar di sini</a>
                    </div>

                    {{-- Demo Credentials Hint 
                    <div class="mt-4 p-3" style="background: #f0f5f0; border-radius: 0.5rem; border: 1px dashed #c8e6c9;">
                        <p class="small text-muted mb-1 fw-semibold"><i class="bi bi-info-circle text-success"></i> Demo Login:</p>
                        <p class="small text-muted mb-0">Admin: <code>admin</code> / <code>admin123</code></p>
                    </div>
                    --}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const errorBox = document.getElementById('loginError');
        const errorText = document.getElementById('loginErrorText');
        const togglePwd = document.getElementById('togglePassword');
        const pwdInput = document.getElementById('loginPassword');
    
        // Toggle password visibility
        togglePwd.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                pwdInput.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });
    });
</script>

{{-- DemoLogin
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const errorBox = document.getElementById('loginError');
        const errorText = document.getElementById('loginErrorText');
        const togglePwd = document.getElementById('togglePassword');
        const pwdInput = document.getElementById('loginPassword');
    
        // Toggle password visibility
        togglePwd.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                pwdInput.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });
    
        // JS-based login (temporary — no database)
        const USERS = {
            admin: { password: 'admin123', role: 'administrator', redirect: '/admin/dashboard' },
            users: {password: 'user123', role: 'user', redirect: '/user/dashboard' }
        };
    
        form.addEventListener('submit', function(e) {
            e.preventDefault();
    
            const username = document.getElementById('loginUsername').value.trim().toLowerCase();
            const password = document.getElementById('loginPassword').value;
    
            // Show loading
            document.getElementById('loginBtnText').classList.add('d-none');
            document.getElementById('loginBtnSpinner').classList.remove('d-none');
            document.getElementById('loginBtn').disabled = true;
            errorBox.classList.add('d-none');
    
            // Simulate network delay
            setTimeout(function() {
                if (USERS[username] && USERS[username].password === password) {
                    // Store session in localStorage (temporary)
                    localStorage.setItem('wargadigi_user', JSON.stringify({
                        username: username,
                        role: USERS[username].role,
                        loginTime: new Date().toISOString()
                    }));
    
                    // Redirect based on role
                    window.location.href = USERS[username].redirect;
                } else {
                    // Show error
                    errorText.textContent = 'Username atau password salah. Silakan coba lagi.';
                    errorBox.classList.remove('d-none');
                    errorBox.classList.add('d-flex');
    
                    document.getElementById('loginBtnText').classList.remove('d-none');
                    document.getElementById('loginBtnSpinner').classList.add('d-none');
                    document.getElementById('loginBtn').disabled = false;
    
                    // Shake animation
                    form.style.animation = 'shake 0.5s ease';
                    setTimeout(() => form.style.animation = '', 500);
                }
            }, 1200);
        });
    });
    </script>
   --}}
<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}
</style>
@endpush
