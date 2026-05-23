<?php

namespace App\Support;

class CourseProgression
{
    public static function suggestions(): array
    {
        return [
            'KPAQ' => ['KuTAB', 'KPAH'],
            'KPAH' => ['KuTAB'],
            'KBA' => ['KPI'],
            'Pra KuTAB' => ['KuTAB'],
            'KPI' => ['KIBLAT'],
        ];
    }

    public static function forCourseCode(?string $code): array
    {
        if (! $code) {
            return [];
        }

        $codes = self::suggestions()[$code] ?? [];

        return \App\Models\Course::whereIn('code', $codes)->get()->all();
    }
}
