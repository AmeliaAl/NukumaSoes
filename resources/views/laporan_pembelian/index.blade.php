@extends('adminlte::page')

@section('title', 'Laporan Pembelian')

@section('content_header')
    <h1>Laporan Pembelian</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        {{-- Judul --}}
        <div class="text-center mb-3">
            <h4 class="font-weight-bold">LAPORAN PEMBELIAN</h4>
            <p class="mb-0">
                Periode:
                {{ request('tanggal_awal') ? \Carbon\Carbon::parse(request('tanggal_awal'))->format('d/m/Y') : '-' }}
                s/d
                {{ request('tanggal_akhir') ? \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d/m/Y') : '-' }}
            </p>
        </div>

        {{-- Filter --}}
        <form method="GET" class="mb-3">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label>Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control"
                           value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-4">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control"
                           value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ url('laporan-pembelian/print') }}?tanggal_awal={{ request('tanggal_awal') }}&tanggal_akhir={{ request('tanggal_akhir') }}"
                       class="btn btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </form>

        <hr>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Bahan Baku</th>
                        <th class="text-center">Qty Pembelian</th>
                        <th class="text-center">Isi/Kemasan</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembelians as $pembelian)
                    @php $rowspan = max(1, $pembelian->details->count()); @endphp
                    <tr>
                        <td rowspan="{{ $rowspan }}" class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td rowspan="{{ $rowspan }}" class="align-middle">{{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y') }}</td>
                        <td rowspan="{{ $rowspan }}" class="align-middle">{{ $pembelian->supplier->nama_supplier }}</td>

                        @if($pembelian->details->count() > 0)
                            <td>{{ $pembelian->details[0]->bahanBaku->nama_bahan ?? '-' }}</td>
                            <td class="text-center">{{ $pembelian->details[0]->qty }}</td>
                            <td class="text-center">{{ $pembelian->details[0]->isi_per_kemasan ?? '-' }}</td>
                            <td class="text-center">{{ $pembelian->details[0]->bahanBaku->satuan ?? '-' }}</td>
                            <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($pembelian->details[0]->harga) }}</td>
                            <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($pembelian->details[0]->subtotal) }}</td>
                        @else
                            <td>-</td><td class="text-center">0</td><td>-</td><td>-</td>
                            <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah(0) }}</td>
                        @endif
                    </tr>
                    @for($i = 1; $i < $rowspan; $i++)
                    <tr>
                        <td>{{ $pembelian->details[$i]->bahanBaku->nama_bahan ?? '-' }}</td>
                        <td class="text-center">{{ $pembelian->details[$i]->qty }}</td>
                        <td class="text-center">{{ $pembelian->details[$i]->isi_per_kemasan ?? '-' }}</td>
                        <td class="text-center">{{ $pembelian->details[$i]->bahanBaku->satuan ?? '-' }}</td>
                        <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($pembelian->details[$i]->harga) }}</td>
                        <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($pembelian->details[$i]->subtotal) }}</td>
                    </tr>
                    @endfor

                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">
                            Tidak ada transaksi pembelian untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if($pembelians->count() > 0)
                <tfoot>
                    <tr class="table-active">
                        <th colspan="8" class="text-right font-weight-bold">Total Pembelian</th>
                        <th class="text-right font-weight-bold">{{ \App\Helpers\FormatHelper::rupiah($summary['subtotal']) }}</th>
                    </tr>
                </tfoot>
                @endif

            </table>
        </div>

    </div>
</div>

@stop
