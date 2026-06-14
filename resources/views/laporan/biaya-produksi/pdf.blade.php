<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Biaya Produksi - {{ $jobOrder->nomor_job }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
        }
        
        .container {
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 12px;
            color: #555;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 9px;
        }
        
        .info-table td.label {
            width: 120px;
            font-weight: bold;
        }
        
        .info-table td.value {
            width: 230px;
        }
        
        .section-title {
            background-color: #333;
            color: #fff;
            padding: 6px 8px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 8px;
        }
        
        .section-title.blue {
            background-color: #0066cc;
        }
        
        .section-title.green {
            background-color: #009933;
        }
        
        .section-title.orange {
            background-color: #ffaa00;
            color: #000;
        }
        
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        
        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9px;
        }
        
        table.data th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }
        
        table.data td.center {
            text-align: center;
        }
        
        table.data td.right,
        table.data th.right {
            text-align: right;
        }
        
        table.data tfoot td {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .summary-box {
            border: 2px solid #000;
            padding: 12px;
            margin: 20px auto;
            width: 65%;
            background-color: #fafafa;
        }
        
        .summary-box h3 {
            text-align: center;
            margin-bottom: 12px;
            font-size: 11px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 4px 6px;
            font-size: 9px;
            border-bottom: 1px solid #ddd;
        }
        
        .summary-table td.right {
            text-align: right;
            width: 140px;
        }
        
        .summary-table tr.total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
        }
        
        .summary-table tr.hpp {
            background-color: #d4edda;
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 10px;
        }
        
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-row {
            width: 100%;
            display: table;
        }
        
        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 8px;
            font-size: 9px;
        }
        
        .signature-space {
            height: 50px;
            margin: 8px 0;
        }
        
        .signature-name {
            font-weight: bold;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 4px;
            min-width: 120px;
        }
        
        .signature-title {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }
        
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            font-size: 8px;
            color: #666;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>KARTU BIAYA PRODUKSI</h1>
            <h2>Job Order Costing System</h2>
        </div>

        <!-- Informasi Job Order -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">No. Job Order</td>
                    <td class="value">: <strong>{{ $jobOrder->nomor_job }}</strong></td>
                    <td class="label">Tanggal Mulai</td>
                    <td class="value">: {{ \Carbon\Carbon::parse($jobOrder->tanggal_mulai)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Produk</td>
                    <td class="value">: {{ $jobOrder->produk->nama_produk ?? '-' }}</td>
                    <td class="label">Tanggal Selesai</td>
                    <td class="value">: {{ $jobOrder->tanggal_selesai ? \Carbon\Carbon::parse($jobOrder->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Order</td>
                    <td class="value">: {{ number_format($jobOrder->jumlah_produksi, 0, ',', '.') }} {{ $jobOrder->produk->satuan_produk ?? '' }}</td>
                    <td class="label">Status</td>
                    <td class="value">: <span class="badge">SELESAI</span></td>
                </tr>
                <tr>
                    <td class="label">Jumlah Batch</td>
                    <td class="value">: {{ $jobOrder->jumlah_batch ?? 1 }} Batch</td>
                    <td class="label">Jenis Produksi</td>
                    <td class="value">: {{ $jobOrder->jenis_produksi == 'maklun' ? 'Maklun' : 'Brand Sendiri' }}</td>
                </tr>
            </table>
        </div>

        <!-- Biaya Bahan Baku -->
        <div class="section-title blue">BIAYA BAHAN BAKU LANGSUNG</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Bahan Baku</th>
                    <th style="width: 60px;">Satuan</th>
                    <th class="right" style="width: 80px;">Jumlah</th>
                    <th class="right" style="width: 100px;">Harga/Satuan</th>
                    <th class="right" style="width: 100px;">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobOrder->pemakaianBahanBakuLangsung as $index => $pemakaian)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $pemakaian->bahanBaku->nama_bahan ?? '-' }}</td>
                    <td class="center">{{ $pemakaian->bahanBaku->satuan ?? '-' }}</td>
                    <td class="right">{{ number_format($pemakaian->jumlah_pakai, 2, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($pemakaian->harga_satuan ?? ($pemakaian->harga_per_satuan ?? 0), 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($pemakaian->total_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="center">Belum ada data pemakaian bahan baku</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="right">TOTAL BIAYA BAHAN BAKU:</td>
                    <td class="right">Rp {{ number_format($jobOrder->total_biaya_bahan ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Biaya Tenaga Kerja -->
        <div class="section-title green">BIAYA TENAGA KERJA LANGSUNG</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Tenaga Kerja</th>
                    <th style="width: 60px;">Jenis</th>
                    <th class="right" style="width: 60px;">Jam</th>
                    <th class="right" style="width: 40px;">Batch</th>
                    <th class="right" style="width: 90px;">Upah/Jam</th>
                    <th class="right" style="width: 90px;">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobOrder->biayaTenagaKerjaLangsung as $index => $biaya)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $biaya->tenagaKerja->nama_tenaga ?? '-' }}</td>
                    <td class="center">
                        @if($biaya->tenagaKerja->jenis_tenaga == 'langsung')
                            <span class="badge">LANGSUNG</span>
                        @else
                            <span class="badge">TDK LANGSUNG</span>
                        @endif
                    </td>
                    <td class="right">{{ number_format($biaya->jam_kerja ?? 0, 1, ',', '.') }} jam</td>
                    <td class="right">{{ $biaya->jumlah_batch ?? 1 }}</td>
                    <td class="right">Rp {{ number_format($biaya->upah_per_jam ?? 0, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($biaya->total_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="center">Belum ada data biaya tenaga kerja</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="right">TOTAL BIAYA TENAGA KERJA:</td>
                    <td class="right">Rp {{ number_format($jobOrder->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Biaya Overhead Pabrik -->
        <div class="section-title orange">BIAYA OVERHEAD PABRIK</div>
        @php
            $allBop = collect();
            foreach($jobOrder->biayaOverheadPabrik as $b) {
                $allBop->push([
                    'jenis' => $b->jenis_overhead ?? $b->jenis_biaya ?? 'Overhead Pabrik',
                    'keterangan' => $b->keterangan,
                    'batch' => $b->jumlah_batch ?? 1,
                    'biaya' => $b->total_biaya ?? ($b->nominal ?? 0)
                ]);
            }
            foreach($jobOrder->biayaTenagaKerjaTidakLangsung as $tk) {
                $allBop->push([
                    'jenis' => 'BTK Tidak Langsung (BTKTL)',
                    'keterangan' => 'Upah ' . ($tk->tenagaKerja->nama_tenaga ?? '') . ' (' . ($tk->tenagaKerja->jabatan ?? '') . ')',
                    'batch' => $tk->jumlah_batch ?? 1,
                    'biaya' => $tk->total_biaya
                ]);
            }
            foreach($jobOrder->pemakaianBahanBakuTidakLangsung as $bh) {
                $allBop->push([
                    'jenis' => 'Bahan Tidak Langsung / Kemasan',
                    'keterangan' => 'Pemakaian ' . ($bh->bahanBaku->nama_bahan ?? '') . ' (' . number_format($bh->jumlah_pakai, 2) . ' ' . ($bh->bahanBaku->satuan ?? '') . ')',
                    'batch' => $jobOrder->jumlah_batch ?? 1,
                    'biaya' => $bh->total_biaya
                ]);
            }
        @endphp

        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Jenis Biaya</th>
                    <th>Keterangan</th>
                    <th class="center" style="width: 50px;">Batch</th>
                    <th class="right" style="width: 100px;">Jumlah Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allBop as $index => $overhead)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $overhead['jenis'] }}</td>
                    <td>{{ $overhead['keterangan'] ?? '-' }}</td>
                    <td class="center">{{ $overhead['batch'] ?? 1 }}</td>
                    <td class="right">Rp {{ number_format($overhead['biaya'], 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="center">Belum ada data biaya overhead pabrik</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="right">TOTAL BIAYA OVERHEAD:</td>
                    <td class="right">Rp {{ number_format($jobOrder->total_biaya_overhead ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Ringkasan Total -->
        <div class="summary-box">
            <h3>RINGKASAN BIAYA PRODUKSI</h3>
            <table class="summary-table">
                <tr>
                    <td>Biaya Bahan Baku Langsung</td>
                    <td class="right">Rp {{ number_format($jobOrder->total_biaya_bahan ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Tenaga Kerja Langsung</td>
                    <td class="right">Rp {{ number_format($jobOrder->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Overhead Pabrik</td>
                    <td class="right">Rp {{ number_format($jobOrder->total_biaya_overhead ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="total">
                    <td><strong>TOTAL BIAYA PRODUKSI</strong></td>
                    <td class="right"><strong>Rp {{ number_format($jobOrder->total_biaya_produksi ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td><strong>JUMLAH PRODUKSI</strong></td>
                    <td class="right"><strong>{{ number_format($jobOrder->jumlah_produksi, 0, ',', '.') }} {{ $jobOrder->produk->satuan_produk ?? '' }}</strong></td>
                </tr>
                <tr class="hpp">
                    <td><strong>HARGA POKOK PRODUKSI (HPP) / UNIT</strong></td>
                    <td class="right">
                        <strong>Rp {{ number_format($jobOrder->harga_pokok_per_unit ?? 0, 0, ',', '.') }} / {{ $jobOrder->produk->satuan_produk ?? 'Unit' }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-col">
                    <p>Dibuat Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $jobOrder->admin->nama ?? '_______________' }}</p>
                    <p class="signature-title">Admin Produksi</p>
                </div>
                <div class="signature-col">
                    <p>Diperiksa Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">_______________</p>
                    <p class="signature-title">Supervisor Produksi</p>
                </div>
                <div class="signature-col">
                    <p>Disetujui Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">_______________</p>
                    <p class="signature-title">Manager Produksi</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>