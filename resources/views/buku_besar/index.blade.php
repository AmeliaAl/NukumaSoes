@extends('adminlte::page')

@section('title', 'Buku Besar')

@section('content_header')
    <h1>Buku Besar</h1>
@stop

@section('content')

{{-- ── Filter ── --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" id="form-filter-bb">
            <input type="hidden" name="periode_awal"  id="bb_hidden_awal"
                   value="{{ request('periode_awal') }}">
            <input type="hidden" name="periode_akhir" id="bb_hidden_akhir"
                   value="{{ request('periode_akhir') }}">

            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Pilih Periode</label>
                    <input type="month" id="bb_input_periode" class="form-control"
                           value="{{ request('periode_awal') ? substr(request('periode_awal'), 0, 7) : '' }}">
                </div>
                <div class="col-md-4">
                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Pilih Akun</label>
                    <select name="akun_id" class="form-control">
                        <option value="">-- Pilih Akun --</option>
                        @foreach($coas as $coa)
                        <option value="{{ $coa->no_akun }}"
                            {{ request('akun_id') == $coa->no_akun ? 'selected' : '' }}>
                            {{ $coa->no_akun }} - {{ $coa->nama_akun }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter Data
                    </button>
                    <a href="#" id="btn-download-pdf-bb" class="btn btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if($akun)

{{-- ── Card Header Perusahaan ── --}}
<div class="card mb-3" style="border-radius:10px; overflow:hidden; border:none; box-shadow:0 2px 8px rgba(0,0,0,.1);">
    <div style="background:#dee2e6; padding:24px 20px; text-align:center;">
        <div style="color:#212529; font-size:20px; font-weight:700; letter-spacing:1px; text-transform:uppercase;">
            Nukuma Soes
        </div>
        <div style="color:#495057; font-size:14px; font-weight:600; margin-top:3px;">
            Buku Besar
        </div>
        <div style="color:#6c757d; font-size:11px; margin-top:3px;">
            Periode:
            {{ $periodeAwal  ? \Carbon\Carbon::parse($periodeAwal)->translatedFormat('d F Y')  : '-' }}
            &ndash;
            {{ $periodeAkhir ? \Carbon\Carbon::parse($periodeAkhir)->translatedFormat('d F Y') : '-' }}
        </div>
    </div>
</div>

{{-- ── Badge Akun Terpilih ── --}}
<div class="card mb-3" style="border-radius:10px; border:1px solid #dee2e6; background:#f8f9fa;">
    <div class="card-body py-2 px-4">
        <span style="color:#495057; font-weight:600; font-size:13px;">
            <i class="fas fa-bookmark mr-1"></i>
            Akun Terpilih: {{ $akun->no_akun }} - {{ $akun->nama_akun }}
        </span>
    </div>
</div>

{{-- ── Tabel Buku Besar ── --}}

<div class="card" style="border-radius:10px; overflow:hidden; border:none; box-shadow:0 2px 8px rgba(0,0,0,.08);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="font-size:13px;">
                <thead>
                    <tr class="thead-light">
                        <th rowspan="2" class="text-center align-middle" style="width:105px; vertical-align:middle;">Tanggal</th>
                        <th rowspan="2" class="align-middle" style="vertical-align:middle;">Keterangan</th>
                        <th rowspan="2" class="text-right align-middle" style="width:130px; vertical-align:middle;">Debit</th>
                        <th rowspan="2" class="text-right align-middle" style="width:130px; vertical-align:middle;">Kredit</th>
                        <th colspan="2" class="text-center align-middle">Saldo</th>
                    </tr>
                    <tr class="thead-light">
                        <th class="text-center" style="width:155px;">Debit</th>
                        <th class="text-center" style="width:155px;">Kredit</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- ── Baris SALDO AWAL ── --}}
                    <tr style="background:#f8f9fa;">
                        <td colspan="2" class="text-center align-middle font-weight-bold"
                            style="color:#212529; padding:8px 14px;">
                            SALDO AWAL
                        </td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        @if($saldoAwalNominal > 0)
                            <td class="text-right align-middle font-weight-bold" style="color:#212529;">
                                {{ \App\Helpers\FormatHelper::rupiah($saldoAwalNominal) }}
                            </td>
                            <td class="text-center text-muted">-</td>
                        @elseif($saldoAwalNominal < 0)
                            <td class="text-center text-muted">-</td>
                            <td class="text-right align-middle font-weight-bold" style="color:#212529;">
                                {{ \App\Helpers\FormatHelper::rupiah(abs($saldoAwalNominal)) }}
                            </td>
                        @else
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                        @endif
                    </tr>

                    {{-- ── Baris Transaksi ── --}}
                    {{-- Saldo berjalan sudah include saldo awal (dihitung di controller) --}}
                    @foreach($jurnals as $index => $jurnal)
                    @php $saldoBerjalan = $jurnal['saldo']; @endphp
                    <tr>
                        <td class="text-center align-middle">
                            {{ \Carbon\Carbon::parse($jurnal['tanggal'])->format('d/m/Y') }}
                        </td>
                        <td class="align-middle">{{ $jurnal['keterangan'] }}</td>
                        <td class="text-right align-middle" style="color:#16a34a; font-weight:500;">
                            @if($jurnal['debit'] > 0)
                                {{ \App\Helpers\FormatHelper::rupiah($jurnal['debit']) }}
                            @else
                                <span class="text-muted">Rp 0</span>
                            @endif
                        </td>
                        <td class="text-right align-middle" style="color:#dc2626; font-weight:500;">
                            @if($jurnal['kredit'] > 0)
                                {{ \App\Helpers\FormatHelper::rupiah($jurnal['kredit']) }}
                            @else
                                <span class="text-muted">Rp 0</span>
                            @endif
                        </td>
                        @if($saldoBerjalan >= 0)
                            <td class="text-right align-middle font-weight-bold" style="color:#212529;">
                                {{ \App\Helpers\FormatHelper::rupiah($saldoBerjalan) }}
                            </td>
                            <td class="text-center text-muted">-</td>
                        @else
                            <td class="text-center text-muted">-</td>
                            <td class="text-right align-middle font-weight-bold" style="color:#212529;">
                                {{ \App\Helpers\FormatHelper::rupiah(abs($saldoBerjalan)) }}
                            </td>
                        @endif
                    </tr>
                    @endforeach

                </tbody>

                {{-- ── Baris SALDO AKHIR — selalu tampil jika akun dipilih ── --}}
                <tfoot>
                    <tr style="background:#f8f9fa; border-top:2px solid #dee2e6;">
                        <td colspan="2" class="text-center font-weight-bold align-middle"
                            style="color:#212529; font-size:13px; letter-spacing:.3px; padding:10px 14px;">
                            SALDO AKHIR
                        </td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        @if($saldoAkhir >= 0)
                            <td class="text-right align-middle font-weight-bold"
                                style="color:#212529; font-size:13px; padding:10px 8px;">
                                {{ \App\Helpers\FormatHelper::rupiah($saldoAkhir) }}
                            </td>
                            <td class="text-center text-muted">-</td>
                        @else
                            <td class="text-center text-muted">-</td>
                            <td class="text-right align-middle font-weight-bold"
                                style="color:#212529; font-size:13px; padding:10px 8px;">
                                {{ \App\Helpers\FormatHelper::rupiah(abs($saldoAkhir)) }}
                            </td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@else

<div class="card" style="border-radius:10px; border:none; box-shadow:0 2px 8px rgba(0,0,0,.06);">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-book-open fa-3x mb-3" style="color:#ced4da;"></i>
        <p class="mb-0" style="font-size:15px;">Pilih akun dan periode untuk menampilkan Buku Besar.</p>
    </div>
</div>

@endif

@stop

@section('js')
<script>
$(document).ready(function () {
    var $input   = $('#bb_input_periode');
    var $form    = $('#form-filter-bb');
    var $hiddenA = $('#bb_hidden_awal');
    var $hiddenZ = $('#bb_hidden_akhir');
    var $btnPdf  = $('#btn-download-pdf-bb');
    var akunId   = '{{ request('akun_id') }}';

    function getRange(val) {
        if (!val) return { awal: '', akhir: '' };
        var parts   = val.split('-');
        var y       = parseInt(parts[0]);
        var m       = parseInt(parts[1]);
        var lastDay = new Date(y, m, 0).getDate();
        return {
            awal:  val + '-01',
            akhir: val + '-' + String(lastDay).padStart(2, '0')
        };
    }

    function update() {
        var range        = getRange($input.val());
        var selectedAkun = $('select[name="akun_id"]').val() || akunId;

        $hiddenA.val(range.awal);
        $hiddenZ.val(range.akhir);

        $btnPdf.attr('href',
            '{{ url("buku-besar/print") }}'
            + '?akun_id='       + encodeURIComponent(selectedAkun)
            + '&periode_awal='  + range.awal
            + '&periode_akhir=' + range.akhir
        );
    }

    $input.on('change', update);
    $('select[name="akun_id"]').on('change', update);
    $form.on('submit', function () { update(); });

    update();
});
</script>
@stop
