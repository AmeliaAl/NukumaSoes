@extends('adminlte::page')

@section('title', 'Buku Besar')

@section('content_header')
    <h1>Buku Besar</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-4">

                    <label>Pilih Akun</label>

                    <select name="akun_id"
                            class="form-control">

                        <option value="">
                            -- Pilih Akun --
                        </option>

                        @foreach($coas as $coa)

                        <option value="{{ $coa->kode_akun }}"
                            {{ request('akun_id') == $coa->kode_akun ? 'selected' : '' }}>

                            {{ $coa->kode_akun }}
                            -
                            {{ $coa->nama_akun }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <label>Periode Awal</label>

                    <input type="date"
                           name="periode_awal"
                           class="form-control"
                           value="{{ request('periode_awal') }}">

                </div>

                <div class="col-md-3">

                    <label>Periode Akhir</label>

                    <input type="date"
                           name="periode_akhir"
                           class="form-control"
                           value="{{ request('periode_akhir') }}">

                </div>

                <div class="col-md-2">

                    <br>

                    <button type="submit"
                            class="btn btn-primary">

                        Filter

                    </button>

                    <a href="{{ url('buku-besar/export') }}?akun_id={{ request('akun_id') }}&periode_awal={{ request('periode_awal') }}&periode_akhir={{ request('periode_akhir') }}"
                       class="btn btn-success">

                        Download Excel

                    </a>

                    <a href="{{ url('buku-besar/print') }}?akun_id={{ request('akun_id') }}&periode_awal={{ request('periode_awal') }}&periode_akhir={{ request('periode_akhir') }}"
                       class="btn btn-danger">

                        Download PDF

                    </a>

                </div>

            </div>

        </form>

        <hr>

        @if($akun)

        <div class="text-center mb-4">

            <h3>
                BUKU BESAR
            </h3>

            <h5>

                Periode:
                {{ $periodeAwal ?? '-' }}
                s/d
                {{ $periodeAkhir ?? '-' }}

            </h5>

            <h5>

                Kode Akun :
                {{ $akun->kode_akun }}

            </h5>

            <h5>

                Nama Akun :
                {{ $akun->nama_akun }}

            </h5>

        </div>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Bukti</th>
                    <th>Keterangan</th>
                    <th>Ref</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                    <th>Saldo</th>

                </tr>

            </thead>

            <tbody>

                @foreach($jurnals as $jurnal)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $jurnal['tanggal'] }}
                    </td>

                    <td>
                        {{ $jurnal['bukti'] }}
                    </td>

                    <td>
                        {{ $jurnal['keterangan'] }}
                    </td>

                    <td>
                        {{ $jurnal['ref'] }}
                    </td>

                    <td>

                        @if($jurnal['debit'] > 0)

                            Rp {{ number_format($jurnal['debit'], 0, ',', '.') }}

                        @endif

                    </td>

                    <td>

                        @if($jurnal['kredit'] > 0)

                            Rp {{ number_format($jurnal['kredit'], 0, ',', '.') }}

                        @endif

                    </td>

                    <td>

                        @if($jurnal['saldo'] < 0)

                            (Rp {{ number_format(abs($jurnal['saldo']), 0, ',', '.') }})

                        @else

                            Rp {{ number_format($jurnal['saldo'], 0, ',', '.') }}

                        @endif

                    </td>

                </tr>

                @endforeach

            </tbody>

            <tfoot>

                <tr>

                    <th colspan="5">

                        TOTAL

                    </th>

                    <th>

                        Rp {{ number_format($totalDebit, 0, ',', '.') }}

                    </th>

                    <th>

                        Rp {{ number_format($totalKredit, 0, ',', '.') }}

                    </th>

                    <th>

                        @if($saldoAkhir < 0)

                            (Rp {{ number_format(abs($saldoAkhir), 0, ',', '.') }})

                        @else

                            Rp {{ number_format($saldoAkhir, 0, ',', '.') }}

                        @endif

                    </th>

                </tr>

            </tfoot>

        </table>

        @endif

    </div>

</div>

@stop