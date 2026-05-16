# 🔧 Troubleshooting: Laporan Posisi Keuangan

## ❓ Masalah Umum & Solusi

### 1. Utang Masih Tampil Negatif

**Gejala:**
```
Kewajiban
  Utang Jangka Panjang ⚠️  Rp-100.000.000
```

**Penyebab:**
Jurnal salah - Utang di-debit saat seharusnya di-kredit

**Solusi:**
1. Buka menu **Laporan → Jurnal**
2. Cari jurnal dengan akun "Utang Jangka Panjang"
3. Cek apakah ada jurnal seperti ini (SALAH):
   ```
   Debit:  Utang Jangka Panjang  Rp 100.000.000
   Kredit: Kas                   Rp 100.000.000
   ```
4. Hapus jurnal yang salah
5. Buat jurnal yang benar:
   ```
   Debit:  Kas                   Rp 100.000.000
   Kredit: Utang Jangka Panjang  Rp 100.000.000
   ```

---

### 2. Kas Negatif

**Gejala:**
```
Aktiva Lancar
  Kas ⚠️  Rp-50.000.000
```

**Penyebab:**
- Kas keluar lebih banyak dari kas masuk
- Ada transaksi yang tidak dicatat
- Jurnal salah

**Solusi:**
1. Cek saldo awal kas - apakah sudah dicatat?
2. Cek semua transaksi kas masuk - apakah sudah lengkap?
3. Cek jurnal kas - apakah ada yang terbalik?
4. Jika memang overdraft, catat sebagai utang bank

---

### 3. Neraca Tidak Balance

**Gejala:**
```
✗ Neraca Tidak Seimbang
Selisih: Rp 10.000.000
```

**Penyebab:**
- Ada jurnal yang tidak balance (debit ≠ kredit)
- Ada transaksi yang hanya dicatat sebagian

**Solusi:**
1. Buka menu **Laporan → Jurnal**
2. Cek setiap jurnal - pastikan total debit = total kredit
3. Cari jurnal yang dibuat manual (bukan otomatis)
4. Perbaiki atau hapus jurnal yang salah

---

### 4. Akumulasi Penyusutan Positif

**Gejala:**
```
Aktiva Tetap
  Akumulasi Penyusutan ⚠️  Rp 10.000.000
```

**Penyebab:**
Jurnal penyusutan terbalik

**Solusi:**
Jurnal penyusutan yang benar:
```
Debit:  Beban Penyusutan      Rp 10.000.000
Kredit: Akumulasi Penyusutan  Rp 10.000.000
```

Bukan:
```
Debit:  Akumulasi Penyusutan  Rp 10.000.000  ← SALAH
Kredit: Beban Penyusutan      Rp 10.000.000
```

---

### 5. Modal Negatif

**Gejala:**
```
Ekuitas
  Modal ⚠️  Rp-50.000.000
```

**Penyebab:**
- Jurnal setoran modal salah
- Prive/penarikan lebih besar dari modal

**Solusi:**
1. Cek jurnal setoran modal awal
2. Pastikan format:
   ```
   Debit:  Kas    Rp 50.000.000
   Kredit: Modal  Rp 50.000.000
   ```
3. Jika prive terlalu besar, tambah setoran modal

---

### 6. Peringatan Saldo Tidak Normal Tidak Muncul

**Gejala:**
Saldo negatif tapi tidak ada ⚠️

**Penyebab:**
- Cache browser
- Perlu refresh

**Solusi:**
1. Refresh halaman (F5)
2. Clear cache browser (Ctrl+Shift+R)
3. Ubah periode lalu kembali ke periode semula

---

### 7. Jurnal Otomatis Tidak Terbuat

**Gejala:**
Buat utang jangka panjang tapi tidak ada jurnal

**Penyebab:**
- Error di model
- Akun tidak valid

**Solusi:**
1. Cek log Laravel: `storage/logs/laravel.log`
2. Pastikan akun_id dan akun_debit_id valid
3. Cek apakah akun yang dipilih masih ada di database
4. Coba buat ulang dengan akun yang berbeda

