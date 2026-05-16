# 🚀 Quick Start: Utang Jangka Panjang

## ⚡ Instalasi Cepat

```bash
# 1. Migration sudah dijalankan ✅
php artisan migrate

# 2. (Opsional) Jalankan seeder untuk data contoh
php artisan db:seed --class=UtangJangkaPanjangSeeder
```

## 📍 Akses Fitur

**Menu:** Keuangan → Utang Jangka Panjang

## 🎯 Cara Pakai

### Input Utang Baru

1. Klik **"Tambah Utang Jangka Panjang"**
2. Isi form:
   - Tanggal & Nama Utang
   - Nominal (harus > 0)
   - **Akun Debit**: Kas (jika terima uang) / Aset (jika beli aset)
   - **Akun Kredit**: Utang Jangka Panjang
   - Jatuh Tempo
3. Simpan → **Jurnal otomatis dibuat!** ✅

## 🧾 Jurnal Otomatis

### Skenario 1: Terima Kas
```
Debit:  Kas/Bank
Kredit: Utang Jangka Panjang
```

### Skenario 2: Beli Aset
```
Debit:  Aset (Kendaraan/Gedung/dll)
Kredit: Utang Jangka Panjang
```

## 📊 Integrasi Neraca

✅ **Otomatis masuk ke Laporan Neraca**  
✅ **Bagian: Kewajiban (Liabilities)**  
✅ **Tidak perlu konfigurasi tambahan**

## ⚠️ Penting!

- **Hapus utang = hapus jurnal** (cascade delete)
- **Edit utang ≠ update jurnal** (jurnal dibuat saat create)
- **Jika perlu koreksi:** Hapus & buat ulang

## 🔍 Fitur Table

- ✅ Filter berdasarkan tanggal
- ✅ Filter utang jatuh tempo
- ✅ Badge merah untuk utang lewat jatuh tempo
- ✅ Lihat nomor jurnal terkait
- ✅ Sortable & searchable

## 📚 Dokumentasi Lengkap

Lihat: `UTANG_JANGKA_PANJANG_GUIDE.md`

## 🧪 Testing

```bash
# Jalankan seeder untuk data contoh
php artisan db:seed --class=UtangJangkaPanjangSeeder

# Cek di menu: Keuangan → Utang Jangka Panjang
# Cek jurnal: Laporan → Jurnal
# Cek neraca: Laporan → Laporan Posisi Keuangan
```

## ✅ Checklist

- [x] Migration
- [x] Model dengan relasi
- [x] Jurnal otomatis
- [x] Filament Resource
- [x] Validasi form
- [x] Cascade delete
- [x] Transaction safety
- [x] Integrasi neraca

---

**Status:** ✅ Ready to use!
