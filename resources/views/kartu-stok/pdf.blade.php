<!DOCTYPE html>
<html>
<head>
    <title>Kartu Stok Produk</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #1f2937; margin: 0; padding: 0; }
        .page { padding: 20px 18px; }
        .header { text-align: center; margin-bottom: 8px; }
        .header h1 { font-size: 20px; margin: 0; color: #111827; letter-spacing: 0.05em; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #4b5563; font-weight: bold; }
        .table-wrapper { width: 100%; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; border: 1px solid #d1d5db; font-size: 9px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 4px; vertical-align: middle; }
        th { background-color: #f3f4f6; color: #111827; font-weight: 700; text-transform: uppercase; font-size: 8.5px; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .font-bold   { font-weight: 700; }
        .nowrap      { white-space: nowrap; }
        .row-masuk   { background-color: #ecfdf5; }
        .row-keluar  { background-color: #fef2f2; }
        .row-pre-sub { background-color: #ffffff; }
        .row-sub     { background-color: #f9fafb; }
        .footer { margin-top: 12px; text-align: right; font-size: 9px; color: #6b7280; }
        .badge-expired     { color: #b91c1c; font-weight: bold; }
        .badge-near-exp    { color: #c2410c; font-weight: bold; }
        .badge-ok          { color: #047857; }
    </style>
</head>
<body>
    @php
        $parts      = explode('-', $periode);
        $bulanNum   = (int)($parts[1] ?? date('m'));
        $tahunNum   = $parts[0] ?? date('Y');
        $bulanList  = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
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
                    <th style="width:10%;">Tanggal</th>
                    <th style="width:9%;">No Batch</th>
                    <th style="width:13%;">Deskripsi</th>
                    <th style="width:9%;">Tgl Kemas</th>
                    <th style="width:9%;">Expired</th>
                    <th style="width:7%;">Masuk</th>
                    <th style="width:7%;">Keluar</th>
                    <th style="width:7%;">Sisa</th>
                    <th style="width:12%;">HPP</th>
                    <th style="width:15%;">Nilai Persediaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        if ($row['is_header']) {
                            $rowClass    = $row['type'] === 'masuk' ? 'row-masuk' : 'row-keluar';
                            $borderStyle = 'border-top: 2px solid #9ca3af;';
                        } else {
                            $rowClass    = 'row-sub';
                            $borderStyle = '';
                        }
                        $expiredRaw   = $row['tgl_expired_raw'] ?? null;
                        $isExpired    = $expiredRaw && \Carbon\Carbon::parse($expiredRaw)->isPast();
                        $isNearExp    = $expiredRaw && !$isExpired && \Carbon\Carbon::parse($expiredRaw)->diffInDays(now()) <= 30;
                        $expClass     = $isExpired ? 'badge-expired' : ($isNearExp ? 'badge-near-exp' : 'badge-ok');
                    @endphp
                    <tr class="{{ $rowClass }}" style="{{ $borderStyle }}">
                        <td class="text-center nowrap">
                            {{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '' }}
                        </td>
                        <td class="text-center nowrap">{{ $row['no_batch'] ?? '-' }}</td>
                        <td class="text-left">{{ $row['keterangan'] ?? '' }}</td>
                        <td class="text-center nowrap">
                            {{ !empty($row['tgl_masuk']) ? \Carbon\Carbon::parse($row['tgl_masuk'])->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center nowrap">
                            @if(!empty($row['tgl_expired']))
                                <span class="{{ $expClass }}">{{ $row['tgl_expired'] }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center font-bold" style="color:#047857;">
                            {{ !is_null($row['masuk']) ? $row['masuk'] : '' }}
                        </td>
                        <td class="text-center font-bold" style="color:#dc2626;">
                            {{ !is_null($row['keluar']) ? $row['keluar'] : '' }}
                        </td>
                        <td class="text-center font-bold">{{ $row['sisa'] }}</td>
                        <td class="text-right">
                            {{ !empty($row['hpp']) ? 'Rp'.number_format($row['hpp'],0,',','.') : '-' }}
                        </td>
                        <td class="text-right font-bold">
                            {{ ($row['nilai_persediaan'] ?? 0) > 0 ? 'Rp'.number_format($row['nilai_persediaan'],0,',','.') : 'Rp0' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding:16px;font-style:italic;color:#6b7280;">
                            Belum ada transaksi untuk kriteria ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($rows) > 0)
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right font-bold" style="background:#dcfce7;color:#166534;border-top:2px solid #86efac;">
                        Total Nilai Produk Masuk
                    </td>
                    <td class="text-center font-bold" style="background:#dcfce7;color:#166534;border-top:2px solid #86efac;">
                        {{ number_format($totalMasuk,0,',','.') }}
                    </td>
                    <td style="background:#dcfce7;border-top:2px solid #86efac;"></td>
                    <td style="background:#dcfce7;border-top:2px solid #86efac;"></td>
                    <td style="background:#dcfce7;border-top:2px solid #86efac;"></td>
                    <td class="text-right font-bold" style="background:#dcfce7;color:#166534;border-top:2px solid #86efac;">
                        Rp{{ number_format($totalNilaiMasuk,0,',','.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="text-right font-bold" style="background:#fef2f2;color:#991b1b;">
                        Total Nilai Produk Keluar
                    </td>
                    <td class="text-center font-bold" style="background:#fef2f2;color:#991b1b;">
                        {{ number_format($totalKeluar,0,',','.') }}
                    </td>
                    <td style="background:#fef2f2;"></td>
                    <td style="background:#fef2f2;"></td>
                    <td class="text-right font-bold" style="background:#fef2f2;color:#991b1b;">
                        Rp{{ number_format($totalNilaiKeluar,0,',','.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <div class="footer">Dicetak pada: {{ date('d/m/Y H:i') }}</div>
</body>
</html>
