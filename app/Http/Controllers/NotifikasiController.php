<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $notifikasi = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('notifikasi.index', [
            'notifikasi' => $notifikasi,
        ]);
    }

    public function tandaiDibaca(Request $request, string $id): RedirectResponse
    {
        $item = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (! $item) {
            abort(404, 'Notifikasi tidak ditemukan.');
        }

        $item->markAsRead();

        return back()->with('toast_success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    public function tandaiSemuaDibaca(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('toast_success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}

