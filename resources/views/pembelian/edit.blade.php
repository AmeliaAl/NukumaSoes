@extends('adminlte::page')

@section('title', 'Edit Pembelian')

@section('content_header')
    <h1>Edit Pembelian</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Baris 1: Tanggal & Supplier --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required
                               value="{{ $pembelian->tanggal }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-control" required>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ $pembelian->supplier_id == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Nomor Permintaan --}}
            {{-- Dropdown manual; ke depannya dari modul Produksi --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nomor Permintaan</label>
                        <select name="nomor_permintaan" class="form-control">
                            <option value="">-- Pilih / Kosongkan jika tidak ada --</option>
                            {{-- Nilai saat ini selalu muncul sebagai pilihan terpilih --}}
                            @if($pembelian->nomor_permintaan)
                            <option value="{{ $pembelian->nomor_permintaan }}" selected>
                                {{ $pembelian->nomor_permintaan }}
                            </option>
                            @endif
                            {{-- Placeholder: nantinya diisi dari modul Produksi --}}
                            @foreach($existingNomorPermintaan ?? [] as $np)
                                @if($np !== $pembelian->nomor_permintaan)
                                <option value="{{ $np }}">{{ $np }}</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Ke depannya akan terhubung otomatis dengan Nomor Permintaan dari modul Produksi.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Status Permintaan — placeholder integrasi modul Produksi --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status Permintaan
                            <small class="text-muted">(opsional — akan otomatis setelah integrasi Produksi)</small>
                        </label>
                        <select name="status_permintaan" class="form-control">
                            <option value="">-- Tidak Terkait Permintaan --</option>
                            <option value="lengkap"
                                {{ old('status_permintaan', $pembelian->status_permintaan) === 'lengkap' ? 'selected' : '' }}>
                                Lengkap
                            </option>
                            <option value="sebagian"
                                {{ old('status_permintaan', $pembelian->status_permintaan) === 'sebagian' ? 'selected' : '' }}>
                                Sebagian
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h5>Detail Item Pembelian</h5>

            <table class="table table-bordered" id="table-items">
                <thead>
                    <tr>
                        <th>Bahan Baku</th>
                        <th width="100">Qty Pembelian</th>
                        <th width="130">Isi/Kemasan</th>
                        <th width="100">Satuan</th>
                        <th width="150">Harga Satuan</th>
                        <th width="170">Subtotal Item</th>
                        <th width="60">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian->details as $index => $detail)
                    <tr>
                        <td>
                            <select name="details[{{ $index }}][bahan_baku_id]"
                                    class="form-control bahan-baku-select" required>
                                <option value="">-- Pilih Bahan --</option>
                                @foreach($bahanBakus as $bahan)
                                <option value="{{ $bahan->id }}"
                                        data-satuan="{{ $bahan->satuan }}"
                                        data-isi="{{ $bahan->isi_per_kemasan ?? '' }}"
                                        {{ $detail->bahan_baku_id == $bahan->id ? 'selected' : '' }}>
                                    {{ $bahan->nama_bahan }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="details[{{ $index }}][qty]"
                                   class="form-control qty-input" min="1"
                                   value="{{ $detail->qty }}" required>
                        </td>
                        <td>
                            <input type="text" name="details[{{ $index }}][isi_per_kemasan]"
                                   class="form-control isi-kemasan"
                                   value="{{ $detail->isi_per_kemasan ?? '' }}" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control satuan-display"
                                   value="{{ $detail->bahanBaku->satuan ?? '' }}" readonly>
                        </td>
                        <td>
                            <input type="text" name="details[{{ $index }}][harga]"
                                   class="form-control harga-input"
                                   value="{{ number_format($detail->harga, 0, ',', '.') }}" required>
                        </td>
                        <td>
                            <input type="text" class="form-control item-subtotal"
                                   value="{{ \App\Helpers\FormatHelper::rupiah($detail->subtotal) }}" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add-item">
                                + Tambah Item
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <hr>

            <div class="row">
                {{-- Pembayaran --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pembayaran <span class="text-danger">*</span></label>
                        <select name="coa_id" class="form-control" required>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->id }}"
                                {{ $pembelian->coa_id == $coa->id ? 'selected' : '' }}>
                                {{ $coa->nama_akun }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Ringkasan Total --}}
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th class="align-middle">Total Pembelian</th>
                                <td>
                                    <input type="text" id="subtotal" class="form-control text-right" readonly
                                           value="{{ \App\Helpers\FormatHelper::rupiah($pembelian->subtotal) }}">
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">(-) Diskon</th>
                                <td>
                                    <input type="text" name="diskon" id="diskon" class="form-control text-right"
                                           value="{{ number_format($pembelian->diskon ?? 0, 0, ',', '.') }}">
                                </td>
                            </tr>
                            <tr style="border-top: 2px solid #dee2e6;">
                                <th class="align-middle">Total Bersih</th>
                                <td>
                                    <input type="text" id="total_bersih" class="form-control text-right" readonly
                                           value="{{ \App\Helpers\FormatHelper::rupiah($pembelian->total_bersih) }}">
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">(+) Ongkir</th>
                                <td>
                                    <input type="text" name="ongkir" id="ongkir" class="form-control text-right"
                                           value="{{ number_format($pembelian->ongkir ?? 0, 0, ',', '.') }}">
                                </td>
                            </tr>
                            <tr style="border-top: 2px solid #dee2e6;">
                                <th class="align-middle">Grand Total</th>
                                <td>
                                    <input type="text" id="grand_total"
                                           class="form-control text-right font-weight-bold"
                                           readonly style="font-size: 1.1em;"
                                           value="{{ \App\Helpers\FormatHelper::rupiah($pembelian->grand_total) }}">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Dokumen Pembelian --}}
            <div class="mb-3">
                <h5>Dokumen Pembelian</h5>
                @if($pembelian->paymentProofs->count())
                    <ul class="list-group mb-3">
                        @foreach($pembelian->paymentProofs as $proof)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ asset('storage/' . $proof->file_path) }}" target="_blank">
                                <i class="fas fa-file mr-1"></i>{{ $proof->file_name }}
                            </a>
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick="deleteProof({{ $proof->id }}, '{{ route('pembelian.delete-proof', $proof->id) }}')">
                                Hapus
                            </button>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Tidak ada dokumen.</p>
                @endif

                <div class="form-group">
                    <label>Unggah Dokumen Pembelian Baru</label>
                    <x-upload-zone
                        input-id="upload_pembelian_edit"
                        input-name="payment_proofs[]"
                        label=""
                        :max-files="5"
                    />
                    <small class="form-text text-muted">
                        Maksimal total 5 file per transaksi, termasuk yang sudah ada.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ url('pembelian') }}" class="btn btn-secondary">Cancel</a>

        </form>
    </div>
