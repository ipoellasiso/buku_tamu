<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Jenis keperluan tamu langsung.
 * Satu sumber untuk: pilihan di form tamu, label di panel admin, dan kalimat pengumuman suara.
 *
 * Placeholder pada kalimat:
 *   {nama}    nama tamu
 *   {asal}    ", dari <instansi>" (kosong kalau tidak diisi)
 *   {tujuan}  "Bapak atau Ibu <pegawai>, <jabatan>"
 *   {ruang}   ", yaitu <ruangan>" (hanya jika tabel pegawai punya kolom `room`)
 *   {perlu}   keterangan keperluan (dipotong 80 karakter)
 *
 * 'suara'      dipakai kalau tamu menyebut pegawai yang dituju
 * 'suara_umum' dipakai kalau tidak ada pegawai yang dituju
 */
class JenisKeperluan
{
    public const DAFTAR = [
        'berkas' => [
            'label' => 'Antar berkas',
            'info'  => 'Berkas dititipkan di resepsionis, tidak perlu bertemu langsung.',
            'suara' => 'Perhatian. Tamu atas nama {nama}{asal} mengantar berkas untuk {tujuan}. '
                     . 'Berkas dititipkan di resepsionis. Mohon segera diambil di resepsionis.',
            'suara_umum' => 'Perhatian. Tamu atas nama {nama}{asal} mengantar berkas dan menitipkannya di resepsionis. '
                          . 'Mohon petugas yang berkepentingan mengambil berkas di resepsionis.',
        ],
        'temu' => [
            'label' => 'Bertemu langsung',
            'info'  => 'Anda akan menuju ruangan pegawai yang dituju untuk bertemu dan berbincang langsung.',
            'suara' => 'Perhatian. {tujuan}, tamu atas nama {nama}{asal} ingin bertemu langsung '
                     . 'dan akan menuju ruangan Anda{ruang}. Mohon bersiap menerima tamu di ruangan.',
            'suara_umum' => 'Perhatian. Tamu atas nama {nama}{asal} ingin bertemu langsung dengan pegawai. '
                          . 'Mohon resepsionis menanyakan tujuan tamu.',
        ],
        'konsultasi' => [
            'label' => 'Konsultasi',
            'info'  => 'Anda akan menuju ruangan pegawai yang dituju untuk berkonsultasi.',
            'suara' => 'Perhatian. {tujuan}, tamu atas nama {nama}{asal} ingin berkonsultasi '
                     . 'dan akan menuju ruangan Anda{ruang}. Mohon bersiap menerima tamu di ruangan.',
            'suara_umum' => 'Perhatian. Tamu atas nama {nama}{asal} ingin berkonsultasi. '
                          . 'Mohon resepsionis menanyakan tujuan tamu.',
        ],
        'lainnya' => [
            'label' => 'Lainnya',
            'info'  => 'Anda menunggu di resepsionis sampai ditemui petugas.',
            'suara' => 'Perhatian. {tujuan}, tamu atas nama {nama}{asal} menunggu di resepsionis. '
                     . 'Keperluan: {perlu}. Mohon menemui tamu di resepsionis.',
            'suara_umum' => 'Perhatian. Tamu atas nama {nama}{asal} telah tiba di resepsionis. Keperluan: {perlu}.',
        ],
    ];

    public static function kode(): array
    {
        return array_keys(self::DAFTAR);
    }

    public static function label(?string $kode): string
    {
        return self::DAFTAR[$kode]['label'] ?? 'Lainnya';
    }

    /** Kalimat yang diucapkan untuk satu tamu (model Guest). */
    public static function kalimat($t): string
    {
        $kode = isset(self::DAFTAR[$t->purpose_type]) ? $t->purpose_type : 'lainnya';
        $cfg  = self::DAFTAR[$kode];

        $host    = $t->host;
        $jabatan = $t->employee->position ?? null;
        $room    = $t->employee->room ?? null;

        $tujuan = $host ? 'Bapak atau Ibu '.$host.($jabatan ? ', '.$jabatan : '') : '';

        return strtr($host ? $cfg['suara'] : $cfg['suara_umum'], [
            '{nama}'   => trim((string) $t->name),
            '{asal}'   => $t->institution ? ', dari '.trim($t->institution) : '',
            '{tujuan}' => $tujuan,
            '{ruang}'  => $room ? ', yaitu '.$room : '',
            '{perlu}'  => Str::limit((string) $t->purpose, 80),
        ]);
    }
}