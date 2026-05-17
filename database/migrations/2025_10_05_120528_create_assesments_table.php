<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assesments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Classroom::class);
            $table->foreignIdFor(\App\Models\Subject::class);
            $table->foreignIdFor(\App\Models\AcademicYear::class);

            // 👇 INI DIA PENYELAMAT SKRIPSI LU JON! 👇
            $table->string('name')->nullable(); // Buat nyimpen "Tugas 1", "UTS", dll
            $table->date('date')->nullable(); // Buat nyimpen tanggal ujiannya
            // 👆👆👆

            $table->string('type', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assesments'); // Note: lu kemaren nulisnya 'assessments' pake double 's', gue samain biar aman.
    }
};
