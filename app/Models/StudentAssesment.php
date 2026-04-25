<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssesment extends Model
{
    /** @use HasFactory<\Database\Factories\StudentAssesmentFactory> */
    use HasFactory;

    protected $guarded = [];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assesment::class, 'assesment_id');
    }
}
