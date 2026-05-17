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
        // Cek dulu, kalo schedule_id belom ada, baru kita bikinin
        if (! Schema::hasColumn('presences', 'schedule_id')) {
            Schema::table('presences', function (Blueprint $table) {
                $table->foreignId('schedule_id')->nullable()->constrained()->cascadeOnDelete();
            });
        }

        // Cek dulu, kalo type belom ada, baru kita bikinin (biar kaga duplicate error lagi)
        if (! Schema::hasColumn('presences', 'type')) {
            Schema::table('presences', function (Blueprint $table) {
                $table->enum('type', ['harian', 'mapel'])->default('harian');
            });
        }
    }

    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            if (Schema::hasColumn('presences', 'schedule_id')) {
                $table->dropForeign(['schedule_id']);
                $table->dropColumn('schedule_id');
            }

            if (Schema::hasColumn('presences', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
