<!DOCTYPE html>
<html>

<head>

    <title>Buku Besar</title>

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

    <h2>BUKU BESAR</h2>

    <h4>

        Periode:
        {{ $periodeAwal ?? '-' }}
        s/d
        {{ $periodeAkhir ?? '-' }}

    </h4>

    <h4>

        Kode Akun :
        {{ $akun->kode_akun ?? '-' }}

    </h4>

    <h4>

        Nama Akun :
        {{ $akun->nama_akun ?? '-' }}

    </h4>

    <table>

        <thead>

            <tr>

                <th>No</th>
                <th>Tanggal</th>
                <th>Bukti</th>
                <th>Keterangan</th>
                <th>Ref</th>
                <th>Debit</th>
                <th>Kredit</th>
                <th>Saldo</th>

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
                    {{ $jurnal['bukti'] }}
                </td>

                <td>
                    {{ $jurnal['keterangan'] }}
                </td>

                <td>
                    {{ $jurnal['ref'] }}
                </td>

                <td class="text-right">

                    @if($jurnal['debit'] > 0)

                        Rp {{ number_format($jurnal['debit']) }}

                    @endif

                </td>

                <td class="text-right">

                    @if($jurnal['kredit'] > 0)

                        Rp {{ number_format($jurnal['kredit']) }}

                    @endif

                </td>

                <td class="text-right">

                   @if($jurnal['saldo'] < 0)

                        (Rp {{ number_format(abs($jurnal['saldo'])) }})

                    @else

                        Rp {{ number_format($jurnal['saldo']) }}

                    @endif

                </td>

            </tr>

            @endforeach

        </tbody>

        <tfoot>

            <tr>

                <th colspan="5">

                    TOTAL

                </th>

                <th class="text-right">

                    Rp {{ number_format($totalDebit) }}

                </th>

                <th class="text-right">

                    Rp {{ number_format($totalKredit) }}

                </th>

                <th class="text-right">

                    @if($saldoAkhir < 0)

                        (Rp {{ number_format(abs($saldoAkhir)) }})

                    @else

                         Rp {{ number_format($saldoAkhir) }}

                    @endif
                </th>

            </tr>

        </tfoot>

    </table>

</body>

</html>