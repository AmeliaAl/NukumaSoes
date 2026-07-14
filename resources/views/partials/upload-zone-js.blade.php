<script>
/**
 * Upload Zone — multiple file, preview, hapus per file
 *
 * Prinsip keamanan state form:
 * - Semua operasi file (pilih, hapus) HANYA menyentuh input file dan preview div.
 * - TIDAK ada form.reset(), TIDAK ada perubahan value input lain.
 * - e.stopPropagation() hanya pada tombol Hapus agar tidak trigger klik zone.
 * - DataTransfer digunakan untuk manipulasi FileList tanpa efek samping.
 */
(function () {

    function makeDataTransfer(files) {
        var dt = new DataTransfer();
        files.forEach(function (f) { dt.items.add(f); });
        return dt;
    }

    function bytesToSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function isImage(name) {
        return /\.(jpg|jpeg|png)$/i.test(name);
    }

    function renderPreview(inputId, files) {
        var preview = document.getElementById('preview_' + inputId);
        if (!preview) return;

        if (!files || files.length === 0) {
            preview.innerHTML = '';
            return;
        }

        var html = '<ul class="list-group mt-2" style="font-size:13px;">';
        Array.from(files).forEach(function (f, idx) {
            var icon = isImage(f.name)
                ? '<i class="fas fa-image text-primary mr-2"></i>'
                : '<i class="fas fa-file-pdf text-danger mr-2"></i>';
            html += '<li class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center">'
                  + '<span>' + icon
                  + '<span style="word-break:break-all;">' + f.name + '</span>'
                  + ' <small class="text-muted ml-1">(' + bytesToSize(f.size) + ')</small></span>'
                  + '<button type="button"'
                  + '  class="btn btn-outline-danger btn-sm py-0 px-2 ml-2 upload-remove-btn"'
                  + '  data-input-id="' + inputId + '"'
                  + '  data-file-idx="' + idx + '">'
                  + 'Hapus'
                  + '</button>'
                  + '</li>';
        });
        html += '</ul>';
        preview.innerHTML = html;
    }

    function initZone(zone) {
        var inputId  = zone.getAttribute('data-input-id');
        var maxFiles = parseInt(zone.getAttribute('data-max') || '5');
        var input    = document.getElementById(inputId);
        if (!input || zone._uploadInited) return;
        zone._uploadInited = true;

        // ── Klik zona → buka file picker ──────────────────────────────
        zone.addEventListener('click', function (e) {
            // Jangan trigger jika klik pada tombol Hapus
            if (e.target.closest('.upload-remove-btn')) return;
            // Cegah event bubble yang bisa trigger hal lain
            e.stopPropagation();
            input.click();
        });

        // ── Drag & Drop ───────────────────────────────────────────────
        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            zone.style.borderColor = '#495057';
            zone.style.background  = '#e9ecef';
        });

        zone.addEventListener('dragleave', function (e) {
            e.stopPropagation();
            zone.style.borderColor = '#adb5bd';
            zone.style.background  = '#f8f9fa';
        });

        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            zone.style.borderColor = '#adb5bd';
            zone.style.background  = '#f8f9fa';

            var dropped  = Array.from(e.dataTransfer.files);
            var existing = input.files ? Array.from(input.files) : [];
            var merged   = existing.concat(dropped).slice(0, maxFiles);

            input.files = makeDataTransfer(merged).files;
            renderPreview(inputId, input.files);
        });

        // ── Pilih via dialog ──────────────────────────────────────────
        input.addEventListener('change', function (e) {
            // e.stopPropagation agar tidak trigger event lain di form
            e.stopPropagation();
            var selected = Array.from(input.files).slice(0, maxFiles);
            input.files  = makeDataTransfer(selected).files;
            renderPreview(inputId, input.files);
        });
    }

    // ── Hapus satu file ────────────────────────────────────────────────
    // Delegasi ke document agar juga bekerja untuk zone yang di-render ulang.
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.upload-remove-btn');
        if (!btn) return;

        // Hentikan propagasi agar tidak trigger klik zone / form
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var inputId = btn.getAttribute('data-input-id');
        var idx     = parseInt(btn.getAttribute('data-file-idx'));
        var input   = document.getElementById(inputId);
        if (!input) return;

        // Hanya modifikasi FileList — tidak menyentuh input lain
        var files = Array.from(input.files);
        files.splice(idx, 1);
        input.files = makeDataTransfer(files).files;

        renderPreview(inputId, input.files);
    }, true); // useCapture=true agar berjalan sebelum handler jQuery/lain

    // ── Init ───────────────────────────────────────────────────────────
    function initAll() {
        document.querySelectorAll('.upload-zone[data-input-id]').forEach(initZone);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
</script>
