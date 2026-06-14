@extends('adminlte::page')

@section('title', 'Jurnal Umum')

@section('content_header')
    <h1>Jurnal Umum</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <div class="text-center mb-4">

            <h3>
                JURNAL UMUM
            </h3>

            <h5>

                Periode:
                {{ request('periode_awal') ?? '-' }}
                s/d
                {{ request('periode_akhir') ?? '-' }}

            </h5>

        </div>

        <form method="GET">

            <div class="row">

                <div class="col-md-4">

                    <label>Periode Awal</label>

                    <input type="date"
                           name="periode_awal"
                           class="form-control"
                           value="{{ request('periode_awal') }}">

                </div>

                <div class="col-md-4">

                    <label>Periode Akhir</label>

                    <input type="date"
                           name="periode_akhir"
                           class="form-control"
                           value="{{ request('periode_akhir') }}">

                </div>

                <div class="col-md-4">

                    <br>

                    <button type="submit"
                            class="btn btn-primary">

                        Filter

                    </button>

                    <a href="{{ url('jurnal-umum/export') }}?periode_awal={{ request('periode_awal') }}&periode_akhir={{ request('periode_akhir') }}"
                       class="btn btn-success">

                        Download Excel

                    </a>

                    <a href="{{ url('jurnal-umum/print') }}?periode_awal={{ request('periode_awal') }}&periode_akhir={{ request('periode_akhir') }}"
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
                    <th>No Bukti</th>
                    <th>Keterangan</th>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th>Debit</th>
                    <th>Kredit</th>

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
                        {{ $jurnal['no_bukti'] }}
                    </td>

                    <td>
                        {{ $jurnal['keterangan'] }}
                    </td>

                    <td>
                        {{ $jurnal['kode_akun'] }}
                    </td>

                    <td>
                        {{ $jurnal['nama_akun'] }}
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

                </tr>

                @endforeach

            </tbody>

            <tfoot>

                <tr>

                    <th colspan="6" class="text-center">

                        TOTAL

                    </th>

                    <th>

                        Rp {{ number_format($totalDebit, 0, ',', '.') }}

                    </th>

                    <th>

                        Rp {{ number_format($totalKredit, 0, ',', '.') }}

                    </th>

                </tr>

            </tfoot>

        </table>

    </div>

</div>

@stop