---

### 8. Total Aktiva ≠ Total Pasiva (Tapi Jurnal Balance)

**Gejala:**
```
Total Aktiva  = Rp 100.000.000
Total Pasiva  = Rp  95.000.000
Selisih       = Rp   5.000.000
```

**Penyebab:**
- Ada akun yang tidak masuk kategori
- Kode akun salah

**Solusi:**
1. Cek tabel `akun` - pastikan semua akun punya `header_akun`
2. Pastikan kode akun konsisten:
   - 1xx = Aset
   - 2xx = Kewajiban
   - 3xx = Ekuitas
   - 4xx = Pendapatan
   - 5xx, 6xx, 7xx = Beban
3. Update kode akun yang salah

---

### 9. Laba Ditahan Salah

**Gejala:**
Laba Ditahan tidak sesuai dengan perhitungan manual

**Penyebab:**
- Ada akun pendapatan/beban yang tidak masuk kategori
- Periode tidak sesuai

**Solusi:**
1. Cek periode neraca - pastikan sesuai
2. Cek akun pendapatan (4xx) - pastikan lengkap
3. Cek akun beban (5xx, 6xx, 7xx) - pastikan lengkap
4. Hitung manual: Laba = Pendapatan - Beban

---

### 10. Data Tidak Update Setelah Input Jurnal

**Gejala:**
Buat jurnal baru tapi neraca tidak berubah

**Penyebab:**
- Cache
- Periode tidak mencakup tanggal jurnal

**Solusi:**
1. Refresh halaman
2. Cek tanggal jurnal vs periode neraca
3. Ubah periode neraca agar mencakup tanggal jurnal
4. Clear cache aplikasi: `php artisan cache:clear`

---

## 🔍 Cara Debugging

### Step 1: Cek Jurnal
```
Menu: Laporan → Jurnal
Filter: Akun yang bermasalah
Cek: Apakah debit = kredit?
```

### Step 2: Cek Buku Besar
```
Menu: Laporan → Buku Besar
Pilih: Akun yang bermasalah
Cek: Total debit dan total kredit
```

### Step 3: Cek Database
```sql
-- Cek saldo akun tertentu
SELECT 
    a.nama_akun,
    SUM(jd.debit) as total_debit,
    SUM(jd.credit) as total_kredit,
    SUM(jd.credit) - SUM(jd.debit) as saldo
FROM akun a
LEFT JOIN jurnal_detail jd ON jd.no_akun = a.id
LEFT JOIN jurnal j ON j.id = jd.id_jurnal
WHERE a.id = [ID_AKUN]
  AND j.tanggal <= '[TANGGAL_AKHIR]'
GROUP BY a.id;
```

### Step 4: Cek Log
```bash
# Lihat error log
tail -f storage/logs/laravel.log

# Cari error spesifik
grep "ERROR" storage/logs/laravel.log
```

---

## 📞 Bantuan Lebih Lanjut

### Jika Masalah Masih Berlanjut:

1. **Backup Database**
   ```bash
   php artisan backup:run
   ```

2. **Export Data**
   - Export jurnal ke Excel
   - Export akun ke Excel
   - Simpan sebagai backup

3. **Konsultasi**
   - Bawa file backup
   - Jelaskan masalah yang terjadi
   - Tunjukkan screenshot error

---

## ✅ Checklist Sebelum Lapor Bug

- [ ] Sudah refresh halaman
- [ ] Sudah clear cache browser
- [ ] Sudah cek log Laravel
- [ ] Sudah cek jurnal terkait
- [ ] Sudah coba periode berbeda
- [ ] Sudah screenshot error
- [ ] Sudah catat langkah reproduksi

---

## 🆘 Kontak Darurat

Jika ada masalah kritis:
1. Jangan hapus data
2. Backup database segera
3. Catat error message
4. Screenshot halaman error
5. Hubungi support

---

**Terakhir diupdate:** 3 Mei 2026
