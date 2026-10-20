<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id', 'judul', 'ai_provider', 'ai_model', 'total_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }

    // Ambil history untuk dikirim ke AI (max 20 pesan terakhir)
    public function getHistoryForAI(int $limit = 20): array
    {
        return $this->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->konten])
            ->values()
            ->toArray();
    }
}
