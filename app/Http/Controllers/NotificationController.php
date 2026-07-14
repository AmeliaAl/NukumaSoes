<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

/**
 * NotificationController — Placeholder integrasi modul Produksi
 *
 * Saat ini halaman notifikasi hanya menampilkan daftar kosong
 * karena modul Produksi belum terintegrasi.
 *
 * TODO (setelah integrasi Produksi):
 *   - index()    : Ambil notifikasi dari tabel notifications
 *   - markRead() : Tandai notifikasi sebagai sudah dibaca
 *   - Tambahkan endpoint untuk menerima notifikasi dari modul Produksi
 *
 * Alur notifikasi yang akan diaktifkan:
 *   Produksi → Pembelian : Permintaan Bahan Baku baru (type: produksi_to_pembelian)
 *   Pembelian → Produksi : Pembelian selesai + status permintaan (type: pembelian_to_produksi)
 */
class NotificationController extends Controller
{
    /**
     * Halaman daftar notifikasi.
     * Saat ini kosong — modul Produksi belum terintegrasi.
     */
    public function index()
    {
        $notifications = Notification::latest()->paginate(20);
        $unreadCount   = Notification::unread()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        Notification::unread()->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }

    /**
     * Ambil jumlah notifikasi belum dibaca (untuk badge navbar).
     * Dipanggil via AJAX atau ViewComposer.
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => Notification::unread()->count(),
        ]);
    }
}
