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
        // Cek dulu, kalau kolom 'semester' BELUM ADA, baru tambahin
        if (! Schema::hasColumn('academic_years', 'semester')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->enum('semester', ['ganjil', 'genap'])->default('ganjil')->after('name');
            });
        }

        // Cek tabel 'users' (Ingat, tadi Student/Teacher nyatunya di sini)
        Schema::table('users', function (Blueprint $table) {
            // Tambahin gender kalau belum ada
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
            if (! Schema::hasColumn('users', 'entry_date')) {
                $table->date('entry_date')->nullable();
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
        });

        // Bagian Mata Pelajaran
        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'teacher_id')) {
                $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (! Schema::hasColumn('subjects', 'classroom_id')) {
                $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }
};
