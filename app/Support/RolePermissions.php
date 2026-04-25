<?php

namespace App\Support;

use Illuminate\Support\Str;

class RolePermissions
{
    /**
     * @return list<string>
     */
    public static function teacher(): array
    {
        $separator = config('filament-shield.permissions.separator', ':');
        $case = config('filament-shield.permissions.case', 'pascal');
        $methods = config('filament-shield.policies.methods', []);

        $subjects = [
            'Classroom',
            'StudentClassroom',
            'Student',
            'Assesment',
            'Championship',
        ];

        return collect($subjects)
            ->flatMap(
                fn (string $subject): array => collect($methods ?? [])
                    ->map(
                        fn (string $method): string => self::permissionName(
                            $method,
                            $subject,
                            $separator,
                            $case,
                        )
                    )
                    ->all()
            )
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function tu(): array
    {
        $separator = config('filament-shield.permissions.separator', ':');
        $case = config('filament-shield.permissions.case', 'pascal');
        $methods = config('filament-shield.policies.methods', []);

        $subjects = [
            'Student',
            'AcademicYear',
            'Presence',
        ];

        return collect($subjects)
            ->flatMap(
                fn (string $subject): array => collect($methods ?? [])
                    ->map(
                        fn (string $method): string => self::permissionName(
                            $method,
                            $subject,
                            $separator,
                            $case,
                        )
                    )
                    ->all()
            )
            ->values()
            ->all();
    }

    protected static function permissionName(string $method, string $subject, string $separator, string $case): string
    {
        return sprintf('%s%s%s', self::formatMethod($method, $case), $separator, $subject);
    }

    protected static function formatMethod(string $method, string $case): string
    {
        return match ($case) {
            'kebab' => Str::kebab($method),
            'camel' => Str::camel($method),
            'upper_snake' => Str::upper(Str::snake($method)),
            'lower_snake', 'snake' => Str::snake($method),
            default => Str::studly($method),
        };
    }
}
