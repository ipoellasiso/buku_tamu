<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    protected $fillable = [
        'name', 'institution', 'address', 'phone', 'purpose',
        'host', 'photo_path', 'signature_path', 'qr_code_path',
        'qr_token', 'check_in_at', 'check_out_at',
        'status', 'user_id', 'purpose_type' // ← tambahkan
    ];

    protected $casts = [
        'check_in_at'  => 'datetime',
        'check_out_at' => 'datetime',
    ];
}
