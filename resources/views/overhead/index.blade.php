@extends('adminlte::page')

@section('title', 'Overhead')

@section('content_header')
    <h1>Data Overhead</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">
    <div class="card-header">
        <a href="{{ route('overhead.create') }}" class="btn btn-primary">
            + Tambah Overhead
        </a>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis Periode</th>
                    <th>Periode Pembebanan</th>
                    <th>Akun Overhead</th>
                    <th>Akun Pembayaran</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overheads as $overhead)
                    @php $rowspan = max(1, $overhead->details->count()); @endphp
                    <tr>
                        <td rowspan="{{ $rowspan }}">{{ $loop->iteration }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $overhead->tanggal }}</td>
                        <td rowspan="{{ $rowspan }}">
                            @php
                                $labelPeriode = ['harian'=>'Harian','mingguan'=>'Mingguan','bulanan'=>'Bulanan'];
                            @endphp
                            {{ $labelPeriode[$overhead->jenis_periode ?? 'harian'] ?? ucfirst($overhead->jenis_periode ?? 'harian') }}
                        </td>
                        <td rowspan="{{ $rowspan }}">
                            @if(($overhead->jenis_periode ?? 'harian') === 'harian')
                                {{ $overhead->periode_mulai ? \Carbon\Carbon::parse($overhead->periode_mulai)->format('d/m/Y') : \Carbon\Carbon::parse($overhead->tanggal)->format('d/m/Y') }}
                            @elseif(($overhead->jenis_periode ?? '') === 'bulanan')
                                {{ $overhead->periode_mulai ? \Carbon\Carbon::parse($overhead->periode_mulai)->translatedFormat('F Y') : '-' }}
                            @else
                                {{ $overhead->periode_mulai ? \Carbon\Carbon::parse($overhead->periode_mulai)->format('d/m/Y') : '-' }}
                                @if($overhead->periode_akhir) – {{ \Carbon\Carbon::parse($overhead->periode_akhir)->format('d/m/Y') }} @endif
                            @endif
                        </td>
                        
                        @if($overhead->details->count() > 0)
                            <td>{{ $overhead->details[0]->coa->nama_akun ?? '-' }}</td>
                            <td>{{ optional($overhead->details[0]->paymentCoa)->nama_akun ?? '-' }}</td>
                            <td>{{ $overhead->details[0]->keterangan }}</td>
                            <td>{{ \App\Helpers\FormatHelper::rupiah($overhead->details[0]->nominal) }}</td>
                        @else
                            <td>{{ $overhead->coa->nama_akun ?? '-' }}</td>
                            <td>{{ optional($overhead->paymentCoa)->nama_akun ?? '-' }}</td>
                            <td>{{ $overhead->keterangan }}</td>
                            <td>{{ \App\Helpers\FormatHelper::rupiah($overhead->nominal) }}</td>
                        @endif

                        <td rowspan="{{ $rowspan }}">
                        @if($overhead->paymentProofs->count())
                            <button type="button" class="btn btn-info btn-sm mb-1" data-toggle="modal" data-target="#modalProofOverhead{{ $overhead->id }}">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                        @endif
                        <a href="{{ route('overhead.edit', $overhead->id) }}" class="btn btn-warning btn-sm">
                            Edit
                        </a>
                    </td>
                    </tr>
                    @for($i = 1; $i < $rowspan; $i++)
                    <tr>
                        <td>{{ $overhead->details[$i]->coa->nama_akun ?? '-' }}</td>
                        <td>{{ optional($overhead->details[$i]->paymentCoa)->nama_akun ?? '-' }}</td>
                        <td>{{ $overhead->details[$i]->keterangan }}</td>
                        <td>{{ \App\Helpers\FormatHelper::rupiah($overhead->details[$i]->nominal) }}</td>
                    </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>

        @foreach($overheads as $overhead)
            @if($overhead->paymentProofs->count())
                <div class="modal fade modal-proof" id="modalProofOverhead{{ $overhead->id }}" tabindex="-1" aria-labelledby="modalProofLabelOverhead{{ $overhead->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalProofLabelOverhead{{ $overhead->id }}">Bukti Pembayaran: Overhead #{{ $overhead->id }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="list-group">
                                            @foreach($overhead->paymentProofs as $proof)
                                                <button type="button" class="list-group-item list-group-item-action proof-item" data-preview-target="#proofPreviewOverhead{{ $overhead->id }}" data-url="{{ asset('storage/' . $proof->file_path) }}" data-name="{{ $proof->file_name }}">
                                                    {{ $proof->file_name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="proofPreviewOverhead{{ $overhead->id }}" style="min-height: 350px;">
                                            <p class="text-muted">Pilih file bukti untuk melihat preview.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ asset('storage/' . $overhead->paymentProofs->first()->file_path) }}" target="_blank" class="btn btn-primary" id="proofOpenLinkOverhead{{ $overhead->id }}">Buka file</a>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

@stop

@section('js')
<script>
    $(document).ready(function () {
        $('.table').DataTable({
            ordering: false
        });
        function renderProofPreview(button) {
            const url = button.data('url');
            const name = button.data('name');
            const target = button.data('preview-target');
            const preview = $(target);
            const fileExt = (name || url).split('.').pop().toLowerCase();
            let html = '';

            if (fileExt === 'pdf') {
                html = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="' + url + '" allowfullscreen></iframe></div>';
            } else {
                html = '<img src="' + url + '" class="img-fluid" alt="' + name + '">';
            }

            html += '<p class="mt-2"><a href="' + url + '" target="_blank" class="btn btn-sm btn-primary">Buka di tab baru</a></p>';
            preview.html(html);
            const modalId = $(target).closest('.modal').attr('id');
            $('#proofOpenLink' + modalId.replace('modalProof', '')).attr('href', url);
        }

        $('body').on('click', '.proof-item', function () {
            $('.proof-item').removeClass('active');
            $(this).addClass('active');
            renderProofPreview($(this));
        });

        $('.modal-proof').on('shown.bs.modal', function () {
            $(this).find('.proof-item').first().trigger('click');
        });
    });
</script>
@stop
