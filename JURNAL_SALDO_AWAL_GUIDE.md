# 📘 Panduan: Jurnal Otomatis Saldo Awal

## 🎯 Overview

Fitur ini membuat **jurnal otomatis** setiap kali Anda input saldo awal, sehingga:
- ✅ Buku besar terbentuk dari saldo awal
- ✅ Neraca akurat
- ✅ Tidak perlu input jurnal manual

---

## 🔧 Cara Kerja

### 1. Input Saldo Awal

Saat Anda input saldo awal:
```
Bulan: 1
Tahun: 2026
Akun: Kas
Nominal: Rp 10.000.000
```

### 2. Jurnal Otomatis Dibuat

Sistem otomatis membuat:

**Header Jurnal:**
```
Tanggal: 01-01-2026
No. Referensi: SALDO-AWAL-1-2026
Deskripsi: Jurnal Saldo Awal - Januari 2026
```

**Detail Jurnal:**
```
Debit:  Kas  Rp 10.000.000
Kredit: ...  (akan di-balance otomatis)
```

### 3. Balance Otomatis

Jika total debit ≠ total kredit, sistem otomatis menambahkan selisih ke **akun Modal**.

---

## 📊 Logic Debit/Kredit

### Akun yang Masuk Debit:
- ✅ **Aset** (header_akun = 1)
  - Kas, Bank, Piutang, Persediaan, dll
  - **Kecuali:** Akumulasi Penyusutan → Kredit
- ✅ **Beban** (header_akun = 5, 6, 7)
  - Beban Gaji, Beban Listrik, dll
- ✅ **Prive** (nama akun mengandung "prive")
  - Prive Pemilik

### Akun yang Masuk Kredit:
- ✅ **Kewajiban** (header_akun = 2)
  - Utang Usaha, Utang Bank, dll
- ✅ **Ekuitas** (header_akun = 3)
  - Modal, Laba Ditahan
  - **Kecuali:** Prive → Debit
- ✅ **Pendapatan** (header_akun = 4)
  - Pendapatan Jasa, Pendapatan Lain-lain
- ✅ **Akumulasi Penyusutan**
  - Kontra aset (kredit)

---

## 🧪 Contoh Kasus

### Kasus 1: Input Saldo Awal Kas

**Input:**
```
Bulan: 1
Tahun: 2026
Akun: Kas
Nominal: Rp 10.000.000
```

**Jurnal yang Dibuat:**
```
Tanggal: 01-01-2026
No. Ref: SALDO-AWAL-1-2026

Debit:  Kas    Rp 10.000.000
Kredit: Modal  Rp 10.000.000  ← Otomatis untuk balance
```

### Kasus 2: Input Beberapa Saldo Awal

**Input 1:**
```
Akun: Kas
Nominal: Rp 10.000.000
```

**Input 2:**
```
Akun: Bank BCA
Nominal: Rp 50.000.000
```

**Input 3:**
```
Akun: Utang Bank
Nominal: Rp 20.000.000
```

**Jurnal yang Dibuat:**
```
Tanggal: 01-01-2026
No. Ref: SALDO-AWAL-1-2026

Debit:  Kas           Rp 10.000.000
Debit:  Bank BCA      Rp 50.000.000
Kredit: Utang Bank    Rp 20.000.000
Kredit: Modal         Rp 40.000.000  ← Penyesuaian otomatis
────────────────────────────────────
Total:                Rp 60.000.000  Rp 60.000.000 ✅ Balance
```

### Kasus 3: Dengan Akumulasi Penyusutan

**Input:**
```
1. Kas: Rp 10.000.000
2. Kendaraan: Rp 100.000.000
3. Akumulasi Penyusutan Kendaraan: Rp 20.000.000
```

**Jurnal:**
```
Debit:  Kas                              Rp  10.000.000
Debit:  Kendaraan                        Rp 100.000.000
Kredit: Akumulasi Penyusutan Kendaraan  Rp  20.000.000
Kredit: Modal                            Rp  90.000.000
```

---

## 🚀 Cara Menggunakan

### Untuk Data Baru

1. **Input Saldo Awal**
   ```
   Master Data → Saldo Awal → Buat Saldo Awal
   ```

2. **Isi Form**
   - Bulan: 1
   - Tahun: 2026
   - Akun: Kas
   - Nominal: 10.000.000

3. **Simpan**
   - Jurnal otomatis dibuat! ✅

4. **Cek Jurnal**
   ```
   Laporan → Jurnal
   Cari: SALDO-AWAL-1-2026
   ```

### Untuk Data Lama (yang Sudah Ada)

Jika Anda sudah punya data saldo awal sebelum fitur ini:

```bash
# Generate jurnal untuk semua saldo awal yang belum punya jurnal
php artisan jurnal:generate-saldo-awal

# Atau untuk periode spesifik
php artisan jurnal:generate-saldo-awal --bulan=1 --tahun=2026
```

---

## 📋 Fitur Tambahan

### 1. Hindari Duplikasi

