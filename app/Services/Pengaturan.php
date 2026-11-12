<?php

namespace App\Services;

use App\Models\Setelan;

class Pengaturan
{
    private const DEFAULT = [
        'nama_aplikasi' => 'Buku Tamu',
        'nama_instansi' => 'Nama Instansi',
        'singkatan'     => '',
        'lokasi'        => '',
        'alamat'        => '',
        'logo'          => '',
    ];

    private ?array $memo = null;

    public function semua(): array
    {
        if ($this->memo !== null) {
            return $this->memo;
        }

        try {
            $db = Setelan::pluck('nilai', 'kunci')->all();
        } catch (\Throwable $e) {
            $db = []; // tabel belum dimigrasi
        }

        $db = array_filter($db, fn ($v) => $v !== null && $v !== '');

        return $this->memo = array_merge(self::DEFAULT, $db);
    }

    public function __get(string $name)
    {
        $d = $this->semua();

        return match ($name) {
            // contoh: "BPKAD Kota Palu"
            'nama_pendek' => trim(($d['singkatan'] ?: $d['nama_instansi']) . ' ' . $d['lokasi']),
            // contoh: "Badan Pengelola Keuangan dan Aset Daerah Kota Palu"
            'nama_resmi'  => trim($d['nama_instansi'] . ' ' . $d['lokasi']),
            'logo_url'    => $d['logo'] ? asset('storage/' . $d['logo']) : null,
            default       => $d[$name] ?? null,
        };
    }

    public function __isset(string $name): bool
    {
        return true;
    }

    public function simpan(array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            Setelan::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        }
        $this->memo = null;
    }
}