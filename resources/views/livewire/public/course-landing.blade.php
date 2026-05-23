<div>
  <!-- Hero Section -->
  <div class="sispaq-hero text-white py-5 text-center position-relative overflow-hidden">
    <div class="container py-4">
      <h1 class="display-4 fw-bold text-white mb-2">🏗️ SISPAQ</h1>
      <h2 class="h3 text-white-50 mb-4">Sistem Pengurusan Program Ikhtisas APIUM</h2>
      <p class="lead mb-4 mx-auto" style="max-width: 600px;">
        Akademi Pengajian Islam Universiti Malaya (APIUM) menawarkan 8 program ikhtisas berkualiti tinggi untuk memperkasa kefahaman al-Quran, hadis, bahasa Arab, falak, dan perubatan Islam.
      </p>

      <!-- Countdown Timer Card -->
      <div class="card bg-white text-dark d-inline-block px-4 py-3 shadow-lg border-0 mb-4 rounded-3">
        <h5 class="text-uppercase text-muted small fw-bold mb-2">Tarikh Tutup Permohonan Semasa</h5>
        <div class="d-flex align-items-center justify-content-center text-center">
          <div class="px-2">
            <span class="d-block h3 fw-bold mb-0 text-primary" id="days">00</span>
            <small class="text-muted">Hari</small>
          </div>
          <div class="h3 px-1 text-muted">:</div>
          <div class="px-2">
            <span class="d-block h3 fw-bold mb-0 text-primary" id="hours">00</span>
            <small class="text-muted">Jam</small>
          </div>
          <div class="h3 px-1 text-muted">:</div>
          <div class="px-2">
            <span class="d-block h3 fw-bold mb-0 text-primary" id="minutes">00</span>
            <small class="text-muted">Minit</small>
          </div>
          <div class="h3 px-1 text-muted">:</div>
          <div class="px-2">
            <span class="d-block h3 fw-bold mb-0 text-primary" id="seconds">00</span>
            <small class="text-muted">Saat</small>
          </div>
        </div>
      </div>
      <div>
        @auth
          <a href="{{ route('student.dashboard') }}" class="btn btn-warning btn-lg fw-bold px-4 shadow">Ke Portal Pelajar</a>
        @else
          <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-4 shadow">Daftar Akaun Sekarang</a>
          <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 ms-2">Log Masuk Portal</a>
        @endauth
      </div>
    </div>
  </div>

  <!-- Course Listings Section -->
  <div class="container py-5">
    <div class="text-center mb-5">
      <h2 class="fw-bold mb-2">Pilih Program Ikhtisas Anda</h2>
      <p class="text-muted">Sila teliti profil kursus di bawah sebelum membuat permohonan</p>
    </div>

    <!-- Course Tabs -->
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="list-group shadow-sm" id="list-tab" role="tablist">
          @foreach($courses as $index => $course)
            <a class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center {{ $index === 0 ? 'active' : '' }}" 
               id="list-{{ $course->code }}-list" 
               data-bs-toggle="list" 
               href="#list-{{ $course->code }}" 
               role="tab">
              <div>
                <strong class="d-block">{{ $course->code }}</strong>
                <small class="text-muted">{{ $course->name }}</small>
              </div>
              <span class="badge bg-secondary rounded-pill">{{ $course->total_modules ? $course->total_modules . ' Modul' : 'Khatam' }}</span>
            </a>
          @endforeach
        </div>
      </div>

      <div class="col-md-8">
        <div class="tab-content shadow-sm p-4 bg-white rounded-3" id="nav-tabContent">
          @foreach($courses as $index => $course)
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                 id="list-{{ $course->code }}" 
                 role="tabpanel" 
                 aria-labelledby="list-{{ $course->code }}-list">
              
              <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
                <div>
                  <span class="badge bg-primary mb-2">{{ $course->code }}</span>
                  <h3 class="fw-bold mb-0">{{ $course->name }}</h3>
                </div>
                <div class="text-end">
                  @if($course->code == 'KIBLAT')
                    <span class="h4 text-success fw-bold d-block">RM1,000 / RM1,200</span>
                    <small class="text-muted">Mengikut Kategori Kelayakan</small>
                  @elseif($course->fee_per_module)
                    <span class="h4 text-success fw-bold d-block">RM{{ number_format($course->fee_per_module) }}</span>
                    <small class="text-muted">Per Modul</small>
                  @else
                    <span class="h4 text-secondary fw-bold d-block">Hubungi Guru</span>
                    <small class="text-muted">Penyertaan Percuma / Khas</small>
                  @endif
                </div>
              </div>

              <div class="mb-4">
                <h5>Penerangan Program</h5>
                <p class="text-muted">{{ $course->description }}</p>
              </div>

              <div class="row mb-4">
                <div class="col-sm-6 mb-3">
                  <div class="p-3 bg-light rounded-3">
                    <strong class="d-block text-uppercase small text-muted">Tempoh / Modul</strong>
                    <span>{{ $course->duration_per_module ?? 'Fleksibel' }}</span>
                  </div>
                </div>
                <div class="col-sm-6 mb-3">
                  <div class="p-3 bg-light rounded-3">
                    <strong class="d-block text-uppercase small text-muted">Jadual Pengajian</strong>
                    <span>{{ $course->schedule_day_time ?? 'Berdasarkan slot temujanji' }}</span>
                  </div>
                </div>
              </div>

              @if($course->code == 'KIBLAT')
                <div class="alert alert-info border-0 rounded-3 mb-4">
                  <h6 class="alert-heading fw-bold mb-1">Perbezaan Yuran Kursus KIBLAT</h6>
                  <p class="mb-0 small">
                    <strong>Kategori A (RM1,000):</strong> Terbuka kepada pelajar yang mempunyai latar belakang ilmu falak.<br>
                    <strong>Kategori B (RM1,200):</strong> Terbuka kepada pelajar tanpa latar belakang ilmu falak (termasuk modul asas).
                  </p>
                </div>
              @endif

              <div class="mb-4">
                <h5>Syarat Kelayakan</h5>
                <p class="text-muted"><i class="ti ti-check text-success me-2"></i>{{ $course->requirements }}</p>
              </div>

              <!-- FAQ Section for Course -->
              <div class="mb-4 border-top pt-4">
                <h5 class="mb-3">Soalan Lazim (FAQ)</h5>
                <div class="accordion" id="faqAccordion-{{ $course->code }}">
                  <div class="accordion-item border-0 shadow-none bg-light mb-2 rounded-3">
                    <h2 class="accordion-header" id="headingOne-{{ $course->code }}">
                      <button class="accordion-button collapsed bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-{{ $course->code }}">
                        Adakah peperiksaan diadakan bagi setiap modul?
                      </button>
                    </h2>
                    <div id="collapseOne-{{ $course->code }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion-{{ $course->code }}">
                      <div class="accordion-body text-muted pt-0 small">
                        Ya. Bagi kursus biasa, peperiksaan akhir atau penilaian prestasi akan dijalankan di akhir setiap modul untuk membolehkan anda naik ke modul seterusnya. Markah lulus minimum ialah 40.
                      </div>
                    </div>
                  </div>
                  <div class="accordion-item border-0 shadow-none bg-light mb-2 rounded-3">
                    <h2 class="accordion-header" id="headingTwo-{{ $course->code }}">
                      <button class="accordion-button collapsed bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo-{{ $course->code }}">
                        Bagaimanakah sistem pembayaran yuran diuruskan?
                      </button>
                    </h2>
                    <div id="collapseTwo-{{ $course->code }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion-{{ $course->code }}">
                      <div class="accordion-body text-muted pt-0 small">
                        Yuran dibayar berasingan bagi setiap modul secara atas talian menerusi FPX/Kad Kredit dalam sistem sebelum modul pengajian bermula.
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-grid mt-4">
                @auth
                  <a href="{{ route('student.apply', ['course_id' => $course->id]) }}" class="btn btn-primary btn-lg shadow-sm">Daftar Sekarang <i class="ti ti-arrow-right ms-2"></i></a>
                @else
                  <a href="{{ route('register') }}?course={{ $course->id }}" class="btn btn-primary btn-lg shadow-sm">Daftar Akaun & Mohon <i class="ti ti-arrow-right ms-2"></i></a>
                @endauth
              </div>

            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <!-- WhatsApp Float Button -->
  <a href="https://wa.me/60186602715?text=Assalamualaikum%20SISPAQ%20APIUM,%20saya%20ingin%20bertanya%20mengenai%20program%20ikhtisas" 
     target="_blank" 
     class="position-fixed d-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-lg text-decoration-none" 
     style="bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 9999; transition: transform 0.3s;"
     onmouseover="this.style.transform='scale(1.1)'" 
     onmouseout="this.style.transform='scale(1)'">
     <!-- Simple Whatsapp SVG Icon -->
     <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
       <path d="M13.601 2.326A7.854 7.854 0 0 0 8 0a7.86 7.86 0 0 0-7.304 4.869 7.86 7.86 0 0 0 1.258 8.136L.25 16l3.111-1.024a7.861 7.861 0 0 0 3.738.93h.003c4.337 0 7.863-3.527 7.863-7.863a7.854 7.854 0 0 0-2.364-5.717zm-4.72 11.518a6.608 6.608 0 0 1-3.393-.94l-.243-.144-1.897.625.636-1.848-.157-.25a6.593 6.593 0 0 1-1.007-3.505c0-3.626 2.956-6.582 6.582-6.582 1.758 0 3.411.684 4.65 1.925a6.584 6.584 0 0 1 1.925 4.65c0 3.626-2.956 6.582-6.582 6.582zm3.56-4.914c-.196-.098-1.162-.574-1.337-.639-.175-.065-.302-.098-.43.098-.127.197-.492.639-.603.766-.111.128-.223.144-.419.046-.2-.098-.844-.312-1.607-.994-.594-.53-1.002-1.185-1.118-1.383-.117-.197-.012-.303.086-.401.088-.088.196-.23.296-.346.1-.115.133-.197.2-.329.066-.131.033-.246-.017-.346-.05-.1-.43-1.036-.59-1.424-.155-.373-.326-.322-.447-.322-.116-.002-.25-.002-.383-.002a.74.74 0 0 0-.53.25c-.183.2-.697.68-6.97 1.657s.51 1.916.58 2.014c.071.098 1.004 1.532 2.43 2.152.34.148.604.237.808.302.342.109.654.093.9.056.275-.041.834-.341 1.025-.87.191-.529.191-.983.134-1.08-.057-.098-.207-.162-.403-.26z"/>
     </svg>
  </a>

  <!-- Countdown JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const countdownDate = new Date("{{ $closeDate }}").getTime();

      const timer = setInterval(function () {
        const now = new Date().getTime();
        const distance = countdownDate - now;

        if (distance < 0) {
          clearInterval(timer);
          document.getElementById("days").innerText = "00";
          document.getElementById("hours").innerText = "00";
          document.getElementById("minutes").innerText = "00";
          document.getElementById("seconds").innerText = "00";
          return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("days").innerText = String(days).padStart(2, '0');
        document.getElementById("hours").innerText = String(hours).padStart(2, '0');
        document.getElementById("minutes").innerText = String(minutes).padStart(2, '0');
        document.getElementById("seconds").innerText = String(seconds).padStart(2, '0');
      }, 1000);
    });
  </script>
</div>
