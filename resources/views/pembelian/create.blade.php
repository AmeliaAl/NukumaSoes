@extends('adminlte::page')

@section('title', 'Tambah Pembelian')

@section('content_header')
    <h1>Tambah Pembelian</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('pembelian.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Baris 1: Tanggal & Supplier --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Nomor Permintaan --}}
            {{-- Saat ini dropdown manual. Ke depannya akan diisi otomatis dari modul Produksi. --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nomor Permintaan</label>
                        <select name="nomor_permintaan" class="form-control">
                            <option value="">-- Pilih / Kosongkan jika tidak ada --</option>
                            {{-- Placeholder: nantinya diisi dari modul Produksi --}}
                            @foreach($existingNomorPermintaan ?? [] as $np)
                            <option value="{{ $np }}">{{ $np }}</option>
                            @endforeach
                        </select>

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
                            <option value="lengkap"  {{ old('status_permintaan') === 'lengkap'  ? 'selected' : '' }}>Lengkap</option>
                            <option value="sebagian" {{ old('status_permintaan') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
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
                    <tr>
                        <td>
                            <select name="details[0][bahan_baku_id]" class="form-control bahan-baku-select" required>
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
                            <input type="number" name="details[0][qty]" class="form-control qty-input" min="1" value="1" required>
                        </td>
                        <td>
                            {{-- Auto-fill dari Master Bahan Baku, disimpan ke DB --}}
                            <input type="text" name="details[0][isi_per_kemasan]" class="form-control isi-kemasan" readonly>
                        </td>
                        <td>
                            {{-- Readonly, auto-fill dari Master Bahan Baku --}}
                            <input type="text" class="form-control satuan-display" readonly>
                        </td>
                        <td>
                            <input type="text" name="details[0][harga]" class="form-control harga-input" placeholder="0" required>
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
                            <option value="">-- Pilih Pembayaran --</option>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->nama_akun }}</option>
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
                                <td><input type="text" id="subtotal" class="form-control text-right" readonly></td>
                            </tr>
                            <tr>
                                <th class="align-middle">(-) Diskon</th>
                                <td><input type="text" name="diskon" id="diskon" class="form-control text-right" value="0"></td>
                            </tr>
                            <tr style="border-top: 2px solid #dee2e6;">
                                <th class="align-middle">Total Bersih</th>
                                <td><input type="text" id="total_bersih" class="form-control text-right" readonly></td>
                            </tr>
                            <tr>
                                <th class="align-middle">(+) Ongkir</th>
                                <td><input type="text" name="ongkir" id="ongkir" class="form-control text-right" value="0"></td>
                            </tr>
                            <tr style="border-top: 2px solid #dee2e6;">
                                <th class="align-middle">Grand Total</th>
                                <td>
                                    <input type="text" id="grand_total" class="form-control text-right font-weight-bold"
                                           readonly style="font-size: 1.1em;">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Dokumen Pembelian --}}
            <x-upload-zone
                input-id="upload_pembelian_create"
                input-name="payment_proofs[]"
                label="Dokumen Pembelian"
                :max-files="5"
            />

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ url('pembelian') }}" class="btn btn-secondary">Cancel</a>

        </form>
    </div>
</div>

{{-- Template row baru (disembunyikan) --}}
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
    let rowIndex = 1;

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
        $('#btn-add-item').click(function () {
            const html = $('#row-template').html().replace(/__INDEX__/g, rowIndex);
            $('#table-items tbody').append(html);
            rowIndex++;
        });
        $(document).on('click', '.remove-row', function () {
            if ($('#table-items tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateAll();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        });
        $(document).on('change', '.bahan-baku-select', function () {
            autoFillBahan($(this).closest('tr'));
            calculateAll();
        });
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
