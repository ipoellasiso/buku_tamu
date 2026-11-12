<?php

namespace App\Services;

use App\Models\Guest;
use App\Models\VisitRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class KunjunganGabungan
{
    public static function label(?string $s): string
    {
        return match ($s) {
            'approved'  => 'Disetujui',
            'confirmed' => 'Di Dalam',
            'completed' => 'Selesai',
            'rejected'  => 'Ditolak',
            default     => 'Menunggu',
        };
    }

    public static function sumberLabel(?string $s): string
    {
        return $s === 'langsung' ? 'Langsung' : 'Terjadwal';
    }

    private static function c($t): ?Carbon
    {
        return $t ? Carbon::parse($t) : null;
    }

    public static function dariPermohonan(VisitRequest $v): object
    {
        return (object) [
            'sumber'    => 'terjadwal',
            'id'        => $v->id,
            'nama'      => $v->guestUser?->name ?? '-',
            'instansi'  => '-',
            'hp'        => $v->guestUser?->phone ?? '-',
            'keperluan' => $v->purpose,
            'tujuan'    => $v->employee?->name ?? '-',
            'catatan'   => $v->notes,
            'foto'      => null,
            'waktu'     => self::c($v->scheduled_at) ?? self::c($v->requested_at),
            'selesai'   => self::c($v->check_out_at),
            'dibuat'    => self::c($v->created_at),
            'status'    => $v->status,
        ];
    }

    public static function dariLangsung(Guest $g): object
    {
        return (object) [
            'sumber'    => 'langsung',
            'id'        => $g->id,
            'nama'      => $g->name,
            'instansi'  => $g->institution ?: '-',
            'hp'        => $g->phone ?: '-',
            'keperluan' => $g->purpose,
            'tujuan'    => $g->host ?: '-',
            'catatan'   => null,
            'foto'      => $g->photo_path,
            'waktu'     => self::c($g->check_in_at) ?? self::c($g->created_at),
            'selesai'   => self::c($g->check_out_at),
            'dibuat'    => self::c($g->created_at),
            'status'    => $g->status,
        ];
    }

    /**
     * Gabungan kunjungan terjadwal + tamu langsung, terbaru di atas.
     */
    public function ambil(
        ?Carbon $dari = null,
        ?Carbon $sampai = null,
        ?array $status = null,
        ?string $sumber = null,
        ?string $cari = null
    ): Collection {
        $filter = function ($q) use ($dari, $sampai, $status) {
            if ($dari)   { $q->where('created_at', '>=', $dari); }
            if ($sampai) { $q->where('created_at', '<=', $sampai); }
            if ($status) { $q->whereIn('status', $status); }
        };

        $terjadwal = collect();
        $langsung  = collect();

        if (!$sumber || $sumber === 'terjadwal') {
            $q = VisitRequest::with(['guestUser', 'employee']);
            $filter($q);
            $terjadwal = $q->get()->map(fn ($v) => self::dariPermohonan($v));
        }

        if (!$sumber || $sumber === 'langsung') {
            $q = Guest::query();
            $filter($q);
            $langsung = $q->get()->map(fn ($g) => self::dariLangsung($g));
        }

        $semua = $terjadwal->concat($langsung);

        if ($cari) {
            $kata  = mb_strtolower($cari);
            $semua = $semua->filter(fn ($r) => str_contains(
                mb_strtolower("{$r->nama} {$r->instansi} {$r->keperluan} {$r->tujuan}"), $kata
            ));
        }

        return $semua->sortByDesc(fn ($r) => $r->dibuat?->timestamp ?? 0)->values();
    }

    public function satu(string $sumber, int $id): object
    {
        return match ($sumber) {
            'terjadwal' => self::dariPermohonan(VisitRequest::with(['guestUser', 'employee'])->findOrFail($id)),
            'langsung'  => self::dariLangsung(Guest::findOrFail($id)),
            default     => abort(404),
        };
    }

    public function ringkas(Collection $d): array
    {
        return [
            'total'     => $d->count(),
            'terjadwal' => $d->where('sumber', 'terjadwal')->count(),
            'langsung'  => $d->where('sumber', 'langsung')->count(),
            'selesai'   => $d->where('status', 'completed')->count(),
            'didalam'   => $d->where('status', 'confirmed')->count(),
            'pending'   => $d->where('status', 'pending')->count(),
        ];
    }

    public function paginate(Collection $items, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}