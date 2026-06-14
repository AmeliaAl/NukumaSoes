@extends('adminlte::page')

@section('title', 'Laporan Overhead')

@section('content_header')
    <h1>Laporan Overhead</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <div class="text-center mb-4">

            <h3>
                LAPORAN OVERHEAD
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

                    <a href="{{ url('laporan-overhead/print') }}?tanggal_awal={{ request('tanggal_awal') }}&tanggal_akhir={{ request('tanggal_akhir') }}"
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
                    <th>Akun Overhead</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>

                </tr>

            </thead>

            <tbody>

                @foreach($overheads as $overhead)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $overhead->tanggal }}
                    </td>

                    <td>
                        {{ $overhead->coa->nama_akun }}
                    </td>

                    <td>
                        {{ $overhead->keterangan }}
                    </td>

                    <td>

                        Rp {{ number_format($overhead->nominal, 0, ',', '.') }}

                    </td>

                </tr>

                @endforeach

            </tbody>
            <tfoot>

            <tr>

                <th colspan="4" class="text-center">

                    TOTAL

                </th>

                <th>

                    Rp {{ number_format($total, 0, ',', '.') }}

                </th>

            </tr>

        </tfoot>

        </table>


    </div>

</div>

@stop