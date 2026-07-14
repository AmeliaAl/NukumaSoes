@extends('adminlte::page')

@section('title', 'Tambah Overhead')

@section('content_header')
    <h1>Tambah Overhead</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('overhead.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- Tanggal Transaksi --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal"
                               class="form-control @error('tanggal') is-invalid @enderror"
                               required value="{{ old('tanggal', date('Y-m-d')) }}">
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Jenis Periode --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Periode <span class="text-danger">*</span></label>
                        <select name="jenis_periode" id="jenis_periode"
                                class="form-control @error('jenis_periode') is-invalid @enderror" required>
                            <option value="harian"   {{ old('jenis_periode','harian') === 'harian'   ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ old('jenis_periode') === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan"  {{ old('jenis_periode') === 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                        </select>
                        @error('jenis_periode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Periode Harian --}}
            <div class="row" id="row-harian">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Pembebanan <span class="text-danger">*</span></label>
                        <input type="date" name="periode_mulai" id="periode_mulai_harian"
                               class="form-control @error('periode_mulai') is-invalid @enderror"
                               value="{{ old('periode_mulai', date('Y-m-d')) }}">
                        @error('periode_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Sama dengan tanggal transaksi.</small>
                    </div>
                </div>
            </div>

            {{-- Periode Mingguan --}}
            <div class="row d-none" id="row-mingguan">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="periode_mulai_mingguan" id="periode_mulai_mingguan"
                               class="form-control" value="{{ old('periode_mulai') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Akhir <span class="text-danger">*</span></label>
                        <input type="date" name="periode_akhir" id="periode_akhir"
                               class="form-control @error('periode_akhir') is-invalid @enderror"
                               value="{{ old('periode_akhir') }}">
                        @error('periode_akhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Periode Bulanan --}}
            <div class="row d-none" id="row-bulanan">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bulan <span class="text-danger">*</span></label>
                        <select name="bulan_pembebanan" id="bulan_pembebanan" class="form-control">
                            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $nama)
                            <option value="{{ $num }}" {{ date('m') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Tahun <span class="text-danger">*</span></label>
                        <select name="tahun_pembebanan" id="tahun_pembebanan" class="form-control">
                            @for($y = date('Y'); $y >= 2026; $y--)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            {{-- Hidden input: periode_mulai final --}}
            <input type="hidden" name="periode_mulai" id="periode_mulai_final" value="{{ old('periode_mulai', date('Y-m-d')) }}">

            <hr>
            <h5>Detail Overhead</h5>

            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Akun Overhead <span class="text-danger">*</span></label>
                        <select name="details[0][coa_id]"
                                class="form-control @error('details.0.coa_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->id }}" {{ old('details.0.coa_id') == $coa->id ? 'selected' : '' }}>
                                {{ $coa->no_akun }} - {{ $coa->nama_akun }}
                            </option>
                            @endforeach
                        </select>
                        @error('details.0.coa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Akun Pembayaran <span class="text-danger">*</span></label>
                        <select name="details[0][coa_pembayaran_id]"
                                class="form-control @error('details.0.coa_pembayaran_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Akun Pembayaran --</option>
                            @foreach($paymentCoas as $coa)
                            <option value="{{ $coa->id }}" {{ old('details.0.coa_pembayaran_id') == $coa->id ? 'selected' : '' }}>
                                {{ $coa->no_akun }} - {{ $coa->nama_akun }}
                            </option>
                            @endforeach
                        </select>
                        @error('details.0.coa_pembayaran_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Keterangan <span class="text-danger">*</span></label>
                        <textarea name="details[0][keterangan]"
                                  class="form-control @error('details.0.keterangan') is-invalid @enderror"
                                  rows="2" required>{{ old('details.0.keterangan') }}</textarea>
                        @error('details.0.keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Nominal <span class="text-danger">*</span></label>
                        <input type="text" name="details[0][nominal]" id="nominal_input"
                               class="form-control @error('details.0.nominal') is-invalid @enderror"
                               value="{{ old('details.0.nominal') }}" required>
                        @error('details.0.nominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            <x-upload-zone
                input-id="upload_overhead_create"
                input-name="payment_proofs[]"
                label="Dokumen Pembayaran"
                :max-files="5"
            />

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ url('overhead') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

@stop

@section('js')
<script>
function formatRupiah(value) {
    if (!value) return '';
    return new Intl.NumberFormat('id-ID').format(value.toString().replace(/\D/g, ''));
}

function lastDayOfMonth(year, month) {
    return new Date(year, parseInt(month), 0).getDate();
}

function updatePeriodeFromBulanan() {
    var bulan = $('#bulan_pembebanan').val();
    var tahun = $('#tahun_pembebanan').val();
    if (!bulan || !tahun) return;
    var last  = lastDayOfMonth(parseInt(tahun), bulan);
    var mulai = tahun + '-' + bulan + '-01';
    var akhir = tahun + '-' + bulan + '-' + String(last).padStart(2, '0');
    $('#periode_mulai_final').val(mulai);
    $('input[name="periode_akhir"]').val(akhir);
}

function switchPeriode(jenis) {
    $('#row-harian, #row-mingguan, #row-bulanan').addClass('d-none');
    $('#periode_mulai_harian, #periode_mulai_mingguan').removeAttr('required');
    $('input[name="periode_akhir"]').removeAttr('required');

    if (jenis === 'harian') {
        $('#row-harian').removeClass('d-none');
        $('#periode_mulai_harian').attr('required', true);
        $('#periode_mulai_harian').off('change').on('change', function() {
            $('#periode_mulai_final').val($(this).val());
        }).trigger('change');
    } else if (jenis === 'mingguan') {
        $('#row-mingguan').removeClass('d-none');
        $('#periode_mulai_mingguan').attr('required', true);
        $('input[name="periode_akhir"]').attr('required', true);
        $('#periode_mulai_mingguan').off('change').on('change', function() {
            $('#periode_mulai_final').val($(this).val());
        });
    } else if (jenis === 'bulanan') {
        $('#row-bulanan').removeClass('d-none');
        updatePeriodeFromBulanan();
    }
}

$(document).ready(function () {
    switchPeriode($('#jenis_periode').val());

    $('#jenis_periode').on('change', function () { switchPeriode($(this).val()); });
    $('#bulan_pembebanan, #tahun_pembebanan').on('change', updatePeriodeFromBulanan);

    // Format nominal saat input
    $(document).on('input', '#nominal_input', function () {
        $(this).val(formatRupiah($(this).val()));
    });

    // Validasi sebelum submit
    $('form').on('submit', function (e) {
        var jenis = $('#jenis_periode').val();

        if (jenis === 'harian') {
            if (!$('#periode_mulai_harian').val()) {
                alert('Tanggal pembebanan wajib diisi.');
                e.preventDefault(); return false;
            }
            $('#periode_mulai_final').val($('#periode_mulai_harian').val());
            // Nonaktifkan (bukan hapus name) agar tidak ada dua field periode_mulai terkirim
            $('#periode_mulai_mingguan').prop('disabled', true);

        } else if (jenis === 'mingguan') {
            var mulai = $('#periode_mulai_mingguan').val();
            var akhir = $('input[name="periode_akhir"]').val();
            if (!mulai) { alert('Tanggal mulai wajib diisi.'); e.preventDefault(); return false; }
            if (!akhir) { alert('Tanggal akhir wajib diisi.'); e.preventDefault(); return false; }
            if (akhir < mulai) { alert('Tanggal akhir tidak boleh sebelum tanggal mulai.'); e.preventDefault(); return false; }
            $('#periode_mulai_final').val(mulai);
            $('#periode_mulai_harian').prop('disabled', true);

        } else if (jenis === 'bulanan') {
            updatePeriodeFromBulanan();
            if (!$('#periode_mulai_final').val()) {
                alert('Silakan pilih bulan dan tahun.');
                e.preventDefault(); return false;
            }
            $('#periode_mulai_harian, #periode_mulai_mingguan').prop('disabled', true);
        }

        // Validasi nominal: strip format rupiah lalu cek > 0
        var rawNominal = parseInt($('#nominal_input').val().replace(/\./g, '').replace(/,/g, '')) || 0;
        if (rawNominal <= 0) {
            $('#nominal_input').addClass('is-invalid');
            alert('Nominal harus lebih besar dari 0.');
            // Restore disabled sebelum return agar data tidak hilang saat halaman tidak refresh
            $('#periode_mulai_harian, #periode_mulai_mingguan').prop('disabled', false);
            e.preventDefault(); return false;
        }
        $('#nominal_input').removeClass('is-invalid');
    });
});
</script>
@include('partials.upload-zone-js')
@stop
