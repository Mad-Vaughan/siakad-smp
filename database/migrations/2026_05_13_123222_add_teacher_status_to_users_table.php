<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // NIP/NUPTK khusus Guru
            $table->string('nip')->nullable()->after('nisn');
            // PNS, PPPK, Honorer, GTY
            $table->string('employment_status')->nullable()->after('address');
            // Aktif, Cuti, Pensiun, Meninggal
            $table->string('active_status')->default('Aktif')->after('employment_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'employment_status', 'active_status']);
        });
    }
};
