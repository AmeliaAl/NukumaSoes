<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; line-height: 1.4; }
        h2 { text-align: center; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px; vertical-align: top; }
        .w-50 { width: 50%; }
        .w-25 { width: 25%; }
        .border-bottom { border-bottom: 1px solid #000; }
        .border-top { border-top: 1px solid #000; }
        .relative { position: relative; }
        .plus-sign { display: inline-block; padding-left: 10px; }
        .minus-sign { display: inline-block; padding-left: 10px; }
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
    <h2>NUKUMA SOES</h2>
    <h2 style="margin-top: -5px;">LAPORAN LABA RUGI</h2>
    <p class="text-center font-bold" style="margin-top: -5px; margin-bottom: 30px;">PERIODE: {{ strtoupper($namaPeriode) }}</p>

    <div style="margin-left: auto; margin-right: auto; width: 100%;">
        <table class="w-full">
            <tbody>
                <!-- PENJUALAN BERSIH -->
                <tr class="font-bold">
                    <td class="w-50 uppercase">Penjualan Bersih</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right border-bottom" style="width: 20%;">Rp {{ number_format($penjualanBersih, 2, ',', '.') }}</td>
                </tr>

                <tr><td colspan="5" style="padding: 10px 0;"></td></tr>

                <!-- HARGA POKOK PENJUALAN -->
                <tr class="font-bold">
                    <td colspan="5">Harga Pokok Penjualan :</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Persediaan Produk Jadi Awal</td>
                    <td></td>
                    <td></td>
                    <td class="text-right">Rp {{ number_format($persediaanProdukJadiAwal, 2, ',', '.') }}</td>
                    <td></td>
                </tr>

                <!-- HARGA POKOK PRODUKSI -->
                <tr>
                    <td style="padding-left: 40px;" class="font-bold">Harga Pokok Produksi :</td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td style="padding-left: 60px; font-style: italic;">Persediaan BDP Awal</td>
                    <td></td>
                    <td class="text-right">Rp {{ number_format($persediaanBDPAwal, 2, ',', '.') }}</td>
                    <td></td><td></td>
                </tr>

                <!-- BIAYA PRODUKSI -->
                <tr>
                    <td style="padding-left: 60px; color: #666;">Biaya Produksi :</td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td style="padding-left: 80px;">&bull; Biaya Bahan Baku</td>
                    <td class="text-right">Rp {{ number_format($biayaBahanBaku, 2, ',', '.') }}</td>
                    <td></td><td></td><td></td>
                </tr>
                <tr>
                    <td style="padding-left: 80px;">&bull; Biaya Tenaga Kerja Langsung</td>
                    <td class="text-right">Rp {{ number_format($biayaTenagaKerjaLangsung, 2, ',', '.') }}</td>
                    <td></td><td></td><td></td>
                </tr>
                <tr>
                    <td style="padding-left: 80px;">&bull; Overhead Pabrik</td>
                    <td class="text-right border-bottom">Rp {{ number_format($biayaOverheadPabrik, 2, ',', '.') }} <span class="plus-sign">+</span></td>
                    <td></td><td></td><td></td>
                </tr>
                
                <!-- TOTAL BIAYA PRODUKSI -->
                <tr>
                    <td style="padding-left: 60px;" class="font-bold">Biaya Produksi</td>
                    <td></td>
                    <td class="text-right">Rp {{ number_format($totalBiayaProduksi, 2, ',', '.') }} <span class="plus-sign">+</span></td>
                    <td></td><td></td>
                </tr>

                <tr>
                    <td style="padding-left: 60px; font-style: italic;">Jumlah Biaya Produksi</td>
                    <td></td>
                    <td class="text-right border-top">Rp {{ number_format($persediaanBDPAwal + $totalBiayaProduksi, 2, ',', '.') }}</td>
                    <td></td><td></td>
                </tr>
                <tr>
                    <td style="padding-left: 60px; font-style: italic;">Persediaan BDP Akhir</td>
                    <td></td>
                    <td class="text-right border-bottom">Rp {{ number_format($persediaanBDPAkhir, 2, ',', '.') }} <span class="minus-sign">-</span></td>
                    <td></td><td></td>
                </tr>

                <!-- HARGA POKOK PRODUKSI FINAL -->
                <tr>
                    <td style="padding-left: 40px;" class="font-bold">Harga Pokok Produksi</td>
                    <td></td><td></td>
                    <td class="text-right">Rp {{ number_format($hargaPokokProduksi, 2, ',', '.') }} <span class="plus-sign">+</span></td>
                    <td></td>
                </tr>

                <tr>
                    <td style="padding-left: 20px; font-style: italic;">Barang Tersedia Dijual</td>
                    <td></td><td></td>
                    <td class="text-right border-top">Rp {{ number_format($persediaanProdukJadiAwal + $hargaPokokProduksi, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="padding-left: 20px; font-style: italic;">Persediaan Produk Jadi Akhir</td>
                    <td></td><td></td>
                    <td class="text-right border-bottom">Rp {{ number_format($persediaanProdukJadiAkhir, 2, ',', '.') }} <span class="minus-sign">-</span></td>
                    <td></td>
                </tr>

                <!-- HARGA POKOK PENJUALAN FINAL -->
                <tr class="font-bold">
                    <td class="uppercase">Harga Pokok Penjualan</td>
                    <td></td><td></td><td></td>
                    <td class="text-right border-bottom">Rp {{ number_format($hargaPokokPenjualan, 2, ',', '.') }} <span class="minus-sign">-</span></td>
                </tr>

                <!-- LABA KOTOR -->
                <tr class="font-bold" style="background-color: #f3f4f6;">
                    <td class="uppercase" style="font-size: 14px;">Laba Kotor</td>
                    <td></td><td></td><td></td>
                    <td class="text-right" style="font-size: 14px;">Rp {{ number_format($labaKotor, 2, ',', '.') }}</td>
                </tr>

                <tr><td colspan="5" style="padding: 10px 0;"></td></tr>

                <!-- BIAYA USAHA -->
                <tr class="font-bold">
                    <td colspan="5">Biaya Usaha :</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Biaya Pemasaran</td>
                    <td class="text-right">Rp {{ number_format($biayaPemasaran, 2, ',', '.') }}</td>
                    <td></td><td></td><td></td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Biaya Administrasi dan Umum</td>
                    <td class="text-right border-bottom">Rp {{ number_format($biayaAdmUmum, 2, ',', '.') }} <span class="plus-sign">+</span></td>
                    <td></td><td></td><td></td>
                </tr>
                <tr class="font-bold">
                    <td class="px-4">Total Biaya Usaha</td>
                    <td></td><td></td><td></td>
                    <td class="text-right border-bottom">Rp {{ number_format($totalBiayaUsaha, 2, ',', '.') }} <span class="minus-sign">-</span></td>
                </tr>

                <!-- LABA BERSIH USAHA -->
                <tr class="font-bold" style="background-color: #7a0e14; color: #fbbf24;">
                    <td class="uppercase" style="font-size: 18px; padding: 20px; letter-spacing: 2px;">Laba Bersih Usaha</td>
                    <td></td><td></td><td></td>
                    <td class="text-right" style="font-size: 18px; padding: 20px; font-weight: 900;">Rp {{ number_format($labaBersihUsaha, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
