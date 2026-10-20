<?php

namespace App\Console;

use App\Jobs\GenerateAIRecommendation;
use App\Models\Umkm;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Setiap Senin pagi jam 07:00 — generate rekomendasi otomatis untuk semua UMKM
        $schedule->call(function () {
            Umkm::chunk(50, function ($umkms) {
                foreach ($umkms as $umkm) {
                    GenerateAIRecommendation::dispatch($umkm, 'umum')->onQueue('ai');
                }
            });
        })->weeklyOn(1, '07:00')->name('weekly-ai-recommendations');

        // Setiap tanggal 1 — generate laporan + insight bulan sebelumnya
        $schedule->call(function () {
            $periode = now()->subMonth()->format('Y-m');
            Umkm::chunk(50, function ($umkms) use ($periode) {
                foreach ($umkms as $umkm) {
                    \App\Jobs\GenerateFinancialInsight::dispatch(
                        app(\App\Services\FinancialAnalysisService::class)->summarize($umkm, $periode)
                    )->onQueue('ai');
                }
            });
        })->monthlyOn(1, '06:00')->name('monthly-financial-insight');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
