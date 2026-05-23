<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. KPAQ, KPAH, KBA, etc.
            $table->string('name');
            $table->integer('total_modules')->nullable();
            $table->decimal('fee_per_module', 10, 2)->nullable();
            $table->string('duration_per_module')->nullable();
            $table->string('schedule_day_time')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->decimal('kiblat_cat_a_fee', 10, 2)->nullable(); // Category A RM1000
            $table->decimal('kiblat_cat_b_fee', 10, 2)->nullable(); // Category B RM1200
            $table->timestamps();
        });

        // 2. student_applications
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('kiblat_category')->nullable(); // 'A' or 'B'
            $table->string('reading_level')->nullable(); // For Pra KuTAB / KuTAB
            $table->string('ic_document')->nullable();
            $table->string('supporting_documents')->nullable();
            $table->string('status')->default('pending'); // pending, under_review, test_scheduled, offered, approved, rejected
            $table->boolean('auto_eligible')->default(false);
            $table->timestamps();
        });

        // 3. placements_tests
        Schema::create('placements_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('student_applications')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('examiner_id')->nullable()->constrained('users')->onDelete('set null'); // teacher user
            $table->dateTime('test_date_time');
            $table->string('reading_score')->nullable(); // score or assessment details
            $table->text('notes')->nullable();
            $table->foreignId('recommended_course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->string('status')->default('scheduled'); // scheduled, completed, absent
            $table->timestamps();
        });

        // 4. student_enrollments
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->integer('current_module')->default(1);
            $table->string('status')->default('active'); // active, completed, dropped
            $table->timestamps();
        });

        // 5. payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->nullable()->constrained('student_enrollments')->onDelete('set null');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->integer('module_number');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // fpx, card, ewallet, manual
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('transaction_id')->nullable();
            $table->string('receipt_number')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        // 6. classes_and_schedules (For KPAQ, KPAH, KBA, KPI, KIBLAT)
        Schema::create('classes_and_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->integer('module_number');
            $table->dateTime('schedule_datetime');
            $table->string('location'); // Room name or Zoom URL
            $table->boolean('is_hybrid')->default(false);
            $table->timestamps();
        });

        // 7. talaqqi_bookings (For Pra KuTAB, KuTAB, KTKT)
        Schema::create('talaqqi_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('student_enrollments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('booking_datetime');
            $table->integer('current_page')->nullable(); // e.g. 1-604
            $table->string('session_status')->default('Belum Selesai'); // Lulus, Perlu Ulang, Belum Selesai
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. attendances
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('student_enrollments')->onDelete('cascade');
            $table->foreignId('class_schedule_id')->nullable()->constrained('classes_and_schedules')->onDelete('cascade');
            $table->foreignId('talaqqi_booking_id')->nullable()->constrained('talaqqi_bookings')->onDelete('cascade');
            $table->string('status')->default('tidak_hadir'); // hadir, tidak_hadir, lewat, bersebab
            $table->dateTime('recorded_at');
            $table->timestamps();
        });

        // 9. academic_records
        Schema::create('academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('student_enrollments')->onDelete('cascade');
            $table->integer('module_number');
            $table->integer('marks'); // 0 - 100
            $table->string('status'); // lulus, gagal
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null'); // teacher id
            $table->timestamps();
        });

        // 10. certificates
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('certificate_type'); // lulus, kehadiran, sanad, kemahiran
            $table->string('certificate_number')->unique();
            $table->date('issue_date');
            $table->string('qr_code_hash')->unique();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('academic_records');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('talaqqi_bookings');
        Schema::dropIfExists('classes_and_schedules');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('placements_tests');
        Schema::dropIfExists('student_applications');
        Schema::dropIfExists('courses');
    }
};
