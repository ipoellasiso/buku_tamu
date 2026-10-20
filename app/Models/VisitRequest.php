<?php

// app/Models/VisitRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitRequest extends Model
{
    protected $fillable = [
        'guest_user_id', 'employee_id', 'purpose', 'notes',
        'requested_at', 'status', 'employee_notes',
        'scheduled_at', 'check_out_at', 'guest_id',
        'qr_token', 'qr_code_path', // ← pastikan ini ada
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function guestUser()
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}