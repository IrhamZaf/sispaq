<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\StudentApplication;
use App\Models\StudentEnrollment;
use App\Models\Payment;
use App\Models\ClassSchedule;
use App\Models\Attendance;
use App\Models\AcademicRecord;
use App\Models\Certificate;
use App\Models\TalaqqiBooking;
use App\Models\PlacementTest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed courses
        $this->call(CourseSeeder::class);

        // Fetch courses for reference
        $kpaq = Course::where('code', 'KPAQ')->first();
        $kpah = Course::where('code', 'KPAH')->first();
        $kba = Course::where('code', 'KBA')->first();
        $kutab = Course::where('code', 'KuTAB')->first();
        $kiblat = Course::where('code', 'KIBLAT')->first();

        // 2. Seed default users
        $admin = User::updateOrCreate(
            ['email' => 'admin@sispaq.com'],
            [
                'name' => 'Pengurus SISPAQ',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '012-345 6789',
                'ic_number' => '850101-14-1234',
                'address' => 'Akademi Pengajian Islam, Universiti Malaya, Kuala Lumpur',
            ]
        );

        $teacher = User::updateOrCreate(
            ['email' => 'ustaz@sispaq.com'],
            [
                'name' => 'Ustaz Ahmad Bin Yusuf',
                'password' => bcrypt('password'),
                'role' => 'teacher',
                'phone' => '013-987 6543',
                'ic_number' => '780202-03-5678',
                'address' => 'No. 12, Jalan Universiti, Seksyen 12, Petaling Jaya, Selangor',
            ]
        );

        $kpaq->update(['teacher_id' => $teacher->id]);
        $kpah->update(['teacher_id' => $teacher->id]);
        $kba->update(['teacher_id' => $teacher->id]);

        $student1 = User::updateOrCreate(
            ['email' => 'pelajar@sispaq.com'],
            [
                'name' => 'Ahmad Bin Ibrahim',
                'password' => bcrypt('password'),
                'role' => 'student',
                'phone' => '018-999 8888',
                'ic_number' => '990303-10-9876',
                'address' => 'Lot 45, Kampung Baru, Kuala Lumpur',
            ]
        );

        $student2 = User::updateOrCreate(
            ['email' => 'pelajar2@sispaq.com'],
            [
                'name' => 'Muhammad Ali Bin Osman',
                'password' => bcrypt('password'),
                'role' => 'student',
                'phone' => '011-222 3333',
                'ic_number' => '980404-14-5555',
                'address' => 'Taman Tun Dr Ismail, Kuala Lumpur',
            ]
        );

        $student3 = User::updateOrCreate(
            ['email' => 'pelajar3@sispaq.com'],
            [
                'name' => 'Fatimah Binti Harun',
                'password' => bcrypt('password'),
                'role' => 'student',
                'phone' => '017-333 4444',
                'ic_number' => '010505-10-6666',
                'address' => 'Flat Sentul Utama, Kuala Lumpur',
            ]
        );

        $student4 = User::updateOrCreate(
            ['email' => 'pelajar4@sispaq.com'],
            [
                'name' => 'Aisyah Binti Zulkifli',
                'password' => bcrypt('password'),
                'role' => 'student',
                'phone' => '019-444 5555',
                'ic_number' => '000606-08-7777',
                'address' => 'Gombak, Kuala Lumpur',
            ]
        );

        // 3. Seed student applications and enrollments
        
        // Student 1 (Ahmad):
        // Application 1: KPAQ -> Approved & Enrolled
        $app1 = StudentApplication::create([
            'user_id' => $student1->id,
            'course_id' => $kpaq->id,
            'reading_level' => 'Sederhana',
            'status' => 'approved',
            'auto_eligible' => true,
        ]);
        
        $enroll1 = StudentEnrollment::create([
            'student_id' => $student1->id,
            'course_id' => $kpaq->id,
            'current_module' => 2,
            'status' => 'active',
        ]);

        // Application 2: KBA -> Completed
        $app2 = StudentApplication::create([
            'user_id' => $student1->id,
            'course_id' => $kba->id,
            'status' => 'approved',
            'auto_eligible' => true,
        ]);

        $enroll2 = StudentEnrollment::create([
            'student_id' => $student1->id,
            'course_id' => $kba->id,
            'current_module' => 3,
            'status' => 'completed',
        ]);

        // Application 3: KuTAB -> Approved & Enrolled (Talaqqi)
        $app3 = StudentApplication::create([
            'user_id' => $student1->id,
            'course_id' => $kutab->id,
            'reading_level' => 'Lancar',
            'status' => 'approved',
            'auto_eligible' => false,
        ]);

        $enroll3 = StudentEnrollment::create([
            'student_id' => $student1->id,
            'course_id' => $kutab->id,
            'current_module' => 1,
            'status' => 'active',
        ]);

        // Student 2 (Muhammad Ali):
        // Application: KPAH -> Approved & Enrolled (With poor attendance to demo warnings)
        $app4 = StudentApplication::create([
            'user_id' => $student2->id,
            'course_id' => $kpah->id,
            'status' => 'approved',
            'auto_eligible' => true,
        ]);

        $enroll4 = StudentEnrollment::create([
            'student_id' => $student2->id,
            'course_id' => $kpah->id,
            'current_module' => 1,
            'status' => 'active',
        ]);

        // Student 3 (Fatimah):
        // Application: KuTAB -> Scheduled Placement Test
        $app5 = StudentApplication::create([
            'user_id' => $student3->id,
            'course_id' => $kutab->id,
            'reading_level' => 'Merangkak',
            'status' => 'test_scheduled',
            'auto_eligible' => false,
        ]);

        PlacementTest::create([
            'application_id' => $app5->id,
            'student_id' => $student3->id,
            'examiner_id' => $teacher->id,
            'test_date_time' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0)->setSecond(0),
            'status' => 'scheduled',
        ]);

        // Student 4 (Aisyah):
        // Application: KIBLAT Category B -> Under Review
        $app6 = StudentApplication::create([
            'user_id' => $student4->id,
            'course_id' => $kiblat->id,
            'kiblat_category' => 'B',
            'status' => 'under_review',
            'auto_eligible' => false,
        ]);


        // 4. Seed Payments
        // Student 1 (Ahmad) Payments:
        // KBA module 1, 2, 3 (paid)
        for ($m = 1; $m <= 3; $m++) {
            Payment::create([
                'enrollment_id' => $enroll2->id,
                'student_id' => $student1->id,
                'course_id' => $kba->id,
                'module_number' => $m,
                'amount' => 900.00,
                'payment_method' => 'fpx',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN-KBA-' . $m . '-' . rand(10000, 99999),
                'receipt_number' => 'REC-KBA-2025-' . sprintf('%04d', rand(1, 999)),
                'paid_at' => Carbon::now()->subMonths(6 - $m),
            ]);
        }

        // KPAQ module 1, 2 (paid)
        Payment::create([
            'enrollment_id' => $enroll1->id,
            'student_id' => $student1->id,
            'course_id' => $kpaq->id,
            'module_number' => 1,
            'amount' => 600.00,
            'payment_method' => 'fpx',
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-KPAQ-1-' . rand(10000, 99999),
            'receipt_number' => 'REC-KPAQ-2026-0012',
            'paid_at' => Carbon::now()->subMonths(3),
        ]);

        Payment::create([
            'enrollment_id' => $enroll1->id,
            'student_id' => $student1->id,
            'course_id' => $kpaq->id,
            'module_number' => 2,
            'amount' => 600.00,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-KPAQ-2-' . rand(10000, 99999),
            'receipt_number' => 'REC-KPAQ-2026-0089',
            'paid_at' => Carbon::now()->subDays(5),
        ]);

        // KuTAB module 1 (paid)
        Payment::create([
            'enrollment_id' => $enroll3->id,
            'student_id' => $student1->id,
            'course_id' => $kutab->id,
            'module_number' => 1,
            'amount' => 600.00,
            'payment_method' => 'fpx',
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-KUTAB-1-' . rand(10000, 99999),
            'receipt_number' => 'REC-KUTAB-2026-0044',
            'paid_at' => Carbon::now()->subDays(10),
        ]);

        // Student 2 (Muhammad Ali) Payments:
        // KPAH module 1 (paid)
        Payment::create([
            'enrollment_id' => $enroll4->id,
            'student_id' => $student2->id,
            'course_id' => $kpah->id,
            'module_number' => 1,
            'amount' => 600.00,
            'payment_method' => 'fpx',
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-KPAH-1-' . rand(10000, 99999),
            'receipt_number' => 'REC-KPAH-2026-0051',
            'paid_at' => Carbon::now()->subMonths(1),
        ]);


        // 5. Seed Class Schedules & Attendances
        
        // KPAQ Schedules and Attendances (Ahmad)
        $kpaqSchedules = [];
        for ($i = 1; $i <= 4; $i++) {
            $kpaqSchedules[] = ClassSchedule::create([
                'course_id' => $kpaq->id,
                'module_number' => 2,
                'schedule_datetime' => Carbon::now()->subWeeks(5 - $i)->setHour(9)->setMinute(0),
                'location' => 'Bilik Al-Ghazali, APIUM',
                'is_hybrid' => $i % 2 === 0,
            ]);
        }

        // Ahmad attended all KPAQ classes
        foreach ($kpaqSchedules as $sched) {
            Attendance::create([
                'enrollment_id' => $enroll1->id,
                'class_schedule_id' => $sched->id,
                'status' => 'hadir',
                'recorded_at' => $sched->schedule_datetime,
            ]);
        }

        // KPAH Schedules and Attendances (Muhammad Ali)
        $kpahSchedules = [];
        for ($i = 1; $i <= 4; $i++) {
            $kpahSchedules[] = ClassSchedule::create([
                'course_id' => $kpah->id,
                'module_number' => 1,
                'schedule_datetime' => Carbon::now()->subWeeks(5 - $i)->setHour(9)->setMinute(0),
                'location' => 'Bilik Al-Bukhari, APIUM',
                'is_hybrid' => false,
            ]);
        }

        // Muhammad Ali has low attendance: absent in 3 out of 4 (25% attendance rate)
        foreach ($kpahSchedules as $idx => $sched) {
            Attendance::create([
                'enrollment_id' => $enroll4->id,
                'class_schedule_id' => $sched->id,
                'status' => ($idx === 2) ? 'hadir' : 'tidak_hadir',
                'recorded_at' => $sched->schedule_datetime,
            ]);
        }


        // 6. Seed Academic Records & Certificate
        
        // Student 1 KBA final marks (Module 1, 2, 3)
        AcademicRecord::create([
            'enrollment_id' => $enroll2->id,
            'module_number' => 1,
            'marks' => 82,
            'status' => 'lulus',
            'recorded_by' => $teacher->id,
        ]);
        AcademicRecord::create([
            'enrollment_id' => $enroll2->id,
            'module_number' => 2,
            'marks' => 75,
            'status' => 'lulus',
            'recorded_by' => $teacher->id,
        ]);
        AcademicRecord::create([
            'enrollment_id' => $enroll2->id,
            'module_number' => 3,
            'marks' => 88,
            'status' => 'lulus',
            'recorded_by' => $teacher->id,
        ]);

        // Issue completed certificate for KBA
        Certificate::create([
            'student_id' => $student1->id,
            'course_id' => $kba->id,
            'certificate_type' => 'lulus',
            'certificate_number' => 'CERT-KBA-2026-8877',
            'issue_date' => Carbon::now()->subMonths(1)->toDateString(),
            'qr_code_hash' => md5('CERT-KBA-2026-8877-' . $student1->id),
            'pdf_path' => null,
        ]);

        // KPAQ module 1 record for Ahmad
        AcademicRecord::create([
            'enrollment_id' => $enroll1->id,
            'module_number' => 1,
            'marks' => 90,
            'status' => 'lulus',
            'recorded_by' => $teacher->id,
        ]);


        // 7. Seed Talaqqi Bookings (Ahmad in KuTAB)
        $talaqqiDate1 = Carbon::now()->subWeeks(2)->setHour(10)->setMinute(0);
        $tb1 = TalaqqiBooking::create([
            'enrollment_id' => $enroll3->id,
            'student_id' => $student1->id,
            'teacher_id' => $teacher->id,
            'booking_datetime' => $talaqqiDate1,
            'current_page' => 15,
            'session_status' => 'Lulus',
            'notes' => 'Tajwid makhraj huruf hijaiyah amat baik. Teruskan.',
        ]);
        Attendance::create([
            'enrollment_id' => $enroll3->id,
            'talaqqi_booking_id' => $tb1->id,
            'status' => 'hadir',
            'recorded_at' => $talaqqiDate1,
        ]);

        $talaqqiDate2 = Carbon::now()->subWeeks(1)->setHour(10)->setMinute(15);
        $tb2 = TalaqqiBooking::create([
            'enrollment_id' => $enroll3->id,
            'student_id' => $student1->id,
            'teacher_id' => $teacher->id,
            'booking_datetime' => $talaqqiDate2,
            'current_page' => 30,
            'session_status' => 'Lulus',
            'notes' => 'Kelancaran surah Al-Baqarah bertambah baik.',
        ]);
        Attendance::create([
            'enrollment_id' => $enroll3->id,
            'talaqqi_booking_id' => $tb2->id,
            'status' => 'hadir',
            'recorded_at' => $talaqqiDate2,
        ]);

        $talaqqiDate3 = Carbon::now()->addDays(2)->setHour(11)->setMinute(0);
        TalaqqiBooking::create([
            'enrollment_id' => $enroll3->id,
            'student_id' => $student1->id,
            'teacher_id' => $teacher->id,
            'booking_datetime' => $talaqqiDate3,
            'current_page' => null,
            'session_status' => 'Belum Selesai',
            'notes' => null,
        ]);
    }
}
