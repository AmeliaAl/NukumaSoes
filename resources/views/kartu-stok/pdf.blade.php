<!DOCTYPE html>
<html>
<head>
    <title>Kartu Stok Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .page {
            padding: 20px 18px;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #111827;
            letter-spacing: 0.05em;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #4b5563;
            font-weight: bold;
        }
        .meta-table {
            width: 100%;
            margin-top: 8px;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10px;
        }
        .meta-label {
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            font-size: 9px;
            width: 90px;
        }
        .meta-value {
            font-weight: bold;
            color: #111827;
        }
        .table-wrapper {
            width: 100%;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 5px;
            vertical-align: middle;
        }
        th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.03em;
        }
        tr:nth-child(even) td {
            background-color: #fbfbfb;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .nowrap { white-space: nowrap; }
        .small { font-size: 8px; }
        .footer {
            margin-top: 12px;
            text-align: right;
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @php
        $parts = explode('-', $periode);
        $bulanNum = (int) ($parts[1] ?? date('m'));
        $tahunNum = $parts[0] ?? date('Y');
        $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
    @endphp
    <div class="header">
        <h1>KARTU STOK PRODUK</h1>
        <p>PERIODE: {{ strtoupper($namaPeriode) }}</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 9%;">No Batch</th>
                    <th style="width: 18%;">Deskripsi</th>
                    <th style="width: 10%;">Expired</th>
                    <th style="width: 8%;">Masuk</th>
                    <th style="width: 8%;">Keluar</th>
                    <th style="width: 8%;">Sisa</th>
                    <th style="width: 11%;">HPP</th>
                    <th style="width: 14%;">Total Nilai</th>
                </tr>
            </thead>
        <tbody>
            @forelse($entries as $entry)
                @php
                    $rowStyle = $entry['masuk'] > 0 ? 'background-color: #ecfdf5;' : ($entry['keluar'] > 0 ? 'background-color: #fef2f2;' : '');
                @endphp
                <tr style="{{ $rowStyle }}">
                    <td class="text-center nowrap">{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                    <td class="text-center nowrap">{{ $entry['no_batch'] ?: '-' }}</td>
                    <td class="text-left">{{ $entry['keterangan'] }}</td>
                    <td class="text-center nowrap">{{ !empty($entry['tgl_expired']) ? \Carbon\Carbon::parse($entry['tgl_expired'])->format('d/m/Y') : '-' }}</td>
                    <td class="text-center font-bold text-green-600">{{ number_format($entry['masuk'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center font-bold text-red-600">{{ number_format($entry['keluar'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ number_format($entry['saldo'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ !empty($entry['hpp']) ? 'Rp ' . number_format($entry['hpp'], 0, ',', '.') : '-' }}</td>
                    <td class="text-right font-bold">{{ !empty($entry['total_hpp']) ? 'Rp ' . number_format($entry['total_hpp'], 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 16px; font-style: italic; color: #6b7280;">
                        Belum ada transaksi untuk kriteria ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($entries) > 0)
        <tfoot>
            <tr>
                <td colspan="6" class="text-right font-bold small" style="background-color: #f3f4f6;">Total Nilai Persediaan</td>
                <td class="text-center font-bold small" style="background-color: #f3f4f6; color: {{ $sisaAkhir >= 0 ? '#047857' : '#dc2626' }};">{{ number_format($sisaAkhir, 0, ',', '.') }}</td>
                <td class="text-right" style="background-color: #f3f4f6;">&nbsp;</td>
                <td class="text-right font-bold" style="background-color: #f8fafc; color: {{ $totalNilaiBersih >= 0 ? '#047857' : '#dc2626' }};">Rp {{ number_format($totalNilaiBersih, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right font-bold small" style="background-color: #f3f4f6;">Total Nilai Produk Keluar</td>
                <td class="text-center font-bold small" style="background-color: #f3f4f6; color: #dc2626;">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                <td class="text-right" style="background-color: #f3f4f6;">&nbsp;</td>
                <td class="text-right font-bold" style="background-color: #f8fafc; color: #dc2626;">Rp {{ number_format($totalNilaiKeluar, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
