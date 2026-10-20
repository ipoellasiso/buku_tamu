<?php

namespace App\Services;

use App\Models\AiRecommendation;
use App\Models\Umkm;
use App\Services\AI\AIManager;
use App\Services\AI\UMKMPromptBuilder;

class RecommendationService
{
    public function __construct(
        private AIManager $ai,
        private UMKMPromptBuilder $promptBuilder,
    ) {}

    public function generate(Umkm $umkm, string $tipe): AiRecommendation
    {
        $systemPrompt = $this->promptBuilder->build($umkm, $tipe);
        $userPrompt   = $this->buildUserPrompt($umkm, $tipe);

        $result = $this->ai->chatWithFallback(
            [['role' => 'user', 'content' => $userPrompt]],
            $systemPrompt,
        );

        // Coba parse JSON dari respons AI
        $content   = $result['content'];
        $judul     = $this->extractJudul($content, $tipe);
        $confidence= $this->estimateConfidence($result);

        return $umkm->aiRecommendations()->create([
            'tipe'             => $tipe,
            'judul'            => $judul,
            'konten'           => $content,
            'confidence_score' => $confidence,
        ]);
    }

    private function buildUserPrompt(Umkm $umkm, string $tipe): string
    {
        return match ($tipe) {
            'pemasaran' => "Berikan 3 strategi pemasaran konkret dan spesifik untuk usaha {$umkm->nama_usaha} ({$umkm->jenis_usaha}) yang bisa langsung diterapkan minggu ini. Sertakan estimasi biaya dan langkah-langkah detail.",

            'keuangan'  => "Analisis kondisi keuangan saat ini dan berikan rekomendasi penghematan biaya serta peningkatan pendapatan untuk usaha {$umkm->nama_usaha}. Prioritaskan yang bisa dilakukan tanpa modal tambahan.",

            'stok'      => "Berikan rekomendasi manajemen stok untuk {$umkm->nama_usaha}. Produk mana yang harus segera direstok? Berapa jumlah ideal? Strategi apa untuk mencegah penumpukan stok?",

            default     => "Apa 3 hal paling penting yang harus dilakukan pemilik usaha {$umkm->nama_usaha} minggu ini untuk mengembangkan bisnis? Berikan saran praktis dan actionable.",
        };
    }

    private function extractJudul(string $content, string $tipe): string
    {
        // Ambil kalimat pertama sebagai judul (max 80 karakter)
        $firstLine = trim(explode("\n", $content)[0]);
        $judul     = mb_substr(strip_tags($firstLine), 0, 80);

        return $judul ?: ucfirst($tipe) . ' — ' . now()->format('d M Y');
    }

    private function estimateConfidence(array $result): float
    {
        // Estimasi berdasarkan panjang respons (proxy sederhana)
        $length = mb_strlen($result['content'] ?? '');

        if ($length > 800) return 0.9;
        if ($length > 400) return 0.75;
        return 0.6;
    }
}
