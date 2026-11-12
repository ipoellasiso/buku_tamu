<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrSitusController extends Controller
{
    private const KEY = 'qr_situs_base';

    // Alamat dasar situs: yang disimpan admin, atau host yang sedang dipakai
    private function base(Request $request): string
    {
        return Cache::get(self::KEY, $request->getSchemeAndHttpHost());
    }

    private function svg(string $link): string
    {
        return (string) QrCode::format('svg')->size(400)->margin(1)->errorCorrection('H')->generate($link);
    }

    public function index(Request $request)
    {
        $base  = $this->base($request);
        $link  = $base . '/mulai';
        $qr    = $this->svg($link);
        $lokal = in_array(parse_url($base, PHP_URL_HOST), ['localhost', '127.0.0.1', '::1']);

        return view('web.qr.index', compact('base', 'link', 'qr', 'lokal'));
    }

    public function simpan(Request $request)
    {
        $data = $request->validate(['base' => 'required|url|max:200']);
        Cache::forever(self::KEY, rtrim($data['base'], '/'));

        return back()->with('success', 'Alamat situs disimpan, QR diperbarui.');
    }

    public function unduh(Request $request)
    {
        $svg = $this->svg($this->base($request) . '/mulai');

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="qr-buku-tamu.svg"',
        ]);
    }

    public function poster(Request $request)
    {
        $link = $this->base($request) . '/mulai';
        $qr   = $this->svg($link);

        return view('web.qr.poster', compact('link', 'qr'));
    }
}