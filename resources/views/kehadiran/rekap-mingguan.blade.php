@extends('layouts.app')

@section('title', 'Rekap Kehadiran Mingguan')
@section('page-title', 'Rekap Mingguan & Insentif Karyawan')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Rekap Mingguan & Insentif Karyawan</h4>
            <p class="text-muted mb-0">Lihat rekap kehadiran mingguan dan proses klaim insentif bonus Rp 30.000 secara langsung</p>
        </div>
        <div>
            <a href="{{ route('kehadiran.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-calendar-check me-2"></i>Absensi Harian
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-left: 4px solid #198754 !important;">
        <div class="d-flex">
            <div class="me-3 fs-4 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Klaim Berhasil!</h6>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-left: 4px solid #dc3545 !important;">
        <div class="d-flex">
            <div class="me-3 fs-4 text-danger">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Gagal Memproses Jurnal!</h6>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Date Week Picker -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light rounded">
        <form method="GET" action="{{ route('kehadiran.rekap-mingguan') }}" id="weekFilterForm" class="row align-items-center g-3">
            <div class="col-md-5">
                <label for="tanggal_mulai" class="form-label fw-semibold text-secondary">Pilih Tanggal dalam Minggu</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-week text-primary"></i></span>
                    <input type="date" class="form-control border-start-0" id="tanggal_mulai" name="tanggal_mulai" 
                           value="{{ $startStr }}" onchange="document.getElementById('weekFilterForm').submit();">
                </div>
                <small class="text-muted mt-1 d-block" style="font-size:12px;">Sistem akan mengambil satu minggu penuh (Senin - Sabtu) dari tanggal yang Anda pilih.</small>
            </div>
            <div class="col-md-7 text-md-end pt-md-3">
                <div class="p-3 bg-white d-inline-block rounded shadow-sm border border-light-subtle">
                    <span class="text-secondary fw-semibold">Rentang Periode Minggu Ini:</span>
                    <span class="fs-6 fw-bold text-primary ms-2">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ Carbon\Carbon::parse($startStr)->translatedFormat('d M Y') }} s/d {{ Carbon\Carbon::parse($endStr)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Incentive Explanation Card -->
<div class="card shadow-sm border-0 mb-4 bg-gradient-primary-dark text-white card-incentive-info">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="me-4 d-none d-md-flex align-items-center justify-content-center bg-white text-primary rounded-circle" style="width: 70px; height: 70px; min-width: 70px;">
                <i class="fas fa-award fa-2x"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-2 text-white">Syarat & Ketentuan Insentif Mingguan (Rp 30.000)</h5>
                <p class="mb-0 text-white-50" style="font-size:14px; line-height: 1.6;">
                    Pekerja berhak mendapatkan bonus insentif sebesar <strong>Rp 30.000</strong> per minggu jika memenuhi kriteria kehadiran prima:
                    <br>
                    <span class="badge bg-success-subtle text-success me-1 mt-1"><i class="fas fa-check-circle me-1"></i>Kehadiran Penuh 6 Hari (Senin s/d Sabtu)</span>
                    <span class="badge bg-success-subtle text-success mt-1"><i class="fas fa-history me-1"></i>Akumulasi Jam Kerja Minimum 48 Jam</span>
                    <br>
                    <span class="text-white-50 mt-1 d-inline-block" style="font-size: 13px;">
                        *Insentif mingguan <strong>tidak dialokasikan ke HPP harian</strong> demi menjaga ketepatan perhitungan real-time HPP, melainkan dijurnal langsung di akhir minggu ke Akun <strong>705 (Gaji Lainnya - insentif bonus)</strong> & <strong>212 (Hutang Gaji / Lainnya)</strong>.
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Presence Matrix -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
            <span class="badge bg-primary-subtle text-primary me-2 p-2 rounded-circle" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-clipboard-list"></i>
            </span>
            Matriks Rekap Kehadiran Mingguan Pekerja
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="rekapTable">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" style="width: 18%;">Pekerja</th>
                        <th class="text-center" style="width: 10%;">Senin</th>
                        <th class="text-center" style="width: 10%;">Selasa</th>
                        <th class="text-center" style="width: 10%;">Rabu</th>
                        <th class="text-center" style="width: 10%;">Kamis</th>
                        <th class="text-center" style="width: 10%;">Jumat</th>
                        <th class="text-center" style="width: 10%;">Sabtu</th>
                        <th class="text-center" style="width: 10%;">Presensi</th>
                        <th class="text-center" style="width: 12%;">Insentif Rp 30.000</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $row)
                        @php
                            $worker = $row['worker'];
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $worker->nama_tenaga }}</div>
                                <small class="text-muted" style="font-size:12px;">{{ $worker->kode_tenaga }} | {{ $worker->jabatan }}</small>
                            </td>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                @php
                                    $detail = $row['detail'][$day];
                                    $status = $detail['status'];
                                    $jam = floatval($detail['jam']);
                                @endphp
                                <td class="text-center">
                                    @if($status === 'hadir')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 d-block m-auto" style="max-width: 75px;" title="Jam Kerja: {{ $jam }} Jam">
                                            <i class="fas fa-check me-1"></i>{{ $jam }}h
                                        </span>
                                    @elseif($status === 'absen')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 d-block m-auto" style="max-width: 75px;">
                                            <i class="fas fa-times me-1"></i>Alpa
                                        </span>
                                    @elseif($status === 'izin')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 d-block m-auto" style="max-width: 75px;">
                                            <i class="fas fa-info-circle me-1"></i>Izin
                                        </span>
                                    @elseif($status === 'sakit')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 d-block m-auto" style="max-width: 75px;">
                                            <i class="fas fa-heart me-1"></i>Sakit
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1 d-block m-auto" style="max-width: 75px;">
                                            <i class="fas fa-minus me-1"></i>-
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-center">
                                <div class="fw-bold text-dark">{{ $row['hari_masuk'] }} Hari</div>
                                <small class="text-muted" style="font-size:12px;">Total: {{ $row['total_jam'] }} Jam</small>
                            </td>
                            <td class="text-center">
                                @if($row['dapat_insentif'])
                                    @if($row['sudah_klaim'])
                                        <span class="badge bg-success rounded-pill px-3 py-2 fw-semibold text-white shadow-sm">
                                            <i class="fas fa-check-double me-1"></i>Sudah Dijurnal
                                        </span>
                                    @else
                                        <form action="{{ route('kehadiran.insentif') }}" method="POST" class="d-inline confirm-incentive-form">
                                            @csrf
                                            <input type="hidden" name="id_tenaga" value="{{ $worker->id_tenaga }}">
                                            <input type="hidden" name="tanggal_akhir" value="{{ $endStr }}">
                                            <button type="button" class="btn btn-sm btn-success rounded-pill fw-semibold shadow-sm px-3 py-2 btn-klaim-insentif">
                                                <i class="fas fa-gift me-1"></i>Klaim Rp 30k
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="badge bg-light-subtle text-muted border border-light-subtle rounded-pill px-2 py-2" title="Syarat: Masuk 6 hari penuh & minimal 48 jam">
                                        <i class="fas fa-times-circle me-1"></i>Tidak Memenuhi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.btn-klaim-insentif').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Proses Jurnal Insentif?',
                text: "Sistem akan mendebit Akun Gaji Lainnya (705) dan mengkredit Hutang Gaji (212) sebesar Rp 30.000 untuk pekerja ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Jurnal Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<style>
    .bg-gradient-primary-dark {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .card-incentive-info {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.15) !important;
    }
    #rekapTable tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    #rekapTable tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.015);
    }
    .bg-light-subtle {
        background-color: #f8f9fa !important;
    }
    .btn-klaim-insentif {
        transition: all 0.2s ease-in-out;
    }
    .btn-klaim-insentif:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.35) !important;
    }
</style>
@endsection
