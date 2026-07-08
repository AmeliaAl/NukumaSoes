<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Biaya Produksi - {{ $jobOrder->nomor_job }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Screen Styles */
        @media screen {
            body {
                background-color: #f8f9fa;
                padding: 20px;
            }
            
            .no-print {
                display: block !important;
            }
            
            .print-content {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
        }

        /* Print Styles */
        @media print {
            /* Force hide everything first */
            * {
                visibility: hidden;
            }
            
            /* Then show only print content */
            body,
            .print-content,
            .print-content * {
                visibility: visible;
            }
            
            /* Reset body */
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            
            /* Position print content at top */
            .print-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 15mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            
            /* Hide buttons and non-printable elements */
            .no-print,
            nav,
            .navbar,
            .sidebar,
            aside,
            .btn,
            button,
            .alert,
            header,
            footer,
            .breadcrumb {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Page setup */
            @page {
                size: A4;
                margin: 15mm;
            }
            
            /* Prevent page breaks */
            h1, h2, h3, h4, h5, h6 {
                page-break-after: avoid;
            }
            
            table {
                page-break-inside: avoid;
            }
            
            .signature-section {
                page-break-inside: avoid;
            }
            
            /* Ensure colors print */
            .table-primary,
            .bg-primary {
                background-color: #0d6efd !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .table-success,
            .bg-success {
                background-color: #198754 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .table-warning,
            .bg-warning {
                background-color: #ffc107 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .table-light {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .border-dark {
                border-color: #000 !important;
            }
            
            .table-bordered {
                border: 2px solid #000 !important;
            }
            
            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000 !important;
            }
        }
        
        /* Common Styles */
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
        }
        
        .print-header h1 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .print-header h2 {
            font-size: 16px;
            color: #666;
        }
        
        .section-header {
            font-weight: bold;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-size: 14px;
        }
        
        .signature-section {
            margin-top: 50px;
        }
        
        .signature-col {
            text-align: center;
        }
        
        .signature-space {
            height: 60px;
        }
        
        .signature-name {
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
            min-width: 150px;
            font-weight: bold;
        }
        
        .signature-title {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Button Bar - Hidden on Print -->
    <div class="no-print mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-file-invoice me-2"></i>Kartu Biaya Produksi</h3>
            <div>
                <a href="{{ route('laporan.biaya-produksi.show', $jobOrder->id_permintaan_produksi) }}" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i> Cetak
                </button>
                <a href="{{ route('laporan.biaya-produksi.pdf', $jobOrder->id_permintaan_produksi) }}" 
                   class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Print Content -->
    <div class="print-content">
        <!-- Header -->
        <div class="print-header">
            <h1>KARTU BIAYA PRODUKSI</h1>
            <h2>Job Order Costing System</h2>
        </div>

        <!-- Info Job Order -->
        <div class="row mb-4">
            <div class="col-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>No. Job Order</strong></td>
                        <td>: <strong>{{ $jobOrder->nomor_job }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Produk</strong></td>
                        <td>: {{ $jobOrder->produk->nama_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Order</strong></td>
                        <td>: {{ number_format($jobOrder->jumlah_produksi, 0, ',', '.') }} {{ $jobOrder->produk->satuan_produk ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Batch</strong></td>
                        <td>: {{ $jobOrder->jumlah_batch ?? 1 }} Batch</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Produksi</strong></td>
                        <td>: {{ $jobOrder->jenis_produksi == 'maklun' ? 'Maklun' : 'Brand Sendiri' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>Tanggal Mulai</strong></td>
                        <td>: {{ \Carbon\Carbon::parse($jobOrder->tanggal_mulai)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Selesai</strong></td>
                        <td>: {{ $jobOrder->tanggal_selesai ? \Carbon\Carbon::parse($jobOrder->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>: <span class="badge bg-success">SELESAI</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Bahan Baku -->
        <div class="section-header bg-primary text-white">BIAYA BAHAN BAKU LANGSUNG</div>
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th width="40" class="text-center">No</th>
                    <th>Nama Bahan Baku</th>
                    <th width="80" class="text-center">Satuan</th>
                    <th width="100" class="text-end">Jumlah</th>
                    <th width="130" class="text-end">Harga/Satuan</th>
                    <th width="130" class="text-end">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobOrder->pemakaianBahanBakuLangsung as $index => $pemakaian)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $pemakaian->bahanBaku->nama_bahan ?? '-' }}</td>
                    <td class="text-center">{{ $pemakaian->bahanBaku->satuan ?? '-' }}</td>
                    <td class="text-end">{{ number_format($pemakaian->jumlah_pakai, 2, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($pemakaian->harga_satuan ?? ($pemakaian->harga_per_satuan ?? 0), 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($pemakaian->total_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="5" class="text-end">TOTAL BIAYA BAHAN BAKU:</th>
                    <th class="text-end">Rp {{ number_format($jobOrder->total_biaya_bahan ?? 0, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Tenaga Kerja -->
        <div class="section-header bg-success text-white">BIAYA TENAGA KERJA LANGSUNG</div>
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th width="40" class="text-center">No</th>
                    <th>Nama Tenaga Kerja</th>
                    <th width="80" class="text-center">Jenis</th>
                    <th width="80" class="text-end">Jam</th>
                    <th width="60" class="text-center">Batch</th>
                    <th width="120" class="text-end">Upah/Jam</th>
                    <th width="120" class="text-end">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobOrder->biayaTenagaKerjaLangsung as $index => $biaya)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $biaya->tenagaKerja->nama_tenaga ?? '-' }}</td>
                    <td class="text-center">
                        @if($biaya->tenagaKerja->jenis_tenaga == 'langsung')
                            <span class="badge bg-primary">LANGSUNG</span>
                        @else
                            <span class="badge bg-secondary text-white">TDK LANGSUNG</span>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($biaya->jam_kerja ?? 0, 1, ',', '.') }} jam</td>
                    <td class="text-center">{{ $biaya->jumlah_batch ?? 1 }}</td>
                    <td class="text-end">Rp {{ number_format($biaya->upah_per_jam ?? 0, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($biaya->total_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="6" class="text-end">TOTAL BIAYA TENAGA KERJA:</th>
                    <th class="text-end">Rp {{ number_format($jobOrder->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Overhead -->
        <div class="section-header bg-warning text-dark">BIAYA OVERHEAD PABRIK</div>
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
                    'jenis' => 'Bahan Penolong / BOP',
                    'keterangan' => 'Pemakaian ' . ($bh->bahanBaku->nama_bahan ?? '') . ' (' . number_format($bh->jumlah_pakai, 2) . ' ' . ($bh->bahanBaku->satuan ?? '') . ')',
                    'batch' => $jobOrder->jumlah_batch ?? 1,
                    'biaya' => $bh->total_biaya
                ]);
            }
        @endphp

        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th width="40" class="text-center">No</th>
                    <th>Jenis Biaya</th>
                    <th>Keterangan</th>
                    <th width="60" class="text-center">Batch</th>
                    <th width="150" class="text-end">Jumlah Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allBop as $index => $overhead)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $overhead['jenis'] }}</td>
                    <td>{{ $overhead['keterangan'] ?? '-' }}</td>
                    <td class="text-center">{{ $overhead['batch'] ?? 1 }}</td>
                    <td class="text-end">Rp {{ number_format($overhead['biaya'], 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end">TOTAL BIAYA OVERHEAD:</th>
                    <th class="text-end">Rp {{ number_format($jobOrder->total_biaya_overhead ?? 0, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Summary -->
        <div class="row mt-4">
            <div class="col-6 offset-6">
                <div class="border border-dark border-2 p-3">
                    <h5 class="text-center mb-3"><strong>RINGKASAN BIAYA PRODUKSI</strong></h5>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Biaya Bahan Baku Langsung</td>
                            <td class="text-end">Rp {{ number_format($jobOrder->total_biaya_bahan ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Biaya Tenaga Kerja Langsung</td>
                            <td class="text-end">Rp {{ number_format($jobOrder->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Biaya Overhead Pabrik</td>
                            <td class="text-end">Rp {{ number_format($jobOrder->total_biaya_overhead ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top border-dark border-2">
                            <td><strong>TOTAL BIAYA PRODUKSI</strong></td>
                            <td class="text-end"><strong>Rp {{ number_format($jobOrder->total_biaya_produksi ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>JUMLAH PRODUKSI</strong></td>
                            <td class="text-end"><strong>{{ number_format($jobOrder->jumlah_produksi, 0, ',', '.') }} {{ $jobOrder->produk->satuan_produk ?? '' }}</strong></td>
                        </tr>
                        <tr class="border-top border-dark border-2 bg-success bg-opacity-10">
                            <td><strong>HPP / UNIT</strong></td>
                            <td class="text-end"><strong class="fs-5">Rp {{ number_format($jobOrder->harga_pokok_per_unit ?? 0, 0, ',', '.') }} / {{ $jobOrder->produk->satuan_produk ?? 'Unit' }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="row">
                <div class="col-4 signature-col">
                    <p>Dibuat Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $jobOrder->admin->nama ?? '_______________' }}</p>
                    <p class="signature-title">Admin Produksi</p>
                </div>
                <div class="col-4 signature-col">
                    <p>Diperiksa Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">_______________</p>
                    <p class="signature-title">Supervisor Produksi</p>
                </div>
                <div class="col-4 signature-col">
                    <p>Disetujui Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">_______________</p>
                    <p class="signature-title">Manager Produksi</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 pt-3 border-top">
            <small class="text-muted">Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
        </div>
    </div>
</body>
</html>
