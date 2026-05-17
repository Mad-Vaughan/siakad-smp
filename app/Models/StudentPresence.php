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
        'note', // Tambahin note sekalian biar aman pas di-fill
    ];

    protected $casts = [
        'status' => PresenceStatus::class,
    ];

    // 👇 REVISI ACCESSOR: Biar kaga tabrakan sama Enum asli pas nge-set data 👇
    protected function hadir(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::PRESENT,
        );
    }

    protected function sakit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::SICK,
        );
    }

    protected function izin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::PERMISSION,
        );
    }

    protected function terlambat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::LATE,
        );
    }

    protected function alpa(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === PresenceStatus::ABSENT,
        );
    }

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id'); // Pake User/Student biar relasinya bener
    }
}
