<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'code' => 'KPAQ',
                'name' => 'Pengajian Al-Quran',
                'total_modules' => 5,
                'fee_per_module' => 600.00,
                'duration_per_module' => '4 bulan',
                'schedule_day_time' => 'Sabtu 9-11 pagi',
                'description' => 'Program pengajian Al-Quran mendalam merangkumi tajwid, tafsir asas, dan tadabbur.',
                'requirements' => 'Terbuka kepada semua warganegara Malaysia dengan asas pembacaan Al-Quran.',
            ],
            [
                'code' => 'KPAH',
                'name' => 'Pengajian Al-Hadith',
                'total_modules' => 4,
                'fee_per_module' => 600.00,
                'duration_per_module' => '4 bulan',
                'schedule_day_time' => 'Sabtu 9-11 pagi',
                'description' => 'Siri pengajian hadis-hadis hukum dan mustalah hadis bersama pensyarah berkelayakan.',
                'requirements' => 'Terbuka kepada semua warganegara Malaysia.',
            ],
            [
                'code' => 'KBA',
                'name' => 'Bahasa Arab',
                'total_modules' => 3,
                'fee_per_module' => 900.00,
                'duration_per_module' => '6 bulan',
                'schedule_day_time' => 'Sabtu 9-1:30 petang',
                'description' => 'Pembelajaran bahasa Arab secara komprehensif (Nahu, Saraf, Balaghah, dan perbualan).',
                'requirements' => 'Terbuka kepada semua warganegara Malaysia (tanpa syarat kelayakan khusus).',
            ],
            [
                'code' => 'Pra KuTAB',
                'name' => 'Pra Talaqqi Bersanad',
                'total_modules' => 1,
                'fee_per_module' => 600.00,
                'duration_per_module' => '4 bulan',
                'schedule_day_time' => 'Talaqqi 15 minit (Slot Individu)',
                'description' => 'Persediaan awal sebelum memasuki Talaqqi Bersanad bagi memantapkan kelancaran bacaan Al-Quran.',
                'requirements' => 'Terbuka kepada pelajar yang ingin memperkemas bacaan sebelum talaqqi bersanad.',
            ],
            [
                'code' => 'KuTAB',
                'name' => 'Talaqqi Bersanad',
                'total_modules' => 12,
                'fee_per_module' => 600.00,
                'duration_per_module' => '4 bulan',
                'schedule_day_time' => 'Talaqqi 15 minit (Slot Individu)',
                'description' => 'Pembacaan 30 juzuk Al-Quran secara bertalaqqi dari muka surat 1 hingga 604 bagi mendapatkan ijazah sanad bertulis.',
                'requirements' => 'Memerlukan keupayaan membaca Al-Quran dengan baik dan memahami asas tajwid (melalui ujian penempatan).',
            ],
            [
                'code' => 'KPI',
                'name' => 'Pengubatan Islam',
                'total_modules' => 4,
                'fee_per_module' => 600.00,
                'duration_per_module' => '4 bulan',
                'schedule_day_time' => 'Sabtu 2:30-4:30 petang',
                'description' => 'Modul pengajian perubatan dan rawatan Islam berasaskan Al-Quran, Sunnah, dan amalan salafussoleh.',
                'requirements' => 'Terbuka kepada semua warganegara Malaysia.',
            ],
            [
                'code' => 'KIBLAT',
                'name' => 'Sijil Kemahiran Jurufalak',
                'total_modules' => 6,
                'fee_per_module' => null,
                'duration_per_module' => '4 bulan',
                'schedule_day_time' => 'Hujung Minggu / Fleksibel',
                'description' => 'Sijil kemahiran teknikal falak bagi menentukan arah kiblat, waktu solat, dan pencerapan anak bulan.',
                'requirements' => 'Kategori A: Dengan latar belakang falak / Kategori B: Tanpa latar belakang falak.',
                'kiblat_cat_a_fee' => 1000.00,
                'kiblat_cat_b_fee' => 1200.00,
            ],
            [
                'code' => 'KTKT',
                'name' => 'Talaqqi Kitab Turath',
                'total_modules' => 4,
                'fee_per_module' => 500.00,
                'duration_per_module' => '4 bulan',
                'schedule_day_time' => 'Talaqqi 15 minit (Slot Individu)',
                'description' => 'Talaqqi pembacaan kitab-kitab klasik turath (Mazhab Syafi\'i) bersama syeikh/guru yang bersanad.',
                'requirements' => 'Mempunyai asas bahasa Arab atau kebolehan membaca kitab jawi/arab gundul.',
            ]
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], $course);
        }
    }
}
