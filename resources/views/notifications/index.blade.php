@extends('adminlte::page')

@section('title', 'Notifikasi')

@section('content_header')
    <h1>Notifikasi</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            @if($unreadCount > 0)
                <span class="badge badge-danger mr-1">{{ $unreadCount }}</span>
            @endif
            Semua Notifikasi
        </span>
        @if($unreadCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <div class="card-body p-0">
        @if($notifications->isEmpty())
            {{-- Placeholder: akan terisi setelah modul Produksi terintegrasi --}}
            <div class="text-center text-muted py-5">
                <i class="fas fa-bell-slash fa-3x mb-3 d-block" style="color:#ced4da;"></i>
                <p class="mb-1" style="font-size:15px;">Belum ada notifikasi.</p>
                <small>Notifikasi akan muncul setelah modul Produksi terintegrasi.</small>
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($notifications as $notif)
                <li class="list-group-item {{ $notif->is_read ? '' : 'bg-light' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            @if($notif->type === 'produksi_to_pembelian')
                                <i class="fas fa-clipboard-list text-info mr-2"></i>
                            @elseif($notif->type === 'pembelian_to_produksi')
                                <i class="fas fa-shopping-cart text-success mr-2"></i>
                            @else
                                <i class="fas fa-bell text-secondary mr-2"></i>
                            @endif
                            <strong>{{ $notif->title }}</strong>
                            @if(!$notif->is_read)
                                <span class="badge badge-danger ml-1" style="font-size:10px;">Baru</span>
                            @endif
                            <div class="text-muted" style="font-size:13px; margin-top:4px;">
                                {{ $notif->message }}
                                @if($notif->reference_no)
                                    <span class="ml-2 badge badge-light border">{{ $notif->reference_no }}</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!$notif->is_read)
                        <form action="{{ route('notifications.mark-read', $notif->id) }}" method="POST" class="ml-2">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline-secondary" title="Tandai dibaca">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>

            <div class="px-3 py-2">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ── Template Notifikasi (placeholder untuk integrasi) ── --}}
{{--
    Template: Produksi → Pembelian
    ┌──────────────────────────────────────────┐
    │  📋 Permintaan Bahan Baku Baru           │
    │  Nomor Permintaan : PR-001               │
    │  Silakan lakukan proses pembelian.        │
    └──────────────────────────────────────────┘

    Template: Pembelian → Produksi
    ┌──────────────────────────────────────────┐
    │  🛒 Pembelian Telah Diproses             │
    │  Nomor Permintaan : PR-001               │
    │  Status : Lengkap / Sebagian             │
    └──────────────────────────────────────────┘
--}}

@stop
