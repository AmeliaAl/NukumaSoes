@extends('adminlte::page')

@section('title', 'Jurnal Umum')

@section('content_header')
    <h1>Jurnal Umum</h1>
@stop

@section('content')

{{-- ── Filter (satu input bulan/tahun) ── --}}
@php
    // Derive bulan yang aktif dari parameter yang ada
    $activePeriode = request('periode')
        ?? (request('periode_awal') ? substr(request('periode_awal'), 0, 7) : null);
@endphp

<div class="card mb-3">
    <div class="card-body py-3">
        {{-- Form ini TIDAK di-submit langsung ke server.
             JS akan mengubah nilai bulan menjadi periode_awal & periode_akhir. --}}
        <form method="GET" id="form-filter">
            {{-- Hidden fields yang dikirim ke controller --}}
            <input type="hidden" name="periode_awal"  id="hidden_periode_awal"
                   value="{{ request('periode_awal') }}">
            <input type="hidden" name="periode_akhir" id="hidden_periode_akhir"
                   value="{{ request('periode_akhir') }}">

            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Pilih Periode</label>
                    <input type="month" id="input_periode" class="form-control"
                           value="{{ $activePeriode }}">
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter Data
                    </button>
                    <a href="#" id="btn-download-pdf" class="btn btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Card Header Perusahaan ── --}}
<div class="card mb-3" style="border-radius:10px; overflow:hidden; border:none; box-shadow:0 2px 8px rgba(0,0,0,.1);">
    <div style="background:#dee2e6; padding:24px 20px; text-align:center;">
        <div style="color:#212529; font-size:20px; font-weight:700; letter-spacing:1px; text-transform:uppercase;">
            Nukuma Soes
        </div>
        <div style="color:#495057; font-size:14px; font-weight:600; margin-top:3px;">
            Jurnal Umum
        </div>
        <div style="color:#6c757d; font-size:11px; margin-top:3px;">
            @if(request('periode_awal') && request('periode_akhir'))
                Periode:
                {{ \Carbon\Carbon::parse(request('periode_awal'))->translatedFormat('d F Y') }}
                &ndash;
                {{ \Carbon\Carbon::parse(request('periode_akhir'))->translatedFormat('d F Y') }}
            @elseif($activePeriode)
                Periode: {{ \Carbon\Carbon::parse($activePeriode . '-01')->translatedFormat('F Y') }}
            @else
                Semua Periode
            @endif
        </div>
    </div>
</div>

{{-- ── Tabel Jurnal ── --}}
<div class="card" style="border-radius:10px; overflow:hidden; border:none; box-shadow:0 2px 8px rgba(0,0,0,.08);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="font-size:13px;">
                <thead>
                    <tr class="thead-light">
                        <th class="text-center align-middle" style="width:110px;">Tanggal</th>
                        <th class="align-middle">Akun</th>
                        <th class="text-center align-middle" style="width:80px;">Ref</th>
                        <th class="text-right align-middle" style="width:150px;">Debit</th>
                        <th class="text-right align-middle" style="width:150px;">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedJurnals = collect($jurnals)->groupBy('no_bukti');
                    @endphp

                    @forelse($groupedJurnals as $noBukti => $rows)
                        @php $firstRow = $rows->first(); @endphp

                        @foreach($rows as $i => $row)
                        <tr>
                            {{-- Tanggal pakai rowspan, muncul sekali per grup --}}
                            @if($i === 0)
                            <td class="text-center align-middle" rowspan="{{ $rows->count() }}"
                                style="white-space:nowrap; vertical-align:middle;">
                                {{ \Carbon\Carbon::parse($firstRow['tanggal'])->format('d/m/Y') }}
                            </td>
                            @endif

                            {{-- Akun: debit normal, kredit indent + italic --}}
                            <td class="align-middle"
                                style="{{ $row['kredit'] > 0 ? 'padding-left:36px; font-style:italic; color:#555;' : 'padding-left:14px;' }}">
                                {{ $row['nama_akun'] }}
                            </td>

                            {{-- Ref: kode akun --}}
                            <td class="text-center align-middle" style="color:#6b7280; font-size:12px;">
                                {{ $row['kode_akun'] }}
                            </td>

                            {{-- Debit --}}
                            <td class="text-right align-middle" style="color:#16a34a; font-weight:500;">
                                @if($row['debit'] > 0)
                                    {{ \App\Helpers\FormatHelper::rupiah($row['debit']) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Kredit --}}
                            <td class="text-right align-middle" style="color:#dc2626; font-weight:500;">
                                @if($row['kredit'] > 0)
                                    {{ \App\Helpers\FormatHelper::rupiah($row['kredit']) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x d-block mb-2" style="color:#ced4da;"></i>
                                Belum ada data jurnal untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($jurnals) > 0)
                <tfoot>
                    <tr style="background:#f8f9fa; border-top:2px solid #dee2e6;">
                        <td colspan="3" class="text-right align-middle font-weight-bold"
                            style="color:#212529; font-size:13px; padding:10px 14px;">
                            TOTAL
                        </td>
                        <td class="text-right align-middle font-weight-bold"
                            style="color:#16a34a; font-size:13px; padding:10px 14px;">
                            {{ \App\Helpers\FormatHelper::rupiah($totalDebit) }}
                        </td>
                        <td class="text-right align-middle font-weight-bold"
                            style="color:#dc2626; font-size:13px; padding:10px 14px;">
                            {{ \App\Helpers\FormatHelper::rupiah($totalKredit) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-center py-2" style="border-top:none;">
                            @if($totalDebit == $totalKredit)
                                <span style="background:#dcfce7; color:#16a34a; padding:5px 18px;
                                             border-radius:20px; font-size:12px; font-weight:600;">
                                    <i class="fas fa-check-circle mr-1"></i> Jurnal Seimbang
                                </span>
                            @else
                                <span style="background:#fee2e2; color:#dc2626; padding:5px 18px;
                                             border-radius:20px; font-size:12px; font-weight:600;">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Jurnal Tidak Seimbang &mdash;
                                    Selisih: {{ \App\Helpers\FormatHelper::rupiah(abs($totalDebit - $totalKredit)) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
$(document).ready(function () {

    var $input    = $('#input_periode');
    var $form     = $('#form-filter');
    var $btnPdf   = $('#btn-download-pdf');
    var $hiddenA  = $('#hidden_periode_awal');
    var $hiddenZ  = $('#hidden_periode_akhir');

    // Set PDF link setiap kali halaman dimuat
    function updatePdfLink() {
        var val = $input.val();
        if (val) {
            var parts = val.split('-');
            var y = parseInt(parts[0]);
            var m = parseInt(parts[1]);
            // Hari terakhir bulan
            var lastDay = new Date(y, m, 0).getDate();
            var awal  = val + '-01';
            var akhir = val + '-' + String(lastDay).padStart(2, '0');
            $hiddenA.val(awal);
            $hiddenZ.val(akhir);
            $btnPdf.attr('href',
                '{{ url("jurnal-umum/print") }}?periode_awal=' + awal + '&periode_akhir=' + akhir
            );
        } else {
            $hiddenA.val('');
            $hiddenZ.val('');
            $btnPdf.attr('href', '{{ url("jurnal-umum/print") }}');
        }
    }

    // Jalankan saat input berubah
    $input.on('change', function () {
        updatePdfLink();
    });

    // Jalankan saat submit agar hidden fields terisi sebelum form dikirim
    $form.on('submit', function () {
        updatePdfLink();
    });

    // Init saat load
    updatePdfLink();
});
</script>
@stop
