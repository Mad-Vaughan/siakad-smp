<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Matematika',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Ilmu Pengetahuan Alam (IPA)',
            'Ilmu Pengetahuan Sosial (IPS)',
            'Pendidikan Agama',
            'Pendidikan Kewarganegaraan (PKN)',
            'Seni Budaya',
            'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)',
            'Teknologi Informasi dan Komunikasi (TIK)',
        ];

        foreach ($subjects as $subjectName) {
            \App\Models\Subject::updateOrCreate([
                'name' => $subjectName,
            ]);
        }
    }
}
