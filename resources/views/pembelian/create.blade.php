@extends('adminlte::page')

@section('title', 'Tambah Pembelian')

@section('content_header')
    <h1>Tambah Pembelian</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('pembelian.store') }}" method="POST">

            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
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
                    <tr>
                        <td>
                            <select name="details[0][bahan_baku_id]" class="form-control" required>
                                <option value="">-- Pilih Bahan --</option>
                                @foreach($bahanBakus as $bahan)
                                <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="details[0][qty]" class="form-control qty-input" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="text" name="details[0][harga]" class="form-control harga-input" required>
                        </td>
                        <td>
                            <input type="text" class="form-control item-subtotal" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
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
                            <option value="">-- Pilih Pembayaran --</option>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
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
                                <td><input type="text" id="grand_total" class="form-control text-right font-weight-bold" readonly style="font-size: 1.1em;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
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
    let rowIndex = 1;

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

        calculateAll();
    });
</script>

@stop