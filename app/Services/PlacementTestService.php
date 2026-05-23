<?php

namespace App\Services;

use App\Models\Course;
use App\Models\PlacementTest;
use App\Models\StudentApplication;

class PlacementTestService
{
    public static function suggestRecommendedCourse(int $readingScore, ?string $appliedCourseCode): ?int
    {
        if ($readingScore >= 70) {
            return Course::where('code', 'KuTAB')->value('id');
        }

        if (in_array($appliedCourseCode, ['KuTAB', 'Pra KuTAB'], true)) {
            return Course::where('code', 'Pra KuTAB')->value('id');
        }

        return null;
    }

    public static function completeTest(
        PlacementTest $test,
        int $readingScore,
        string $notes,
        ?int $recommendedCourseId = null
    ): void {
        $application = $test->application;
        $recommendedCourseId = $recommendedCourseId
            ?? self::suggestRecommendedCourse($readingScore, $application->course->code);

        $test->update([
            'reading_score' => $readingScore,
            'notes' => $notes,
            'recommended_course_id' => $recommendedCourseId,
            'status' => 'completed',
        ]);

        $application->update(['status' => 'offered']);
    }

    public static function placementRequired(StudentApplication $application): bool
    {
        return $application->course->requiresPlacementTest();
    }
}
