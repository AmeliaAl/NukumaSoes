@extends('adminlte::page')

@section('title', 'Laporan Pembelian')

@section('content_header')
    <h1>Laporan Pembelian</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <div class="text-center mb-4">

            <h3>
                LAPORAN PEMBELIAN
            </h3>

            <h5>

                Periode:
                {{ request('tanggal_awal') ?? '-' }}
                s/d
                {{ request('tanggal_akhir') ?? '-' }}

            </h5>

        </div>

        <form method="GET">

            <div class="row">

                <div class="col-md-4">

                    <label>Tanggal Awal</label>

                    <input type="date"
                           name="tanggal_awal"
                           class="form-control"
                           value="{{ request('tanggal_awal') }}">

                </div>

                <div class="col-md-4">

                    <label>Tanggal Akhir</label>

                    <input type="date"
                           name="tanggal_akhir"
                           class="form-control"
                           value="{{ request('tanggal_akhir') }}">

                </div>

                <div class="col-md-4">

                    <br>

                    <button type="submit"
                            class="btn btn-primary">

                        Filter

                    </button>

                    <a href="{{ url('laporan-pembelian/print') }}?tanggal_awal={{ request('tanggal_awal') }}&tanggal_akhir={{ request('tanggal_akhir') }}"
                       class="btn btn-danger">

                        Download PDF

                    </a>

                </div>

            </div>

        </form>

        <hr>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Bahan Baku</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Grand Total</th>

                </tr>

            </thead>

            <tbody>

                @forelse($pembelians as $pembelian)
                @php $rowspan = max(1, $pembelian->details->count()); @endphp
                <tr>
                    <td rowspan="{{ $rowspan }}">{{ $loop->iteration }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $pembelian->tanggal }}</td>
                    <td rowspan="{{ $rowspan }}">{{ $pembelian->supplier->nama_supplier }}</td>
                    @if($pembelian->details->count() > 0)
                        <td>{{ $pembelian->details[0]->bahanBaku->nama_bahan ?? '-' }}</td>
                        <td>{{ $pembelian->details[0]->qty }}</td>
                        <td>Rp {{ number_format($pembelian->details[0]->harga, 0, ',', '.') }}</td>
                    @else
                        <td>-</td><td>0</td><td>Rp 0</td>
                    @endif
                    <td rowspan="{{ $rowspan }}">Rp {{ number_format($pembelian->grand_total ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0) + ($pembelian->ongkir ?? 0)), 0, ',', '.') }}</td>
                </tr>
                @for($i = 1; $i < $rowspan; $i++)
                <tr>
                    <td>{{ $pembelian->details[$i]->bahanBaku->nama_bahan ?? '-' }}</td>
                    <td>{{ $pembelian->details[$i]->qty }}</td>
                    <td>Rp {{ number_format($pembelian->details[$i]->harga, 0, ',', '.') }}</td>
                </tr>
                @endfor

                @empty

                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi pembelian untuk periode ini.</td>
                </tr>

                @endforelse

            </tbody>

            <tfoot>

                <tr>
                    <th colspan="6" class="text-right">TOTAL</th>
                    <th>Rp {{ number_format($pembelians->sum(function($p){ return $p->grand_total ?? (($p->subtotal ?: $p->qty * $p->harga) - ($p->diskon ?? 0) + ($p->ongkir ?? 0)); }), 0, ',', '.') }}</th>
                </tr>

            </tfoot>

        </table>

    </div>

</div>

@stop