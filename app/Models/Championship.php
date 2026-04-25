<?php

namespace App\Models;

use App\Enums\ChampionLevel;
use App\Enums\ChampionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    /** @use HasFactory<\Database\Factories\ChampionshipFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'year',
        'level',
        'position',
        'type',
    ];

    protected $casts = [
        'level' => ChampionLevel::class,
        'type' => ChampionType::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
