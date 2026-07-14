{{--
    Komponen Upload Zone
    Props:
    - inputId   : ID unik untuk input file (string)
    - inputName : name attribute, default 'payment_proofs[]'
    - maxFiles  : default 5
    - label     : label yang tampil, default 'Dokumen Pembayaran'
--}}
@props([
    'inputId'   => 'upload_zone_' . uniqid(),
    'inputName' => 'payment_proofs[]',
    'maxFiles'  => 5,
    'label'     => 'Dokumen Pembayaran',
])

<div class="form-group">
    <label>{{ $label }}</label>

    {{-- Drop Zone --}}
    <div class="upload-zone"
         id="zone_{{ $inputId }}"
         data-input-id="{{ $inputId }}"
         data-max="{{ $maxFiles }}"
         style="border:2px dashed #adb5bd; border-radius:8px; padding:24px 16px;
                text-align:center; cursor:pointer; background:#f8f9fa;
                transition:border-color .2s, background .2s;">
        <div>
            <i class="fas fa-cloud-upload-alt fa-2x text-secondary mb-2"></i>
            <div class="text-muted" style="font-size:14px;">
                <strong>Klik atau seret file ke sini</strong><br>
                <small>JPG &bull; PNG &bull; PDF &bull; Maks. {{ $maxFiles }} file &bull; 2MB per file</small>
            </div>
        </div>
        <input type="file"
               id="{{ $inputId }}"
               name="{{ $inputName }}"
               accept=".jpg,.jpeg,.png,.pdf"
               multiple
               style="display:none;">
    </div>

    {{-- Preview list --}}
    <div id="preview_{{ $inputId }}" class="mt-2"></div>

    @error('payment_proofs')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @error('payment_proofs.*')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
