<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintHistory extends Model
{
    protected $fillable = [
        'student_id',
        'user_id',
        'certificate_number',
        'printed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    /**
     * Relationship to the Student model.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship to the User model (the Admin who performed the print).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
