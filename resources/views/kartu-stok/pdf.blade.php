<!DOCTYPE html>
<html>
<head>
    <title>Kartu Stok Produk</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #d53f8c; /* pink-600 */
            text-transform: uppercase;
            font-size: 9px;
        }
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #97266d; /* pink-800 */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #fbb6ce; /* pink-200 */
        }
        th, td {
            border: 1px solid #cbd5e0; /* gray-400 */
            padding: 6px 4px;
        }
        th {
            background-color: #f687b3; /* pink-300 */
            color: #1a202c; /* gray-900 */
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            text-align: center;
        }
        .bg-pink-400 { background-color: #f687b3; }
        .bg-pink-500 { background-color: #ed64a6; }
        .bg-pink-200 { background-color: #fbb6ce; }
        
        tr:nth-child(even) {
            background-color: #f7fafc; /* gray-50 */
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .text-green-600 { color: #047857; }
        .text-red-600 { color: #dc2626; }
        .text-pink-600 { color: #d53f8c; }
        .font-bold { font-weight: bold; }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #718096;
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
        <p style="text-align: center; font-weight: bold; margin-top: -10px;">PERIODE: {{ strtoupper($namaPeriode) }}</p>
    </div>

    @if(request('kategori'))
        <div style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
            <span style="font-size: 10px; color: #718096; text-transform: uppercase; font-weight: bold;">Kategori:</span>
            <span style="font-size: 14px; font-weight: bold; color: #2d3748;">{{ request('kategori') }}</span>
        </div>
    @endif

    <table>
        <thead>
            <tr class="bg-orange-100">
                <th rowspan="2" style="width: 15%; background-color: #ffedd5; color: #374151; border: 1px solid #000;">Tanggal</th>
                <th rowspan="2" style="width: 40%; background-color: #ffedd5; color: #374151; border: 1px solid #000;">Deskripsi</th>
                <th colspan="3" style="background-color: #ffedd5; color: #374151; border: 1px solid #000;">Produk</th>
            </tr>
            <tr class="bg-orange-100">
                <th style="background-color: #ffedd5; color: #374151; border: 1px solid #000;">Masuk</th>
                <th style="background-color: #ffedd5; color: #374151; border: 1px solid #000;">Keluar</th>
                <th style="background-color: #ffedd5; color: #374151; border: 1px solid #000;">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                @php
                    $rowStyle = $entry['masuk'] > 0 ? 'background-color: #dcfce7;' : ($entry['keluar'] > 0 ? 'background-color: #fee2e2;' : '');
                @endphp
                <tr style="{{ $rowStyle }}">
                    <td class="text-center">{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                    <td>{{ $entry['keterangan'] }}</td>
                    <td class="text-center font-bold text-green-600">{{ $entry['masuk'] ?: '0' }}</td>
                    <td class="text-center font-bold text-red-600">{{ $entry['keluar'] ?: '0' }}</td>
                    <td class="text-center font-bold">{{ $entry['saldo'] ?? '0' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; font-style: italic; color: #718096;">
                        Belum ada transaksi untuk kriteria ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
