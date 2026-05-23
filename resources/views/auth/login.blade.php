@php
$pageConfigs = ['myLayout' => 'blank'];
$customizerHidden = 'customizer-hide';
Helper::updatePageConfig($pageConfigs);
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Log Masuk - SISPAQ')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
  /* Import Google Fonts */
  @import url('https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600;700&display=swap');

  body, html, .authentication-wrapper {
    font-family: 'Poppins', sans-serif !important;
  }

  .arabic-title {
    font-family: 'Amiri', serif !important;
    font-size: 1.8rem;
    font-weight: 700;
    color: #192f59;
    direction: rtl;
  }

  .brand-navy {
    color: #192f59 !important;
  }

  .brand-gold {
    color: #fdc800 !important;
  }

  .btn-navy {
    background-color: #192f59 !important;
    border-color: #192f59 !important;
    color: #fff !important;
  }

  .btn-navy:hover {
    background-color: #112140 !important;
    border-color: #112140 !important;
  }

  .btn-outline-navy {
    color: #192f59 !important;
    border-color: #192f59 !important;
    background-color: transparent !important;
  }

  .btn-outline-navy:hover {
    background-color: rgba(25, 47, 89, 0.08) !important;
  }

  .auth-cover-bg {
    background-color: #f7f8fa !important;
  }

  .text-gold-light {
    color: #fdc800 !important;
  }

  .authentication-bg {
    background-color: #ffffff !important;
    border-left: 1px solid #eef0f3;
  }
</style>
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6" style="max-width: 460px; margin: 0 auto;">
      <!-- Login -->
      <div class="card shadow-lg border-0">
        <div class="card-body p-sm-8 p-6">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6" style="height: auto;">
            <a href="{{ url('/') }}" class="app-brand-link gap-2 text-decoration-none">
              <img src="{{ asset('assets/img/branding/um-logo.png') }}" alt="Universiti Malaya Logo" style="height: 70px; width: auto; object-fit: contain;" />
            </a>
          </div>
          <!-- /Logo -->
          
          <h4 class="arabic-title mb-2 text-center" style="font-size: 1.4rem;">سِيسْبَاقْ - قِسْمُ الدِّرَاسَاتِ الإِسْلَامِيَّةِ</h4>
          <h5 class="fw-bold brand-navy text-center mb-1" style="font-size: 1.1rem;">Sistem Pengurusan Program Ikhtisas</h5>
          <p class="text-muted text-center mb-4 small">Akademi Pengajian Islam Universiti Malaya (APIUM)</p>
          
          <div class="text-center mb-4">
            <span class="badge bg-label-primary text-uppercase px-3 py-2">Log Masuk Portal</span>
          </div>

          <a href="{{ route('auth.google') }}" class="btn btn-outline-danger d-grid w-100 py-2 fw-bold shadow-sm mb-4">
            <span class="d-flex align-items-center justify-content-center gap-2">
              <i class="ti tabler-brand-google fs-5"></i>
              Log Masuk dengan UMMail
            </span>
          </a>

          <div class="divider my-4">
            <div class="divider-text text-uppercase text-muted small fw-bold">Atau Guna Emel</div>
          </div>

          <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4 form-control-validation">
              <label for="email" class="form-label text-uppercase small fw-bold text-muted">Alamat Emel</label>
              <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" placeholder="Masukkan emel anda" autofocus />
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-4 form-password-toggle form-control-validation">
              <div class="d-flex justify-content-between">
                <label class="form-label text-uppercase small fw-bold text-muted" for="password">Kata Laluan</label>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password"
                  placeholder="············" aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="my-4">
              <div class="d-flex justify-content-between">
                <div class="form-check mb-0">
                  <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                  <label class="form-check-label text-muted" for="remember-me"> Ingat Saya </label>
                </div>
              </div>
            </div>
            <button class="btn btn-navy d-grid w-100 py-2 fw-bold shadow-sm" type="submit">Log Masuk</button>
          </form>

          <p class="text-center">
            <span class="text-muted">Baru di platform kami?</span>
            <a href="{{ route('register') }}" class="fw-bold brand-navy text-decoration-none border-bottom border-primary pb-1">
              <span>Daftar Akaun Baru</span>
            </a>
          </p>

          <div class="divider my-5">
            <div class="divider-text text-uppercase text-muted small fw-bold">Akaun Ujian Pantas</div>
          </div>

          <!-- Quick Test Credentials Box -->
          <div class="bg-light p-4 rounded-3 border">
            <div class="d-grid gap-2">
              <button type="button" class="btn btn-xs btn-outline-navy text-start py-2" onclick="fillCredentials('pelajar@sispaq.com', 'password')">
                <strong>Pelajar:</strong> pelajar@sispaq.com <span class="badge bg-secondary float-end">Student</span>
              </button>
              <button type="button" class="btn btn-xs btn-outline-navy text-start py-2" onclick="fillCredentials('ustaz@sispaq.com', 'password')">
                <strong>Guru:</strong> ustaz@sispaq.com <span class="badge bg-success float-end">Teacher</span>
              </button>
              <button type="button" class="btn btn-xs btn-outline-navy text-start py-2" onclick="fillCredentials('admin@sispaq.com', 'password')">
                <strong>Pentadbir:</strong> admin@sispaq.com <span class="badge bg-danger float-end">Admin</span>
              </button>
            </div>
            <div class="text-center text-muted small mt-3">Kata Laluan Ujian: <code>password</code></div>
          </div>

          <!-- UMMail Access Guide -->
          <div class="card bg-label-info border-0 shadow-none mt-4">
            <div class="card-body p-3">
              <div class="d-flex align-items-start gap-2">
                <i class="ti tabler-mail-opened text-info fs-4 mt-0.5"></i>
                <div>
                  <h6 class="card-title fw-bold mb-2 text-info" style="font-size: 0.85rem; letter-spacing: 0.5px;">PANDUAN AKSES UMMAIL / UMMAIL GUIDE</h6>
                  <ol class="ps-3 mb-0 small text-muted" style="font-size: 0.78rem; line-height: 1.45;">
                    <li class="mb-2">
                      <strong>Baru kepada UMMail? / New to UMMail?</strong><br>
                      Sila gunakan emel rasmi Universiti Malaya anda yang aktif.
                    </li>
                    <li>
                      <strong>Cara Mengakses / How to Access UMMail:</strong><br>
                      Log masuk emel anda melalui <a href="https://mail.google.com/a/um.edu.my" target="_blank" class="fw-bold text-info text-decoration-underline">mail.google.com/a/um.edu.my</a> atau <a href="https://gmail.com" target="_blank" class="fw-bold text-info text-decoration-underline">gmail.com</a>.
                    </li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
</div>

<script>
  function fillCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
  }
</script>
@endsection
