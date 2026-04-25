<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AssesmentType: string implements HasColor, HasIcon, HasLabel
{
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case EXAM = 'exam';
    case MIDTERM = 'midterm';
    case FINAL = 'final';
    case DAILY_TEST = 'daily_test';
    case PRACTICAL = 'practical';
    case PROJECT = 'project';

    public function getLabel(): string
    {
        return match ($this) {
            self::QUIZ => 'Kuis',
            self::ASSIGNMENT => 'Tugas',
            self::EXAM => 'Ujian',
            self::MIDTERM => 'Ujian Tengah Semester',
            self::FINAL => 'Ujian Akhir Semester',
            self::DAILY_TEST => 'Ulangan Harian',
            self::PRACTICAL => 'Praktikum',
            self::PROJECT => 'Proyek',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::QUIZ => 'heroicon-o-question-mark-circle',
            self::ASSIGNMENT => 'heroicon-o-clipboard',
            self::EXAM => 'heroicon-o-document-text',
            self::MIDTERM => 'heroicon-o-calendar',
            self::FINAL => 'heroicon-o-check-badge',
            self::DAILY_TEST => 'heroicon-o-pencil-square',
            self::PRACTICAL => 'heroicon-o-cog',
            self::PROJECT => 'heroicon-o-folder-open',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::QUIZ => 'primary',
            self::ASSIGNMENT => 'secondary',
            self::EXAM => 'warning',
            self::MIDTERM => 'info',
            self::FINAL => 'success',
            self::DAILY_TEST => 'accent',
            self::PRACTICAL => 'danger',
            self::PROJECT => 'gray',
        };
    }
}
