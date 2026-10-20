<?php

namespace App\Services;

use App\Models\FinancialReport;
use App\Models\Umkm;
use App\Services\AI\AIManager;
use App\Services\AI\UMKMPromptBuilder;
use Illuminate\Support\Facades\DB;

class FinancialAnalysisService
{
    public function __construct(
        private AIManager $ai,
        private UMKMPromptBuilder $promptBuilder,
    ) {}

    /**
     * Hitung ringkasan keuangan untuk periode tertentu.
     * periode format: YYYY-MM
     */
    public function summarize(Umkm $umkm, string $periode): FinancialReport
    {
        [$year, $month] = explode('-', $periode);

        $rows = $umkm->transactions()
            ->selectRaw('tipe, kategori, SUM(total) as jumlah')
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->groupBy('tipe', 'kategori')
            ->get();

        $pemasukan   = $rows->where('tipe', 'pemasukan')->sum('jumlah');
        $pengeluaran = $rows->where('tipe', 'pengeluaran')->sum('jumlah');
        $labaRugi    = $pemasukan - $pengeluaran;

        // Detail per kategori
        $detail = [
            'pemasukan'   => $rows->where('tipe', 'pemasukan')
                ->mapWithKeys(fn($r) => [$r->kategori => (float)$r->jumlah])->toArray(),
            'pengeluaran' => $rows->where('tipe', 'pengeluaran')
                ->mapWithKeys(fn($r) => [$r->kategori => (float)$r->jumlah])->toArray(),
        ];

        $report = FinancialReport::updateOrCreate(
            ['umkm_id' => $umkm->id, 'periode' => $periode],
            [
                'total_pemasukan'   => $pemasukan,
                'total_pengeluaran' => $pengeluaran,
                'laba_rugi'         => $labaRugi,
                'detail_json'       => $detail,
            ],
        );

        return $report;
    }

    /**
     * Data chart 12 bulan terakhir untuk grafik Flutter.
     */
    public function chartData(Umkm $umkm): array
    {
        $rows = $umkm->transactions()
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, tipe, SUM(total) as jumlah")
            ->where('tanggal', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan', 'tipe')
            ->orderBy('bulan')
            ->get();

        $chart = [];
        foreach ($rows as $r) {
            $chart[$r->bulan][$r->tipe] = (float) $r->jumlah;
        }

        // Pastikan setiap bulan ada kedua tipe
        return collect($chart)->map(fn($v, $k) => [
            'bulan'       => $k,
            'pemasukan'   => $v['pemasukan'] ?? 0,
            'pengeluaran' => $v['pengeluaran'] ?? 0,
            'laba_rugi'   => ($v['pemasukan'] ?? 0) - ($v['pengeluaran'] ?? 0),
        ])->values()->toArray();
    }

    /**
     * Generate AI insight untuk laporan keuangan.
     * Dipanggil via Job agar tidak blocking response.
     */
    public function generateInsight(FinancialReport $report): void
    {
        $umkm = $report->umkm;

        $systemPrompt = $this->promptBuilder->build($umkm, 'financial');

        $prompt = <<<PROMPT
Analisis laporan keuangan berikut untuk usaha "{$umkm->nama_usaha}" periode {$report->periode}:

- Total Pemasukan   : Rp {$report->total_pemasukan}
- Total Pengeluaran : Rp {$report->total_pengeluaran}
- Laba/Rugi         : Rp {$report->laba_rugi}
- Detail            : {$this->formatDetail($report->detail_json)}

Berikan analisis singkat dalam format JSON dengan struktur:
{
  "ringkasan": "...",
  "kekuatan": ["...", "..."],
  "perhatian": ["...", "..."],
  "saran": ["...", "..."],
  "skor_kesehatan": 0-100
}
Jawab HANYA dengan JSON, tanpa teks tambahan.
PROMPT;

        try {
            $result = $this->ai->chatWithFallback(
                [['role' => 'user', 'content' => $prompt]],
                $systemPrompt,
            );

            $insight = json_decode($result['content'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $report->update(['ai_insight' => $insight]);
            }
        } catch (\Throwable) {
            // Jangan gagalkan proses utama jika AI error
        }
    }

    private function formatDetail(?array $detail): string
    {
        if (! $detail) return '-';

        $lines = [];
        foreach ($detail['pemasukan'] ?? [] as $k => $v) {
            $lines[] = "  Pemasukan [{$k}]: Rp " . number_format($v, 0, ',', '.');
        }
        foreach ($detail['pengeluaran'] ?? [] as $k => $v) {
            $lines[] = "  Pengeluaran [{$k}]: Rp " . number_format($v, 0, ',', '.');
        }

        return implode("\n", $lines);
    }
}
