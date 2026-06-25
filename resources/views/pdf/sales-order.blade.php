<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Order - {{ $salesOrder->no_so }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0 0;
            font-size: 18px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature-box p {
            margin-bottom: 60px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>NUKUMA SOES</h1>
        <h2>SALES ORDER</h2>
    </div>

    @php
        $customerName = '-';
        $customerAddress = '-';
        $customerPhone = '-';

        if ($salesOrder->jenis === 'Konsinyasi' && $salesOrder->penjualanKonsinyasi && $salesOrder->penjualanKonsinyasi->mitra) {
            $customerName = $salesOrder->penjualanKonsinyasi->mitra->namaMitra;
            $customerAddress = $salesOrder->penjualanKonsinyasi->mitra->alamat;
            $customerPhone = $salesOrder->penjualanKonsinyasi->mitra->no_telepon;
        } elseif ($salesOrder->jenis === 'Non Konsinyasi' && $salesOrder->penjualanNonKonsinyasi && $salesOrder->penjualanNonKonsinyasi->pelanggan) {
            $customerName = $salesOrder->penjualanNonKonsinyasi->pelanggan->namaPelanggan;
            $customerAddress = $salesOrder->penjualanNonKonsinyasi->pelanggan->alamat;
            $customerPhone = $salesOrder->penjualanNonKonsinyasi->pelanggan->no_telepon;
        }
    @endphp

    <table class="info-table">
        <tr>
            <td width="15%"><strong>No. SO</strong></td>
            <td width="35%">: {{ $salesOrder->no_so }}</td>
            <td width="15%"><strong>Kepada</strong></td>
            <td width="35%">: {{ $customerName }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>: {{ $salesOrder->tanggal ? $salesOrder->tanggal->format('d/m/Y') : '-' }}</td>
            <td><strong>Alamat</strong></td>
            <td>: {{ $customerAddress }}</td>
        </tr>
        <tr>
            <td><strong>Jenis</strong></td>
            <td>: {{ $salesOrder->jenis }}</td>
            <td><strong>Telepon</strong></td>
            <td>: {{ $customerPhone }}</td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>: {{ $salesOrder->status }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="45%">Barang</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga</th>
                <th width="20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($salesOrder->detailSalesOrder as $index => $detail)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $detail->barang ? $detail->barang->nama_lengkap : 'Barang Terhapus' }}</td>
                <td class="text-center">{{ $detail->qty }}</td>
                <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @php $total += $detail->subtotal; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Hormat Kami,</p>
            <p>___________________</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
