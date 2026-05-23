@php
$pageConfigs = ['myLayout' => 'blank'];
$customizerHidden = 'customizer-hide';
Helper::updatePageConfig($pageConfigs);
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Daftar Akaun - SISPAQ')

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

  .auth-cover-bg {
    background-color: #f7f8fa !important;
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
    <div class="authentication-inner py-6" style="max-width: 520px; margin: 0 auto;">
      <!-- Register -->
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
            <span class="badge bg-label-primary text-uppercase px-3 py-2">Daftar Akaun Baru</span>
          </div>

          <a href="{{ route('auth.google') }}" class="btn btn-outline-danger d-grid w-100 py-2 fw-bold shadow-sm mb-4">
            <span class="d-flex align-items-center justify-content-center gap-2">
              <i class="ti tabler-brand-google fs-5"></i>
              Daftar / Log Masuk dengan UMMail
            </span>
          </a>

          <div class="divider my-4">
            <div class="divider-text text-uppercase text-muted small fw-bold">Atau Daftar Manual</div>
          </div>

          <form id="formAuthentication" class="mb-4" action="{{ route('register') }}" method="POST">
            @csrf
            
            @if(request()->has('course'))
              <input type="hidden" name="course_id" value="{{ request()->get('course') }}">
            @endif

            <div class="mb-3 form-control-validation">
              <label for="name" class="form-label text-uppercase small fw-bold text-muted">Nama Penuh (seperti dalam IC)</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name') }}" placeholder="Masukkan nama penuh" required autofocus />
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3 form-control-validation">
              <label for="email" class="form-label text-uppercase small fw-bold text-muted">Alamat Emel</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" placeholder="Masukkan alamat emel" required />
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-3 form-control-validation">
                <label for="phone" class="form-label text-uppercase small fw-bold text-muted">No. Telefon</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                  value="{{ old('phone') }}" placeholder="Contoh: 0123456789" required />
                @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3 form-control-validation">
                <label for="ic_number" class="form-label text-uppercase small fw-bold text-muted">No. Kad Pengenalan</label>
                <input type="text" class="form-control @error('ic_number') is-invalid @enderror" id="ic_number" name="ic_number"
                  value="{{ old('ic_number') }}" placeholder="Contoh: 900101145678" required />
                @error('ic_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- University Student Details -->
            <div class="row">
              <div class="col-md-4 mb-3 form-control-validation">
                <label for="student_id" class="form-label text-uppercase small fw-bold text-muted">No. Matrik / Student ID</label>
                <input type="text" class="form-control @error('student_id') is-invalid @enderror" id="student_id" name="student_id"
                  value="{{ old('student_id') }}" placeholder="Contoh: U2001234" />
                @error('student_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4 mb-3 form-control-validation">
                <label for="uni_course" class="form-label text-uppercase small fw-bold text-muted">Kursus Universiti</label>
                <input type="text" class="form-control @error('uni_course') is-invalid @enderror" id="uni_course" name="uni_course"
                  value="{{ old('uni_course') }}" placeholder="Contoh: Sarjana Muda Pengajian Islam" />
                @error('uni_course')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4 mb-3 form-control-validation">
                <label for="uni_faculty" class="form-label text-uppercase small fw-bold text-muted">Fakulti Universiti</label>
                <input type="text" class="form-control @error('uni_faculty') is-invalid @enderror" id="uni_faculty" name="uni_faculty"
                  value="{{ old('uni_faculty') }}" placeholder="Contoh: Akademi Pengajian Islam" />
                @error('uni_faculty')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3 form-password-toggle form-control-validation">
                <label for="password" class="form-label text-uppercase small fw-bold text-muted">Kata Laluan</label>
                <div class="input-group input-group-merge">
                  <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                    placeholder="········" required />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
                @error('password')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3 form-password-toggle form-control-validation">
                <label for="password_confirmation" class="form-label text-uppercase small fw-bold text-muted">Sahkan Kata Laluan</label>
                <div class="input-group input-group-merge">
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="········" required />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <button class="btn btn-navy d-grid w-100 py-2 fw-bold shadow-sm" type="submit">Daftar Akaun</button>
            </div>
          </form>

          <p class="text-center mb-0">
            <span class="text-muted">Sudah mempunyai akaun?</span>
            <a href="{{ route('login') }}" class="fw-bold brand-navy text-decoration-none border-bottom border-primary pb-1">
              <span>Log Masuk</span>
            </a>
          </p>

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
      <!-- /Register -->
    </div>
  </div>
</div>
@endsection
