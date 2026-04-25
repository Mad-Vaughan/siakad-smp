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
        Schema::create('championships', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Student::class);
            $table->string('name', 100)->comment('Name of the championship');
            $table->year('year')->comment('Year the championship took place');
            $table->string('level', 10)->comment('e.g., school, district, provincial, national, international');
            $table->string('position', 50)->comment('e.g., first place, second place, third place, participant');
            $table->string('type', 10)->comment('e.g., individual, team');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('championships');
    }
};
