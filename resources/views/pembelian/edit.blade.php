@extends('adminlte::page')

@section('title', 'Edit Pembelian')

@section('content_header')
    <h1>Edit Pembelian</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="{{ $pembelian->tanggal }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor Permintaan</label>
                        <input type="text" name="nomor_permintaan" class="form-control" value="{{ $pembelian->nomor_permintaan }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $pembelian->supplier_id == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h4>Detail Item Pembelian</h4>
            <table class="table table-bordered" id="table-items">
                <thead>
                    <tr>
                        <th>Bahan Baku</th>
                        <th width="150">Qty</th>
                        <th width="200">Harga</th>
                        <th width="200">Subtotal Item</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian->details as $index => $detail)
                    <tr>
                        <td>
                            <select name="details[{{ $index }}][bahan_baku_id]" class="form-control" required>
                                <option value="">-- Pilih Bahan --</option>
                                @foreach($bahanBakus as $bahan)
                                <option value="{{ $bahan->id }}" {{ $detail->bahan_baku_id == $bahan->id ? 'selected' : '' }}>
                                    {{ $bahan->nama_bahan }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="details[{{ $index }}][qty]" class="form-control qty-input" min="1" value="{{ $detail->qty }}" required>
                        </td>
                        <td>
                            <input type="text" name="details[{{ $index }}][harga]" class="form-control harga-input" value="{{ number_format($detail->harga, 0, ',', '.') }}" required>
                        </td>
                        <td>
                            <input type="text" class="form-control item-subtotal" value="Rp {{ number_format($detail->subtotal, 0, ',', '.') }}" readonly>
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
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add-item">+ Tambah Item</button>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pembayaran</label>
                        <select name="coa_id" class="form-control" required>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->id }}" {{ $pembelian->coa_id == $coa->id ? 'selected' : '' }}>
                                {{ $coa->nama_akun }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th class="align-middle">Total Pembelian</th>
                                <td><input type="text" id="subtotal" class="form-control text-right" readonly value="Rp {{ number_format($pembelian->subtotal, 0, ',', '.') }}"></td>
                            </tr>
                            <tr>
                                <th class="align-middle">(-) Diskon</th>
                                <td><input type="text" name="diskon" id="diskon" class="form-control text-right" value="{{ number_format($pembelian->diskon ?? 0, 0, ',', '.') }}"></td>
                            </tr>
                            <tr style="border-top: 2px solid #dee2e6;">
                                <th class="align-middle">Total Bersih</th>
                                <td><input type="text" id="total_bersih" class="form-control text-right" readonly value="Rp {{ number_format($pembelian->total_bersih, 0, ',', '.') }}"></td>
                            </tr>
                            <tr>
                                <th class="align-middle">(+) Ongkir</th>
                                <td><input type="text" name="ongkir" id="ongkir" class="form-control text-right" value="{{ number_format($pembelian->ongkir ?? 0, 0, ',', '.') }}"></td>
                            </tr>
                            <tr style="border-top: 2px solid #dee2e6;">
                                <th class="align-middle">Grand Total</th>
                                <td><input type="text" id="grand_total" class="form-control text-right font-weight-bold" readonly style="font-size: 1.1em;" value="Rp {{ number_format($pembelian->grand_total, 0, ',', '.') }}"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ url('pembelian') }}" class="btn btn-secondary">Cancel</a>

        </form>

    </div>

</div>

<!-- Template untuk row baru -->
<table style="display:none;">
    <tbody id="row-template">
        <tr>
            <td>
                <select name="details[__INDEX__][bahan_baku_id]" class="form-control" required>
                    <option value="">-- Pilih Bahan --</option>
                    @foreach($bahanBakus as $bahan)
                    <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="details[__INDEX__][qty]" class="form-control qty-input" min="1" value="1" required>
            </td>
            <td>
                <input type="text" name="details[__INDEX__][harga]" class="form-control harga-input" required>
            </td>
            <td>
                <input type="text" class="form-control item-subtotal" readonly>
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
    let rowIndex = {{ max(1, count($pembelian->details)) }};

    function formatRupiah(input) {
        if (!input) return '';
        const value = input.toString().replace(/\D/g, '');
        return new Intl.NumberFormat('id-ID').format(value);
    }

    function parseNumber(value) {
        return Number(value.toString().replace(/\D/g, '')) || 0;
    }

    function calculateAll() {
        let subtotalAll = 0;

        $('#table-items tbody tr').each(function() {
            const qty = parseNumber($(this).find('.qty-input').val());
            const harga = parseNumber($(this).find('.harga-input').val());
            const itemSubtotal = qty * harga;
            
            $(this).find('.item-subtotal').val('Rp ' + formatRupiah(itemSubtotal));
            subtotalAll += itemSubtotal;
        });

        const diskon = parseNumber($('#diskon').val());
        const ongkir = parseNumber($('#ongkir').val());

        const totalBersih = subtotalAll - diskon;
        const grandTotal = totalBersih + ongkir;

        $('#subtotal').val('Rp ' + formatRupiah(subtotalAll));
        $('#total_bersih').val('Rp ' + formatRupiah(totalBersih));
        $('#grand_total').val('Rp ' + formatRupiah(grandTotal));
    }

    $(document).ready(function() {
        $('#btn-add-item').click(function() {
            let html = $('#row-template').html().replace(/__INDEX__/g, rowIndex);
            $('#table-items tbody').append(html);
            rowIndex++;
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#table-items tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateAll();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        });

        $(document).on('input', '.qty-input, .harga-input, #diskon, #ongkir', function() {
            if($(this).hasClass('harga-input') || $(this).attr('id') === 'diskon' || $(this).attr('id') === 'ongkir') {
                $(this).val(formatRupiah($(this).val()));
            }
            calculateAll();
        });

        // Initialize display calculation
        calculateAll();
    });
</script>

@stop