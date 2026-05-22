<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'nim',
    'major',
    'faculty',
    'admission_year',
    'gpa',
    'status',
    'certificate_status',
    'certificate_number',
    'graduation_date',
    'degree',
    'photo'
])]
class Student extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gpa' => 'float',
            'admission_year' => 'integer',
            'graduation_date' => 'date',
        ];
    }

    /**
     * Relasi ke model User (Setiap mahasiswa memiliki satu User akun).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk mendapatkan predikat kelulusan berdasarkan IPK.
     */
    public function getPredicateAttribute(): string
    {
        if ($this->gpa >= 3.51) {
            return 'Dengan Pujian (Cum Laude)';
        } elseif ($this->gpa >= 3.01) {
            return 'Sangat Memuaskan';
        } elseif ($this->gpa >= 2.76) {
            return 'Memuaskan';
        }
        return 'Cukup';
    }
}
