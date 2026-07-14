@extends('adminlte::page')

@section('title', 'Pembelian')

@section('content_header')
    <h1>Data Pembelian</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">
        <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
            + Tambah Pembelian
        </a>
    </div>

    <div class="card-body">

        {{-- Filter Pencarian — placeholder integrasi Produksi --}}
        <form method="GET" class="mb-3">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="small text-muted mb-1">Nomor Permintaan</label>
                    <input type="text" name="filter_nomor_permintaan" class="form-control form-control-sm"
                           placeholder="Contoh: PR-001"
                           value="{{ request('filter_nomor_permintaan') }}">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted mb-1">Status Permintaan</label>
                    <select name="filter_status" class="form-control form-control-sm">
                        <option value="">Semua Status</option>
                        <option value="lengkap"  {{ request('filter_status') === 'lengkap'  ? 'selected' : '' }}>Lengkap</option>
                        <option value="sebagian" {{ request('filter_status') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('pembelian.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Pembelian</th>
                    <th>Nomor Permintaan</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Jumlah Item</th>
                    <th>Grand Total</th>
                    <th>Pembayaran</th>
                    <th>Status Permintaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembelians as $pembelian)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pembelian->no_pembelian ?? '-' }}</td>
                    <td>{{ $pembelian->nomor_permintaan ?? '-' }}</td>
                    <td>{{ $pembelian->tanggal }}</td>
                    <td>{{ $pembelian->supplier->nama_supplier }}</td>

                    {{-- Jumlah Item + Tombol Detail --}}
                    <td>
                        {{ $pembelian->details->count() }} item
                        <button type="button" class="btn btn-info btn-xs ml-2"
                                data-toggle="modal"
                                data-target="#modalDetail{{ $pembelian->id }}">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>

                    <td>{{ \App\Helpers\FormatHelper::rupiah($pembelian->grand_total ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0) + ($pembelian->ongkir ?? 0))) }}</td>
                    <td>{{ $pembelian->coa->nama_akun }}</td>

                    <td>
                        @php $sp = $pembelian->status_permintaan; @endphp
                        @if($sp === 'lengkap')
                            <span class="badge badge-success">Lengkap</span>
                        @elseif($sp === 'sebagian')
                            <span class="badge badge-warning">Sebagian</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('pembelian.edit', $pembelian->id) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


        {{-- ============================================================
             MODAL DETAIL PEMBELIAN
             Bagian 1: Daftar Item | Bagian 2: Ringkasan Transaksi
        ============================================================ --}}
        @foreach($pembelians as $pembelian)
        <div class="modal fade" id="modalDetail{{ $pembelian->id }}" tabindex="-1"
             aria-labelledby="modalDetailLabel{{ $pembelian->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="modalDetailLabel{{ $pembelian->id }}">
                            <i class="fas fa-shopping-cart mr-1"></i>
                            Detail Pembelian: <strong>{{ $pembelian->no_pembelian }}</strong>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        {{-- ── Bagian 1: Info Header ── --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th width="140">No Pembelian</th>
                                        <td>{{ $pembelian->no_pembelian }}</td>
                                    </tr>
                                    <tr>
                                        <th>No Permintaan</th>
                                        <td>{{ $pembelian->nomor_permintaan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ $pembelian->tanggal }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier</th>
                                        <td>{{ $pembelian->supplier->nama_supplier }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- ── Bagian 1: Daftar Item Pembelian ── --}}
                        <h6 class="font-weight-bold mb-2">Daftar Item Pembelian</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Bahan Baku</th>
                                        <th class="text-center">Qty Pembelian</th>
                                        <th class="text-center">Isi/Kemasan</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-right">Harga Satuan</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembelian->details as $detail)
                                    <tr>
                                        <td>{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                                        <td class="text-center">{{ $detail->qty }}</td>
                                        <td class="text-center">{{ $detail->isi_per_kemasan ?? '-' }}</td>
                                        <td class="text-center">{{ $detail->bahanBaku->satuan ?? '-' }}</td>
                                        <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($detail->harga) }}</td>
                                        <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($detail->subtotal) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ── Bagian 2: Ringkasan Transaksi ── --}}
                        <div class="row">
                            <div class="col-md-5 ml-auto">
                                <h6 class="font-weight-bold mb-2">Ringkasan Transaksi</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Total Pembelian</th>
                                        <td class="text-right">
                                            {{ \App\Helpers\FormatHelper::rupiah($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>(-) Diskon</th>
                                        <td class="text-right">
                                            {{ \App\Helpers\FormatHelper::rupiah($pembelian->diskon ?? 0) }}
                                        </td>
                                    </tr>
                                    <tr style="border-top: 1px solid #dee2e6;">
                                        <th>Total Bersih</th>
                                        <td class="text-right">
                                            {{ \App\Helpers\FormatHelper::rupiah($pembelian->total_bersih ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0))) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>(+) Ongkir</th>
                                        <td class="text-right">
                                            {{ \App\Helpers\FormatHelper::rupiah($pembelian->ongkir ?? 0) }}
                                        </td>
                                    </tr>
                                    <tr style="border-top: 2px solid #343a40;">
                                        <th class="font-weight-bold">Grand Total</th>
                                        <td class="text-right font-weight-bold">
                                            {{ \App\Helpers\FormatHelper::rupiah($pembelian->grand_total ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0) + ($pembelian->ongkir ?? 0))) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Pembayaran</th>
                                        <td class="text-right">{{ $pembelian->coa->nama_akun ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- ── Dokumen Pembelian ── --}}
                        <hr>
                        <h6 class="font-weight-bold mb-2">Dokumen Pembelian</h6>
                        @if($pembelian->paymentProofs->count())
                        <ul class="list-group mb-2">
                            @foreach($pembelian->paymentProofs as $proof)
                            <li class="list-group-item py-2">
                                <a href="{{ asset('storage/' . $proof->file_path) }}" target="_blank">
                                    <i class="fas fa-file mr-1"></i>{{ $proof->file_name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-muted mb-0">Tidak ada dokumen.</p>
                        @endif

                    </div>{{-- end modal-body --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach



    </div>{{-- end card-body --}}
</div>

@stop

@section('js')
<script>
$(document).ready(function () {

    $('.table').DataTable({ ordering: false });

});
</script>
@stop
