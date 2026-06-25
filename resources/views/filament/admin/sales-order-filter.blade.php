<style>
/* Reset form filter ke layout horizontal */
.fi-ta-filters {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 1rem !important;
    flex-wrap: wrap !important;
}

/* Mencegah form pembungkus mendorong tombol ke kanan */
.fi-ta-filters > *:not(.fi-ta-filters-header) {
    flex: 0 0 auto !important;
    width: auto !important;
}

/* Sembunyikan header asli "Filter" */
.fi-ta-filters-header { 
    display: none !important; 
}

/* Tambahkan label "Tanggal" buatan kita sendiri sebelum input */
.fi-ta-filters::before {
    content: "Tanggal" !important;
    font-weight: bold !important;
    color: #111827 !important;
}

/* Sembunyikan label bawaan input */
.fi-ta-filters .fi-fo-field-wrp-label { 
    display: none !important; 
}

/* Hapus tata letak grid dan jadikan sejajar */
.fi-ta-filters .fi-fo-components,
.fi-ta-filters .grid,
.fi-ta-filters form {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 1rem !important;
    margin: 0 !important;
}

/* Tombol Terapkan Filter dibuat sejajar dengan input */
.fi-ta-filters-actions-ctn { 
    margin: 0 !important; 
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
}
</style>
