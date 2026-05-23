<?php

namespace App\Support;

use App\Models\Course;
use App\Models\StudentEnrollment;
use Illuminate\Contracts\Auth\Authenticatable;

class SispaqMenu
{
    public static function talaqqiCourseCodes(): array
    {
        return ['Pra KuTAB', 'KuTAB', 'KTKT'];
    }

    public static function userHasTalaqqiEnrollment(?Authenticatable $user): bool
    {
        if (! $user) {
            return false;
        }

        return StudentEnrollment::query()
            ->where('student_id', $user->getAuthIdentifier())
            ->where('status', 'active')
            ->whereHas('course', fn ($q) => $q->whereIn('code', self::talaqqiCourseCodes()))
            ->exists();
    }

    public static function isVisible(?string $visibleWhen, ?Authenticatable $user): bool
    {
        if (! $visibleWhen) {
            return true;
        }

        return match ($visibleWhen) {
            'has_talaqqi_enrollment' => self::userHasTalaqqiEnrollment($user),
            'guest' => $user === null,
            'auth' => $user !== null,
            default => true,
        };
    }

    public static function isActive(array $item): bool
    {
        $patterns = $item['active'] ?? [];

        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    public static function itemsForRole(?string $role): array
    {
        $menus = config('sispaq-menu', []);

        $items = ($role && isset($menus[$role])) ? $menus[$role] : ($menus['guest'] ?? []);

        return array_map(function (array $item) {
            if (isset($item['label'])) {
                $item['label'] = __('sispaq.' . $item['label']);
            }

            return $item;
        }, $items);
    }

    /** Profile URL for navbar dropdown (not shown in sidebar menu). */
    public static function profileUrlFor(?Authenticatable $user): ?string
    {
        if (! $user || ! isset($user->role)) {
            return null;
        }

        return match ($user->role) {
            'student' => route('student.profile'),
            default => null,
        };
    }

    public static function paymentsUrlFor(?Authenticatable $user): ?string
    {
        if (! $user || ($user->role ?? null) !== 'student') {
            return null;
        }

        return route('student.profile', ['tab' => 'bayaran']);
    }
}
