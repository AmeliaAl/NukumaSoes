<!DOCTYPE html>
<html>

<head>

    <title>Laporan Overhead</title>

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

    </style>

</head>

<body>

    <h2>LAPORAN OVERHEAD</h2>

    <h4>

        Periode:
        {{ $tanggalAwal ?? '-' }}
        s/d
        {{ $tanggalAkhir ?? '-' }}

    </h4>

    <table>

        <thead>

            <tr>

                <th>No</th>
                <th>Tanggal</th>
                <th>Akun Overhead</th>
                <th>Keterangan</th>
                <th>Nominal</th>

            </tr>

        </thead>

        <tbody>

            @foreach($overheads as $overhead)

            <tr>

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $overhead->tanggal }}
                </td>

                <td>
                    {{ $overhead->coa->nama_akun }}
                </td>

                <td>
                    {{ $overhead->keterangan }}
                </td>

                <td>

                    Rp {{ number_format($overhead->nominal, 0, ',', '.') }}

                </td>

            </tr>

            @endforeach

        </tbody>
        <tfoot>

        <tr>

            <th colspan="4" class="text-center">

                TOTAL

            </th>

            <th>

                Rp {{ number_format($total, 0, ',', '.') }}

            </th>

        </tr>

    </tfoot>

    </table>


</body>

</html>