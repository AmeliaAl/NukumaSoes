@extends('layouts.app')

@section('title', 'Absensi Harian')
@section('page-title', 'Absensi Harian & Alokasi Terpusat')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Absensi Harian & Alokasi Terpusat</h4>
            <p class="text-muted mb-0">Input absensi harian dan alokasikan upah secara otomatis ke Job Order aktif</p>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('kehadiran.rekap-mingguan') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-alt me-2"></i>Rekap Mingguan & Insentif
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
                <h6 class="alert-heading fw-bold mb-1">Berhasil!</h6>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-left: 4px solid #dc3545 !important;">
        <div class="d-flex">
            <div class="me-3 fs-4 text-danger">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Terjadi Kesalahan!</h6>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Date Picker Filter -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light rounded">
        <form method="GET" action="{{ route('kehadiran.index') }}" id="dateFilterForm" class="row align-items-center g-3">
            <div class="col-md-4">
                <label for="tanggal" class="form-label fw-semibold text-secondary">Tanggal Absensi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-alt text-primary"></i></span>
                    <input type="date" class="form-control border-start-0" id="tanggal" name="tanggal" 
                           value="{{ $date }}" onchange="document.getElementById('dateFilterForm').submit();">
                </div>
            </div>
            <div class="col-md-8 text-md-end pt-md-4">
                <span class="badge bg-info p-2 px-3 text-dark rounded-pill shadow-sm">
                    <i class="fas fa-clock me-1"></i> Hari Kerja Standar: 8 Jam
                </span>
                <span class="badge bg-warning p-2 px-3 text-dark rounded-pill shadow-sm ms-2">
                    <i class="fas fa-business-time me-1"></i> Jam Lembur: 1.5x Upah/Jam
                </span>
            </div>
        </form>
    </div>
</div>

