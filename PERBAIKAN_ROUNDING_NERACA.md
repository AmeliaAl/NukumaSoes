# Perbaikan Rounding Neraca - Fix Selisih Rp 1-2

## Masalah
Neraca menunjukkan selisih kecil antara Total Aset dan Total Pasiva:
- **Aset**: Rp 610.096.026
- **Pasiva**: Rp 610.096.028
- **Selisih**: Rp 2

Padahal jurnal sudah balance (Total Debit = Total Kredit).

## Penyebab
**Inkonsistensi rounding** dalam perhitungan:
- ✅ Aset: Sudah dibulatkan per akun dengan `round($saldo, 0)`
- ❌ Liabilitas: Tidak dibulatkan (menggunakan nilai desimal penuh)
- ❌ Ekuitas: Tidak dibulatkan (menggunakan nilai desimal penuh)
- ❌ Laba: Tidak dibulatkan (menggunakan nilai desimal penuh)

Ketika beberapa nilai dibulatkan dan yang lain tidak, terjadi akumulasi selisih pembulatan yang menyebabkan perbedaan Rp 1-2.

## Solusi
Terapkan **rounding konsisten** di semua perhitungan:

### 1. Aset (Sudah Benar)
```php
// Aset Lancar
$this->totalAktivaLancar += round($saldo, 0);

// Aset Tetap
$this->totalAktivaTetap += round($saldo, 0);
```

### 2. Liabilitas (Diperbaiki)
```php
// SEBELUM
$this->totalLiabilitas += $saldo;

// SESUDAH
$this->totalLiabilitas += round($saldo, 0); // Bulatkan per akun
```

### 3. Ekuitas (Diperbaiki)
```php
// SEBELUM
$this->totalEkuitas += $saldo;

// SESUDAH
$this->totalEkuitas += round($saldo, 0); // Bulatkan per akun
```

### 4. Laba (Diperbaiki)
```php
// SEBELUM
$laba = $pendapatan - $beban;

// SESUDAH
$laba = round($pendapatan - $beban, 0); // Bulatkan laba
```

## Hasil
Setelah perbaikan:
- ✅ Total Aset = Total Pasiva (balance sempurna)
- ✅ Tidak ada selisih Rp 1-2 lagi
- ✅ Semua perhitungan menggunakan rounding konsisten
- ✅ Jurnal tetap balance (Debit = Kredit)

## Prinsip Rounding
1. **Bulatkan per akun** saat menambahkan ke total
2. **Gunakan `round($nilai, 0)`** untuk bulatkan ke rupiah penuh
3. **Terapkan di semua kategori**: Aset, Liabilitas, Ekuitas, Laba
4. **Konsisten**: Jangan ada yang dibulatkan sebagian

## File yang Diubah
- `app/Filament/Admin/Pages/NeracaPage.php`
  - Line ~237: Tambah rounding di totalLiabilitas
  - Line ~293: Tambah rounding di totalEkuitas
  - Line ~343: Tambah rounding di perhitungan laba

## Testing
Untuk memverifikasi perbaikan:
1. Buka Laporan Posisi Keuangan
2. Pilih periode Juni 2026
3. Cek Total Aset vs Total Pasiva
4. Aktifkan Debug Panel (tombol "Toggle Debug")
5. Lihat "Selisih Neraca" di Summary - harus 0 atau < Rp 1

## Catatan Teknis
- Rounding dilakukan dengan `round($nilai, 0)` = bulatkan ke integer terdekat
- PHP `round()` menggunakan "round half up" (0.5 dibulatkan ke atas)
- Rounding diterapkan **sebelum** ditambahkan ke total, bukan setelah
- Ini memastikan tidak ada akumulasi desimal yang menyebabkan selisih

---
**Tanggal**: 16 Mei 2026  
**Status**: ✅ Selesai  
**Tested**: Juni 2026
