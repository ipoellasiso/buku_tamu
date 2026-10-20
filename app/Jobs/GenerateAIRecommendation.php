<?php

namespace App\Jobs;

use App\Models\Umkm;
use App\Services\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Jalankan via scheduler (misal: mingguan) untuk generate rekomendasi otomatis
 * tanpa user perlu minta manual.
 */
class GenerateAIRecommendation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(
        public Umkm   $umkm,
        public string $tipe = 'umum',
    ) {}

    public function handle(RecommendationService $service): void
    {
        $service->generate($this->umkm, $this->tipe);
    }
}