</div>

{{-- Hidden form untuk hapus bukti pembayaran --}}
<form id="form-delete-proof" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- Template row baru --}}
<table style="display:none;">
    <tbody id="row-template">
        <tr>
            <td>
                <select name="details[__INDEX__][bahan_baku_id]" class="form-control bahan-baku-select" required>
                    <option value="">-- Pilih Bahan --</option>
                    @foreach($bahanBakus as $bahan)
                    <option value="{{ $bahan->id }}"
                            data-satuan="{{ $bahan->satuan }}"
                            data-isi="{{ $bahan->isi_per_kemasan ?? '' }}">
                        {{ $bahan->nama_bahan }}
                    </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="details[__INDEX__][qty]" class="form-control qty-input" min="1" value="1" required>
            </td>
            <td>
                <input type="text" name="details[__INDEX__][isi_per_kemasan]" class="form-control isi-kemasan" readonly>
            </td>
            <td>
                <input type="text" class="form-control satuan-display" readonly>
            </td>
            <td>
                <input type="text" name="details[__INDEX__][harga]" class="form-control harga-input" placeholder="0" required>
            </td>
            <td>
                <input type="text" class="form-control item-subtotal" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </tbody>
</table>

@stop

@section('js')
<script>
    let rowIndex = {{ max(1, count($pembelian->details)) }};

    function deleteProof(id, url) {
        if (!confirm('Hapus bukti pembayaran ini?')) return;
        const form = document.getElementById('form-delete-proof');
        form.action = url;
        form.submit();
    }

    function formatRupiah(input) {
        if (!input) return '';
        return new Intl.NumberFormat('id-ID').format(input.toString().replace(/\D/g, ''));
    }

    function formatRupiahDisplay(number) {
        return 'Rp ' + number.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function parseNumber(value) {
        return Number(value.toString().replace(/\D/g, '')) || 0;
    }

    function autoFillBahan(row) {
        const selected = row.find('.bahan-baku-select option:selected');
        row.find('.isi-kemasan').val(selected.data('isi') || '');
        row.find('.satuan-display').val(selected.data('satuan') || '');
    }

    function calculateAll() {
        let subtotalAll = 0;

        $('#table-items tbody tr').each(function () {
            const qty   = parseNumber($(this).find('.qty-input').val());
            const harga = parseNumber($(this).find('.harga-input').val());
            const itemSubtotal = qty * harga;
            $(this).find('.item-subtotal').val(formatRupiahDisplay(itemSubtotal));
            subtotalAll += itemSubtotal;
        });

        const diskon      = parseNumber($('#diskon').val());
        const ongkir      = parseNumber($('#ongkir').val());
        const totalBersih = subtotalAll - diskon;
        const grandTotal  = totalBersih + ongkir;

        $('#subtotal').val(formatRupiahDisplay(subtotalAll));
        $('#total_bersih').val(formatRupiahDisplay(totalBersih));
        $('#grand_total').val(formatRupiahDisplay(grandTotal));
    }

    $(document).ready(function () {

        // Tambah item
        $('#btn-add-item').click(function () {
            const html = $('#row-template').html().replace(/__INDEX__/g, rowIndex);
            $('#table-items tbody').append(html);
            rowIndex++;
        });

        // Hapus item
        $(document).on('click', '.remove-row', function () {
            if ($('#table-items tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateAll();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        });

        // Auto-fill saat bahan baku dipilih
        $(document).on('change', '.bahan-baku-select', function () {
            autoFillBahan($(this).closest('tr'));
            calculateAll();
        });

        // Hitung ulang saat qty/harga/diskon/ongkir berubah
        $(document).on('input', '.qty-input, .harga-input, #diskon, #ongkir', function () {
            if ($(this).hasClass('harga-input') || $(this).is('#diskon') || $(this).is('#ongkir')) {
                $(this).val(formatRupiah($(this).val()));
            }
            calculateAll();
        });

        $(document).on('focus', '.harga-input', function () {
            const clean = parseNumber($(this).val());
            $(this).val(clean ? formatRupiah(clean) : '');
        });

        calculateAll();
    });
</script>
@include('partials.upload-zone-js')
@stop
