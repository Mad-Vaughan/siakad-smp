<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentClassroom;

$oldYear = AcademicYear::where('name', '2024/2025')->first();
if (! $oldYear) {
    echo "oldYear missing\n";
    exit(0);
}

$student = Student::where('name', 'Siswa Histori')->first();
if (! $student) {
    echo "student missing\n";
    exit(0);
}

echo 'student:' . $student->id . "\n";
$assignments = StudentClassroom::where('student_id', $student->id)->with('classroom')->get();
foreach ($assignments as $assignment) {
    echo 'assignment:' . $assignment->id . ' active:' . ($assignment->is_active ? 1 : 0) . ' class:' . ($assignment->classroom?->name ?? '-') . ' year:' . ($assignment->classroom?->academic_year_id ?? '-') . "\n";
}
