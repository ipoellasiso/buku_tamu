<?php

namespace App\Services\AI;

use App\Models\Umkm;

/**
 * Membangun system prompt yang kaya konteks bisnis UMKM
 * sehingga AI dapat memberi saran yang relevan dan spesifik.
 */
class UMKMPromptBuilder
{
    public function build(Umkm $umkm, string $mode = 'general'): string
    {
        $context = $this->buildContext($umkm);

        $base = <<<PROMPT
Kamu adalah asisten digital cerdas untuk UMKM bernama "{$umkm->nama_usaha}" ({$umkm->jenis_usaha}).
Kamu membantu pemilik usaha dalam hal: analisis keuangan, manajemen stok, strategi pemasaran,
dan pertanyaan bisnis sehari-hari. Jawab dalam Bahasa Indonesia yang ramah dan mudah dipahami.
Berikan saran yang praktis dan spesifik berdasarkan data usaha di bawah ini.

=== DATA USAHA SAAT INI ===
{$context}
===========================

PROMPT;

        return $base . $this->getModeInstruction($mode);
    }

    private function buildContext(Umkm $umkm): string
    {
        $lines = [];

        // Info dasar
        $lines[] = "Nama Usaha  : {$umkm->nama_usaha}";
        $lines[] = "Jenis Usaha : {$umkm->jenis_usaha}";

        // Laporan bulan ini
        $laporan = $umkm->laporanBulanIni();
        if ($laporan) {
            $lines[] = "Bulan ini (" . now()->format('F Y') . "):";
            $lines[] = "  - Pemasukan   : Rp " . number_format($laporan->total_pemasukan, 0, ',', '.');
            $lines[] = "  - Pengeluaran : Rp " . number_format($laporan->total_pengeluaran, 0, ',', '.');
            $lines[] = "  - Laba/Rugi   : Rp " . number_format($laporan->laba_rugi, 0, ',', '.');
        }

        // Produk stok menipis
        $lowStock = $umkm->products()->lowStock()->get(['nama', 'stok', 'stok_minimum', 'satuan']);
        if ($lowStock->isNotEmpty()) {
            $lines[] = "Produk stok menipis:";
            foreach ($lowStock as $p) {
                $lines[] = "  - {$p->nama}: sisa {$p->stok} {$p->satuan} (min: {$p->stok_minimum})";
            }
        }

        // Jumlah produk aktif
        $totalProduk = $umkm->products()->active()->count();
        $lines[]     = "Total produk aktif: {$totalProduk} item";

        return implode("\n", $lines);
    }

    private function getModeInstruction(string $mode): string
    {
        return match ($mode) {
            'financial'  => "Fokus pada analisis keuangan. Berikan insight mendalam tentang arus kas, laba rugi, dan efisiensi biaya.",
            'marketing'  => "Fokus pada strategi pemasaran dan penjualan. Berikan ide kreatif yang sesuai dengan jenis usaha dan kondisi pasar lokal.",
            'inventory'  => "Fokus pada manajemen stok dan inventori. Bantu optimasi pembelian dan hindari kekurangan atau kelebihan stok.",
            default      => "Jawab pertanyaan umum tentang bisnis dengan saran praktis. Jika pertanyaan butuh data lebih spesifik, tanyakan dengan ramah.",
        };
    }
}
