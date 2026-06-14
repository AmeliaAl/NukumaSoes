<!DOCTYPE html>
<html>

<head>

    <title>Jurnal Umum</title>

    <style>

        @page {
            size: A4 landscape;
            margin: 15px;
        }

        body {

            font-family: sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 5px;

        }

        h2, h4 {

            text-align: center;
            margin: 0;

        }

        table {

            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;

        }

        table, th, td {

            border: 1px solid black;

        }

        th, td {

            padding: 6px;
            text-align: center;
            vertical-align: middle;

        }

        th {
            background: #f0f0f0;
        }

        .text-right {

            text-align: right;

        }

    </style>

</head>

<body>

    <h2>JURNAL UMUM</h2>

    <h4>

        Periode:
        {{ $periodeAwal ?? '-' }}
        s/d
        {{ $periodeAkhir ?? '-' }}

    </h4>

    <table>

        <thead>

            <tr>

                <th>No</th>
                <th>Tanggal</th>
                <th>No Bukti</th>
                <th>Keterangan</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Debit</th>
                <th>Kredit</th>

            </tr>

        </thead>

        <tbody>

            @foreach($jurnals as $jurnal)

            <tr>

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $jurnal['tanggal'] }}
                </td>

                <td>
                    {{ $jurnal['no_bukti'] }}
                </td>

                <td>
                    {{ $jurnal['keterangan'] }}
                </td>

                <td>
                    {{ $jurnal['kode_akun'] }}
                </td>

                <td>
                    {{ $jurnal['nama_akun'] }}
                </td>

                <td class="text-right">

                    @if($jurnal['debit'] > 0)

                        Rp {{ number_format($jurnal['debit'], 0, ',', '.') }}

                    @endif

                </td>

                <td class="text-right">

                    @if($jurnal['kredit'] > 0)

                        Rp {{ number_format($jurnal['kredit'], 0, ',', '.') }}

                    @endif

                </td>

            </tr>

            @endforeach

        </tbody>

        <tfoot>

            <tr>

                <th colspan="6">

                    TOTAL

                </th>

                <th class="text-right">

                    Rp {{ number_format($totalDebit, 0, ',', '.') }}

                </th>

                <th class="text-right">

                    Rp {{ number_format($totalKredit, 0, ',', '.') }}

                </th>

            </tr>

        </tfoot>

    </table>

</body>

</html>