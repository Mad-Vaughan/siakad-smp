<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function loginAsAdmin()
{
    $admin = User::factory()->isAdmin()->create();
    test()->actingAs($admin);
}

function enableIconFallback(): void
{
    config()->set('blade-icons.fallback', 'heroicon-o-document-text');
}

function createActiveAcademicYear(array $attributes = []): \App\Models\AcademicYear
{
    return \App\Models\AcademicYear::factory()
        ->state(array_merge(['is_active' => true], $attributes))
        ->create();
}

function createClassroomWithStudents(int $studentCount = 3): \App\Models\Classroom
{
    $academicYear = createActiveAcademicYear();

    $classroom = \App\Models\Classroom::factory()
        ->for($academicYear, 'academicYear')
        ->create();

    $students = \App\Models\Student::factory()->count($studentCount)->create();

    $students->each(function (\App\Models\Student $student, int $index) use ($classroom): void {
        \App\Models\StudentClassroom::factory()
            ->for($student, 'student')
            ->for($classroom, 'classroom')
            ->state(['is_active' => $index === 0])
            ->create();
    });

    return $classroom->refresh();
}

function createPresenceForClassroom(\App\Models\Classroom $classroom, array $attributes = []): \App\Models\Presence
{
    return \App\Models\Presence::create([
        'classroom_id' => $classroom->id,
        'date' => now()->toDateString(),
        ...$attributes,
    ]);
}

function something()
{
    // ..
}
