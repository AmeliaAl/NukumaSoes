@extends('adminlte::page')

@section('title', 'Edit Overhead')

@section('content_header')
    <h1>Edit Overhead</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('overhead.update', $overhead->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Transaksi</label>
                        <input type="date" name="tanggal" class="form-control" required value="{{ $overhead->tanggal }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Periode <span class="text-danger">*</span></label>
                        <select name="jenis_periode" id="jenis_periode" class="form-control" required>
                            <option value="harian"   {{ ($overhead->jenis_periode ?? 'harian') === 'harian'   ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ ($overhead->jenis_periode ?? '') === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan"  {{ ($overhead->jenis_periode ?? '') === 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Periode Harian --}}
            <div class="row" id="row-harian" style="{{ ($overhead->jenis_periode ?? 'harian') !== 'harian' ? 'display:none;' : '' }}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Pembebanan</label>
                        <input type="date" name="periode_mulai" id="periode_mulai_harian" class="form-control"
                               value="{{ $overhead->periode_mulai ?? $overhead->tanggal }}">
                    </div>
                </div>
            </div>

            {{-- Periode Mingguan --}}
            <div class="row" id="row-mingguan" style="{{ ($overhead->jenis_periode ?? 'harian') !== 'mingguan' ? 'display:none;' : '' }}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="periode_mulai_mingguan" id="periode_mulai_mingguan" class="form-control"
                               value="{{ $overhead->jenis_periode === 'mingguan' ? ($overhead->periode_mulai ?? '') : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="periode_akhir" id="periode_akhir" class="form-control"
                               value="{{ $overhead->jenis_periode === 'mingguan' ? ($overhead->periode_akhir ?? '') : '' }}">
                    </div>
                </div>
            </div>

            {{-- Periode Bulanan --}}
            <div class="row" id="row-bulanan" style="{{ ($overhead->jenis_periode ?? 'harian') !== 'bulanan' ? 'display:none;' : '' }}">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bulan</label>
                        <select name="bulan_pembebanan" id="bulan_pembebanan" class="form-control">
                            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $nama)
                            <option value="{{ $num }}"
                                {{ ($overhead->jenis_periode === 'bulanan' && $overhead->periode_mulai && \Carbon\Carbon::parse($overhead->periode_mulai)->format('m') === $num) ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Tahun</label>
                        <select name="tahun_pembebanan" id="tahun_pembebanan" class="form-control">
                            @for($y = date('Y'); $y >= 2026; $y--)
                            <option value="{{ $y }}"
                                {{ ($overhead->jenis_periode === 'bulanan' && $overhead->periode_mulai && \Carbon\Carbon::parse($overhead->periode_mulai)->format('Y') == $y) ? 'selected' : (date('Y') == $y ? 'selected' : '') }}>
                                {{ $y }}
                            </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            {{-- Hidden input: periode_mulai final untuk bulanan --}}
            <input type="hidden" name="periode_mulai" id="periode_mulai_final"
                   value="{{ $overhead->jenis_periode !== 'harian' && $overhead->jenis_periode !== 'mingguan' ? ($overhead->periode_mulai ?? '') : ($overhead->periode_mulai ?? $overhead->tanggal) }}">

            <hr>
            <h4>Detail Item Overhead</h4>
            <table class="table table-bordered" id="table-items">
                <thead>
                    <tr>
                        <th>Akun Overhead</th>
                        <th>Akun Pembayaran</th>
                        <th>Keterangan</th>
                        <th width="200">Nominal</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overhead->details as $index => $detail)
                    <tr>
                        <td>
                            <select name="details[{{ $index }}][coa_id]" class="form-control" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($coas as $coa)
                                <option value="{{ $coa->id }}" {{ $detail->coa_id == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->no_akun }} - {{ $coa->nama_akun }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="details[{{ $index }}][coa_pembayaran_id]" class="form-control" required>
                                <option value="">-- Pilih Akun Pembayaran --</option>
                                @foreach($paymentCoas as $coa)
                                <option value="{{ $coa->id }}" {{ $detail->coa_pembayaran_id == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->no_akun }} - {{ $coa->nama_akun }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea name="details[{{ $index }}][keterangan]" class="form-control" rows="1" required>{{ $detail->keterangan }}</textarea>
                        </td>
                        <td>
                            <input type="text" name="details[{{ $index }}][nominal]" class="form-control nominal-input" value="{{ number_format($detail->nominal, 0, ',', '.') }}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add-item">+ Tambah</button>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <hr>

            <div class="mb-3">
                <h4>Bukti Pembayaran</h4>
                @if($overhead->paymentProofs->count())
                    <ul class="list-group mb-3">
                        @foreach($overhead->paymentProofs as $proof)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ asset('storage/' . $proof->file_path) }}" target="_blank">{{ $proof->file_name }}</a>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteProof({{ $proof->id }}, '{{ route('overhead.delete-proof', $proof->id) }}')">
                                    Hapus
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum ada bukti pembayaran.</p>
                @endif

                <x-upload-zone
                    input-id="upload_overhead_edit"
                    input-name="payment_proofs[]"
                    label="Unggah Dokumen Pembayaran Baru"
                    :max-files="5"
                />
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ url('overhead') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

{{-- Hidden form untuk hapus bukti pembayaran (di luar form utama) --}}
<form id="form-delete-proof" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- Template untuk row baru -->
<table style="display:none;">
    <tbody id="row-template">
        <tr>
            <td>
                <select name="details[__INDEX__][coa_id]" class="form-control" required>
                    <option value="">-- Pilih Akun --</option>
                    @foreach($coas as $coa)
                    <option value="{{ $coa->id }}">{{ $coa->no_akun }} - {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="details[__INDEX__][coa_pembayaran_id]" class="form-control" required>
                    <option value="">-- Pilih Akun Pembayaran --</option>
                    @foreach($paymentCoas as $coa)
                    <option value="{{ $coa->id }}">{{ $coa->no_akun }} - {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <textarea name="details[__INDEX__][keterangan]" class="form-control" rows="1" required></textarea>
            </td>
            <td>
                <input type="text" name="details[__INDEX__][nominal]" class="form-control nominal-input" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    </tbody>
</table>

@stop

@section('js')

<script>
    let rowIndex = {{ max(1, count($overhead->details)) }};

    function deleteProof(id, url) {
        if (!confirm('Hapus bukti pembayaran ini?')) return;
        const form = document.getElementById('form-delete-proof');
        form.action = url;
        form.submit();
    }

    function formatRupiah(value) {
        if (!value) return '';
        const cleanValue = value.toString().replace(/\D/g, '');
        return new Intl.NumberFormat('id-ID').format(cleanValue);
    }

    $(document).ready(function() {
        // Periode switch (sama dengan create)
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
            $('#row-harian, #row-mingguan, #row-bulanan').hide();
            if (jenis === 'harian')   { $('#row-harian').show(); }
            else if (jenis === 'mingguan') { $('#row-mingguan').show(); }
            else if (jenis === 'bulanan')  { $('#row-bulanan').show(); updatePeriodeFromBulanan(); }
        }
        switchPeriode($('#jenis_periode').val());
        $('#jenis_periode').on('change', function() { switchPeriode($(this).val()); });
        $('#bulan_pembebanan, #tahun_pembebanan').on('change', updatePeriodeFromBulanan);
        $('#periode_mulai_harian').on('change', function() {
            if ($('#jenis_periode').val() === 'harian') $('#periode_mulai_final').val($(this).val());
        });
        $('#periode_mulai_mingguan').on('change', function() {
            if ($('#jenis_periode').val() === 'mingguan') $('#periode_mulai_final').val($(this).val());
        });

        // Sebelum submit
        $('form').on('submit', function() {
            var jenis = $('#jenis_periode').val();
            if (jenis === 'harian')   { $('#periode_mulai_final').val($('#periode_mulai_harian').val()); }
            else if (jenis === 'mingguan') { $('#periode_mulai_final').val($('#periode_mulai_mingguan').val()); }
            if (jenis === 'harian')   { $('#periode_mulai_mingguan').prop('disabled', true); }
            else if (jenis === 'mingguan') { $('#periode_mulai_harian').prop('disabled', true); }
            else { $('#periode_mulai_harian').prop('disabled', true); $('#periode_mulai_mingguan').prop('disabled', true); }
        });

        // Tambah row
        $('#btn-add-item').click(function() {
            let html = $('#row-template').html().replace(/__INDEX__/g, rowIndex);
            $('#table-items tbody').append(html);
            rowIndex++;
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#table-items tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        });

        $(document).on('input', '.nominal-input', function() {
            $(this).val(formatRupiah($(this).val()));
        });
    });
</script>
@include('partials.upload-zone-js')
@stop