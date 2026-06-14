@extends('adminlte::page')

@section('title', 'Pembelian')

@section('content_header')
    <h1>Data Pembelian</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">

        <a href="{{ route('pembelian.create') }}"
           class="btn btn-primary">

            + Tambah Pembelian

        </a>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>No Pembelian</th>
                    <th>Nomor Permintaan</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Jumlah Item</th>
                    <th>Total Pembelian</th>
                    <th>Diskon</th>
                    <th>Ongkir</th>
                    <th>Grand Total</th>
                    <th>Pembayaran</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                @foreach($pembelians as $pembelian)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $pembelian->no_pembelian ?? '-' }}
                    </td>

                    <td>
                        {{ $pembelian->nomor_permintaan ?? '-' }}
                    </td>

                    <td>
                        {{ $pembelian->tanggal }}
                    </td>

                    <td>
                        {{ $pembelian->supplier->nama_supplier }}
                    </td>

                    <td>
                        {{ $pembelian->details->count() }} item
                        <button type="button" class="btn btn-info btn-xs ml-2" data-toggle="modal" data-target="#modalDetail{{ $pembelian->id }}">
                            <i class="fas fa-eye"></i> Detail
                        </button>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="modalDetail{{ $pembelian->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $pembelian->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDetailLabel{{ $pembelian->id }}">Detail Pembelian: {{ $pembelian->no_pembelian }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Bahan Baku</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pembelian->details as $detail)
                                                <tr>
                                                    <td>{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                                                    <td>{{ $detail->qty }}</td>
                                                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        Rp {{ number_format($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga, 0, ',', '.') }}
                    </td>

                    <td>
                        Rp {{ number_format($pembelian->diskon ?? 0, 0, ',', '.') }}
                    </td>

                    <td>
                        Rp {{ number_format($pembelian->ongkir ?? 0, 0, ',', '.') }}
                    </td>

                    <td>
                        Rp {{ number_format($pembelian->grand_total ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0) + ($pembelian->ongkir ?? 0)), 0, ',', '.') }}
                    </td>

                    <td>
                        {{ $pembelian->coa->nama_akun }}
                    </td>

                    <td>

                        <a href="{{ route('pembelian.edit', $pembelian->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ route('pembelian.destroy', $pembelian->id) }}"
                              method="POST"
                              style="display:inline-block;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin hapus data?')">

                                Hapus

                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@stop

@section('js')

<script>

$(document).ready(function () {

    $('.table').DataTable({

        ordering: false

    });

});

</script>

@stop