<!-- Active Jobs & Cost Distribution Info -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
            <span class="badge bg-primary-subtle text-primary me-2 p-2 rounded-circle" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;">
                <i class="fas fa-cogs"></i>
            </span>
            Simulasi Alokasi Biaya ke Job Order Aktif
        </h5>
        <span class="badge bg-primary text-white rounded-pill px-3">{{ $activeJobs->count() }} Job Order Aktif</span>
    </div>
    <div class="card-body pt-0">
        @if($activeJobs->isEmpty())
            <div class="alert alert-warning border-0 shadow-sm mb-0">
                <div class="d-flex">
                    <div class="me-3 fs-3 text-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Tidak Ada Job Order Aktif Berstatus 'proses'</h6>
                        <p class="mb-0 text-muted" style="font-size:14px;">
                            Saat ini tidak ada Job Order dengan batch aktif pada tanggal <strong>{{ Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</strong>. 
                            Anda tetap dapat menginput absensi pekerja, namun upah kerja mereka hari ini <strong>tidak akan dialokasikan</strong> ke Job Order dan biaya HPP mana pun.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <p class="text-muted mb-3" style="font-size:14px;">
                Upah harian dari seluruh pekerja yang hadir hari ini akan dialokasikan secara <strong>proporsional</strong> berdasarkan jumlah batch aktif dari masing-masing Job Order. Tenaga kerja langsung masuk BTKL, tenaga kerja tidak langsung masuk BOP.
            </p>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @foreach($activeJobs as $job)
                    @php
                        $porsi = $totalBatchAktif > 0 ? ($job->batch_aktif_count / $totalBatchAktif) * 100 : 0;
                    @endphp
                    <div class="col">
                        <div class="card h-100 border border-light shadow-sm transition-hover">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-dark rounded-pill">{{ $job->nomor_job }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        Proses
                                    </span>
                                </div>
                                <h6 class="fw-bold text-truncate mb-1">{{ $job->produk->nama_produk ?? 'Produk' }}</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:13px;">
                                    <span class="text-muted">Batch Aktif:</span>
                                    <strong class="text-dark">{{ number_format($job->batch_aktif_count, 0) }} batch</strong>
                                </div>
                                <div class="progress mb-1 shadow-sm" style="height: 6px; border-radius: 3px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $porsi }}%" 
                                         aria-valuenow="{{ $porsi }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:12px;">
                                    <span class="text-muted">Porsi Alokasi:</span>
                                    <strong class="text-primary">{{ number_format($porsi, 1, ',', '.') }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 p-3 bg-light rounded border border-light-subtle d-flex justify-content-between align-items-center">
                <span class="text-secondary fw-semibold">Total Batch Aktif Tergabung:</span>
                <span class="fs-5 fw-bold text-dark">{{ number_format($totalBatchAktif, 0, ',', '.') }} Batch</span>
            </div>
        @endif
    </div>
</div>

<!-- Attendance Form -->
<form action="{{ route('kehadiran.store') }}" method="POST">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $date }}">
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <span class="badge bg-success-subtle text-success me-2 p-2 rounded-circle" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;">
                    <i class="fas fa-users-cog"></i>
                </span>
                Formulir Presensi Pekerja
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="attendanceTable">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th style="width: 15%;" class="ps-4">NIK</th>
                            <th style="width: 20%;">Nama Pekerja</th>
                            <th style="width: 15%;">Jenis</th>
                            <th style="width: 25%;" class="text-center">Status Kehadiran</th>
                            <th style="width: 10%;" class="text-center">Jam Kerja</th>
                            <th style="width: 10%;" class="text-center">Jam Lembur</th>
                            <th style="width: 15%;" class="pe-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workers as $worker)
                            @php
                                $existing = $existingKehadiran->get($worker->id_tenaga);
                                $status = $existing ? $existing->status_kehadiran : 'hadir';
                                $jamKerja = $existing ? floatval($existing->jam_kerja) : 8.0;
                                $jamLembur = $existing ? floatval($existing->jam_lembur) : 0.0;
                                $keterangan = $existing ? $existing->keterangan : '';
                            @endphp
                            <tr data-worker-id="{{ $worker->id_tenaga }}">
                                <td class="ps-4">
                                    <strong class="text-dark">{{ $worker->kode_tenaga }}</strong>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $worker->nama_tenaga }}</div>
                                    <small class="text-muted" style="font-size:12px;">{{ $worker->jabatan }} | Rp {{ number_format($worker->upah_per_jam, 0, ',', '.') }}/jam</small>
                                </td>
                                <td>
                                    @if($worker->jenis_tenaga === 'langsung')
                                        <span class="badge bg-primary rounded-pill">BTKL (Langsung)</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">BTKTL (Overhead)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group w-100" role="group" aria-label="Status Kehadiran">
                                        <!-- Hadir -->
                                        <input type="radio" class="btn-check btn-status" 
                                               name="attendance[{{ $worker->id_tenaga }}][status_kehadiran]" 
                                               id="status_hadir_{{ $worker->id_tenaga }}" 
                                               value="hadir" 
                                               data-worker-id="{{ $worker->id_tenaga }}"
                                               {{ $status === 'hadir' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success btn-sm py-2 px-3 fw-semibold" for="status_hadir_{{ $worker->id_tenaga }}">
                                            Hadir
                                        </label>

                                        <!-- Sakit -->
                                        <input type="radio" class="btn-check btn-status" 
                                               name="attendance[{{ $worker->id_tenaga }}][status_kehadiran]" 
                                               id="status_sakit_{{ $worker->id_tenaga }}" 
                                               value="sakit" 
                                               data-worker-id="{{ $worker->id_tenaga }}"
                                               {{ $status === 'sakit' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-info btn-sm py-2 px-3 fw-semibold" for="status_sakit_{{ $worker->id_tenaga }}">
                                            Sakit
                                        </label>

                                        <!-- Izin -->
                                        <input type="radio" class="btn-check btn-status" 
                                               name="attendance[{{ $worker->id_tenaga }}][status_kehadiran]" 
                                               id="status_izin_{{ $worker->id_tenaga }}" 
                                               value="izin" 
                                               data-worker-id="{{ $worker->id_tenaga }}"
                                               {{ $status === 'izin' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning text-dark btn-sm py-2 px-3 fw-semibold" for="status_izin_{{ $worker->id_tenaga }}">
                                            Izin
                                        </label>

                                        <!-- Absen -->
                                        <input type="radio" class="btn-check btn-status" 
                                               name="attendance[{{ $worker->id_tenaga }}][status_kehadiran]" 
                                               id="status_absen_{{ $worker->id_tenaga }}" 
                                               value="absen" 
                                               data-worker-id="{{ $worker->id_tenaga }}"
                                               {{ $status === 'absen' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger btn-sm py-2 px-3 fw-semibold" for="status_absen_{{ $worker->id_tenaga }}">
                                            Alpa
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="24" 
                                           class="form-control text-center input-jam-kerja border-light-subtle shadow-sm"
                                           name="attendance[{{ $worker->id_tenaga }}][jam_kerja]" 
                                           id="jam_kerja_{{ $worker->id_tenaga }}"
                                           value="{{ $jamKerja }}" 
                                           {{ $status !== 'hadir' ? 'readonly' : '' }} required>
                                </td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="24" 
                                           class="form-control text-center input-jam-lembur border-light-subtle shadow-sm"
                                           name="attendance[{{ $worker->id_tenaga }}][jam_lembur]" 
                                           id="jam_lembur_{{ $worker->id_tenaga }}"
                                           value="{{ $jamLembur }}" 
                                           {{ $status !== 'hadir' ? 'readonly' : '' }} required>
                                </td>
                                <td class="pe-4">
                                    <input type="text" class="form-control border-light-subtle shadow-sm" 
                                           name="attendance[{{ $worker->id_tenaga }}][keterangan]" 
                                           value="{{ $keterangan }}" 
                                           placeholder="Catatan...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-4 d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size: 13px;">
                <i class="fas fa-info-circle me-1"></i> Perubahan absensi akan menghitung ulang HPP Job Order terkait secara instan dan mencatat revisi Jurnal Umum.
            </span>
            <button type="submit" class="btn btn-success px-4 py-2 shadow-sm rounded-pill">
                <i class="fas fa-save me-2"></i>Simpan & Alokasikan Biaya
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Event handler ketika status presensi diubah
        $('.btn-status').on('change', function() {
            const workerId = $(this).data('worker-id');
            const status = $(this).val();
            const $jamKerjaInput = $('#jam_kerja_' + workerId);
            const $jamLemburInput = $('#jam_lembur_' + workerId);

            if (status === 'hadir') {
                $jamKerjaInput.prop('readonly', false).val(8.0);
                $jamLemburInput.prop('readonly', false).val(0.0);
            } else {
                $jamKerjaInput.prop('readonly', true).val(0.0);
                $jamLemburInput.prop('readonly', true).val(0.0);
            }
        });
    });
</script>
<style>
    .transition-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }
    #attendanceTable tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    #attendanceTable tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.015);
    }
    .btn-check:checked + .btn-outline-success {
        background-color: #198754 !important;
        color: white !important;
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.3) !important;
    }
    .btn-check:checked + .btn-outline-info {
        background-color: #0dcaf0 !important;
        color: #212529 !important;
        box-shadow: 0 2px 6px rgba(13, 202, 240, 0.3) !important;
    }
    .btn-check:checked + .btn-outline-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
        box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3) !important;
    }
    .btn-check:checked + .btn-outline-danger {
        background-color: #dc3545 !important;
        color: white !important;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3) !important;
    }
</style>
@endsection
