@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

<form method="GET" class="mb-3">

    <div class="row">

        <div class="col-md-3">

            <label>Pilih Tahun</label>

            <select name="tahun"
                    class="form-control">

                @for($i = date('Y'); $i >= 2026; $i--)

                    <option value="{{ $i }}"
                        {{ $tahun == $i ? 'selected' : '' }}>

                        {{ $i }}

                    </option>

                @endfor

            </select>

        </div>

        <div class="col-md-2">

            <br>

            <button type="submit"
                    class="btn btn-primary">

                Filter

            </button>

        </div>

    </div>

</form>

<div class="row">

    <div class="col-lg-3 col-6">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>

                    {{ \App\Helpers\FormatHelper::rupiah($totalPembelian) }}

                </h3>

                <p>
                    Total Pembelian
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-shopping-cart"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    {{ \App\Helpers\FormatHelper::rupiah($totalOverhead) }}

                </h3>

                <p>
                    Total Overhead
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-wallet"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-6">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>

                    {{ $totalTransaksi }}

                </h3>

                <p>
                    Total Transaksi
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-file-invoice"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-6">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>

                    {{ $jumlahSupplier }}

                </h3>

                <p>
                    Jumlah Supplier
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-users"></i>

            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Grafik Pembelian Per Bulan Tahun {{ $tahun }}

        </h3>

    </div>

    <div class="card-body">

        <canvas id="grafikPembelian"></canvas>

    </div>

</div>

@stop

@section('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('grafikPembelian');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: @json($bulanLabel),

        datasets: [{

            label: 'Total Pembelian',

            data: @json($grafikPembelian),

            borderWidth: 1

        }]

    },

    options: {

        responsive: true,

        scales: {

            y: {

                beginAtZero: true,

                ticks: {

                    callback: function(value) {

                        return 'Rp ' + value.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                    }

                }

            }

        },

        plugins: {

            tooltip: {

                callbacks: {

                    label: function(context) {

                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                    }

                }

            }

        }

    }

});

</script>

@stop