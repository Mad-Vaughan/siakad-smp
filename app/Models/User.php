<?php

namespace App\Models;

use App\Enums\Gender;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'nisn',
        'date_of_birth',
        'gender',
        'address',
        'nip',
        'employment_status',
        'active_status',
        'nipd',
        'nik',
        'birth_place',
        'religion',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => Gender::class,
        ];
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function championships()
    {
        return $this->hasMany(Championship::class, 'student_id');
    }

    public function studentPresences()
    {
        return $this->hasMany(StudentPresence::class, 'student_id');
    }

    public function studentAssesments()
    {
        return $this->hasMany(StudentAssesment::class, 'student_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(['admin', 'teacher', 'tu']),
            'parent' => $this->hasRole('student'),
            default => false,
        };
    }
}