Sistem otomatis cek apakah jurnal sudah ada:
- Jika sudah ada jurnal dengan `no_referensi = SALDO-AWAL-{bulan}-{tahun}`
- Tidak akan membuat jurnal baru

### 2. Tracking Jurnal

Setiap saldo awal punya field `jurnal_id`:
- Bisa lihat jurnal mana yang terkait
- Bisa trace dari saldo awal ke jurnal

### 3. Auto Delete

Jika semua saldo awal untuk periode tertentu dihapus:
- Jurnal terkait otomatis dihapus
- Tidak ada jurnal orphan

---

## ⚠️ Catatan Penting

### Akun Modal Wajib Ada

Sistem butuh akun Modal untuk balance:
- Header akun = 3 (Ekuitas)
- Nama akun mengandung: "Modal", "Equity", atau "Capital"

**Jika tidak ada:**
```
Error: Akun Modal tidak ditemukan!
```

**Solusi:**
```
Buat akun baru:
- Nama: Modal Pemilik
- Header: 3 (Ekuitas)
- Kode: 311
```

### Saldo Normal Akun

Pastikan akun punya `header_akun` yang benar:
- 1 = Aset
- 2 = Kewajiban
- 3 = Ekuitas
- 4 = Pendapatan
- 5, 6, 7 = Beban

### Nominal Selalu Positif

Input nominal selalu positif:
- ✅ Kas: 10.000.000 (bukan -10.000.000)
- ✅ Utang: 20.000.000 (bukan -20.000.000)

Sistem otomatis menentukan debit/kredit berdasarkan jenis akun.

---

## 🔍 Troubleshooting

### Problem 1: Jurnal Tidak Terbuat

**Gejala:**
- Input saldo awal berhasil
- Tapi tidak ada jurnal

**Penyebab:**
- Error di service
- Akun Modal tidak ada

**Solusi:**
```bash
# Cek log error
tail -f storage/logs/laravel.log

# Pastikan akun Modal ada
php artisan tinker
>>> \App\Models\Akun::where('header_akun', 3)->where('nama_akun', 'like', '%Modal%')->first()

# Generate manual
php artisan jurnal:generate-saldo-awal --bulan=1 --tahun=2026
```

### Problem 2: Jurnal Tidak Balance

**Gejala:**
```
Error: Jurnal tidak balance! Debit: 100000, Kredit: 90000
```

**Penyebab:**
- Logic debit/kredit salah
- Akun Modal tidak bisa ditambahkan

**Solusi:**
- Cek apakah akun Modal ada
- Cek log untuk detail error

### Problem 3: Duplikasi Jurnal

**Gejala:**
- Ada 2 jurnal dengan no_referensi sama

**Penyebab:**
- Race condition (jarang terjadi)

**Solusi:**
```bash
# Hapus jurnal duplikat
php artisan tinker
>>> $jurnal = \App\Models\Jurnal::where('no_referensi', 'SALDO-AWAL-1-2026')->skip(1)->first();
>>> $jurnal->delete();
```

---

## 📊 Integrasi dengan Laporan

### Buku Besar

Saldo awal akan muncul di buku besar:
```
Buku Besar Kas - Januari 2026

Saldo Awal: Rp 10.000.000 ← Dari jurnal saldo awal

Transaksi:
01/01 - Terima dari customer    Rp  5.000.000
05/01 - Bayar listrik           Rp    500.000
...

Saldo Akhir: Rp 14.500.000
```

### Neraca

Saldo awal otomatis masuk ke neraca:
```
AKTIVA
  Kas                 Rp 10.000.000 ← Dari jurnal saldo awal
  Bank BCA            Rp 50.000.000
  
PASIVA
  Utang Bank          Rp 20.000.000
  Modal               Rp 40.000.000 ← Penyesuaian otomatis
```

---

## 🧪 Testing Checklist

- [ ] Input saldo awal baru → jurnal otomatis dibuat
- [ ] Cek jurnal → no_referensi = SALDO-AWAL-{bulan}-{tahun}
- [ ] Cek jurnal detail → debit = kredit (balance)
- [ ] Input saldo awal lagi (periode sama) → jurnal di-update
- [ ] Hapus saldo awal → jurnal ikut terhapus (jika semua dihapus)
- [ ] Cek buku besar → saldo awal muncul
- [ ] Cek neraca → saldo awal masuk perhitungan
- [ ] Generate untuk data lama → command berhasil

---

## 📁 File yang Dibuat/Diubah

### Baru
1. ✅ `database/migrations/2026_05_03_104233_add_jurnal_id_to_saldoawal_table.php`
2. ✅ `app/Services/SaldoAwalService.php`
3. ✅ `app/Console/Commands/GenerateJurnalSaldoAwal.php`

### Diubah
1. ✅ `app/Models/saldoawal.php` (tambah event & relasi)
2. ✅ `app/Models/Jurnal.php` (tambah relasi)

---

## ✅ Status

**SELESAI** - Siap digunakan!

- ✅ Migration berhasil
- ✅ Service sudah dibuat
- ✅ Event sudah aktif
- ✅ Command tersedia
- ✅ Dokumentasi lengkap

**Tanggal:** 3 Mei 2026
