<?php

namespace App\Models;

use App\Enums\PresenceStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPresence extends Model
{
    /** @use HasFactory<\Database\Factories\StudentPresenceFactory> */
    use HasFactory;

    protected $fillable = [
        'presence_id',
        'student_id',
        'status',
    ];

    protected $casts = [
        'status' => PresenceStatus::class,
    ];

    // Accessor untuk checkbox columns
    protected function hadir(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::PRESENT,
            set: fn ($value) => ['status' => $value ? PresenceStatus::PRESENT : null]
        );
    }

    protected function sakit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::SICK,
            set: fn ($value) => ['status' => $value ? PresenceStatus::SICK : null]
        );
    }

    protected function izin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::PERMISSION,
            set: fn ($value) => ['status' => $value ? PresenceStatus::PERMISSION : null]
        );
    }

    protected function terlambat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::LATE,
            set: fn ($value) => ['status' => $value ? PresenceStatus::LATE : null]
        );
    }

    protected function alpa(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::ABSENT,
            set: fn ($value) => ['status' => $value ? PresenceStatus::ABSENT : null]
        );
    }

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
