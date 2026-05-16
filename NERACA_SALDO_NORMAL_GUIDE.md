# 📚 Panduan Saldo Normal Akun - Laporan Posisi Keuangan

## 🎯 Quick Reference

### Rumus Saldo Normal

| Jenis Akun | Saldo Normal | Rumus Perhitungan | Tampilan |
|------------|--------------|-------------------|----------|
| **Aset** | Debit | `Total Debit - Total Kredit` | Positif |
| **Kewajiban** | Kredit | `Total Kredit - Total Debit` | Positif |
| **Modal** | Kredit | `Total Kredit - Total Debit` | Positif |
| **Pendapatan** | Kredit | `Total Kredit - Total Debit` | → Laba |
| **Beban** | Debit | `Total Debit - Total Kredit` | → Laba |

### Akun Khusus (Kontra)

| Akun | Saldo Normal | Tampilan di Neraca |
|------|--------------|-------------------|
| **Akumulasi Penyusutan** | Kredit | Negatif (pengurang aset) |
| **Prive** | Debit | Negatif (pengurang ekuitas) |

---

## 💡 Contoh Praktis

### 1. Kas (Aset)

**Jurnal:**
```
Terima uang:
  Debit:  Kas  Rp 1.000.000
  Kredit: ...  Rp 1.000.000

Bayar sesuatu:
  Debit:  ...  Rp 300.000
  Kredit: Kas  Rp 300.000
```

**Perhitungan:**
```
Total Debit  = Rp 1.000.000
Total Kredit = Rp   300.000
Saldo        = Rp   700.000 ✅ (positif)
```

### 2. Utang Jangka Panjang (Kewajiban)

**Jurnal:**
```
Terima pinjaman:
  Debit:  Kas                 Rp 5.000.000
  Kredit: Utang Jk Panjang    Rp 5.000.000

Bayar cicilan:
  Debit:  Utang Jk Panjang    Rp 1.000.000
  Kredit: Kas                 Rp 1.000.000
```

**Perhitungan:**
```
Total Kredit = Rp 5.000.000
Total Debit  = Rp 1.000.000
Saldo        = Rp 4.000.000 ✅ (positif)
```

### 3. Modal (Ekuitas)

**Jurnal:**
```
Setoran modal:
  Debit:  Kas    Rp 10.000.000
  Kredit: Modal  Rp 10.000.000
```

**Perhitungan:**
```
Total Kredit = Rp 10.000.000
Total Debit  = Rp          0
Saldo        = Rp 10.000.000 ✅ (positif)
```

### 4. Akumulasi Penyusutan (Kontra Aset)

**Jurnal:**
```
Penyusutan bulanan:
  Debit:  Beban Penyusutan      Rp 500.000
  Kredit: Akumulasi Penyusutan  Rp 500.000
```

**Perhitungan:**
```
Total Kredit = Rp 500.000
Total Debit  = Rp       0
Saldo        = Rp 500.000 (kredit)
```

**Di Neraca:**
```
Aktiva Tetap
  Kendaraan                 Rp 10.000.000
  Akumulasi Penyusutan      Rp   -500.000  ← Negatif (pengurang)
  ─────────────────────────────────────────
  Total Aktiva Tetap        Rp  9.500.000
```

---

## ⚠️ Saldo Tidak Normal

### Apa itu Saldo Tidak Normal?

Saldo tidak normal terjadi ketika akun memiliki saldo yang berlawanan dengan saldo normalnya.

### Contoh Saldo Tidak Normal

#### Kas Negatif ❌
```
Total Debit  = Rp 1.000.000
Total Kredit = Rp 1.500.000
Saldo        = Rp  -500.000 ⚠️ ABNORMAL
```
**Artinya:** Kas keluar lebih banyak dari kas masuk (overdraft)

#### Utang Negatif ❌
```
Total Kredit = Rp 1.000.000
Total Debit  = Rp 1.500.000
Saldo        = Rp  -500.000 ⚠️ ABNORMAL
```
**Artinya:** Bayar utang lebih banyak dari utang yang ada (jurnal salah)

### Cara Mendeteksi di Sistem

Sistem otomatis mendeteksi saldo tidak normal dengan:
- **Indikator ⚠️** di samping nama akun
- **Warna merah** pada nilai saldo
- **Peringatan** di bawah neraca dengan daftar lengkap

---

## 🔍 Cara Memperbaiki Saldo Abnormal

### Langkah 1: Identifikasi Masalah
Lihat peringatan di bawah neraca:
```
⚠️ Peringatan: Ditemukan 1 Akun dengan Saldo Tidak Normal

Utang Jangka Panjang: Rp-500.000
Kewajiban seharusnya kredit
```

### Langkah 2: Cek Jurnal
1. Buka menu **Laporan → Jurnal**
2. Filter berdasarkan akun yang bermasalah
3. Cari transaksi yang mencurigakan

### Langkah 3: Identifikasi Kesalahan
Contoh kesalahan umum:

**Salah:**
```
Terima pinjaman (SALAH):
  Debit:  Utang Jk Panjang  Rp 1.000.000  ← Terbalik!
  Kredit: Kas               Rp 1.000.000
```

**Benar:**
```
Terima pinjaman (BENAR):
  Debit:  Kas               Rp 1.000.000
  Kredit: Utang Jk Panjang  Rp 1.000.000
```

### Langkah 4: Koreksi
1. Hapus jurnal yang salah
2. Buat jurnal baru yang benar
3. Cek neraca lagi

---

## 📊 Persamaan Akuntansi

### Rumus Dasar
```
AKTIVA = PASIVA
AKTIVA = KEWAJIBAN + EKUITAS
```

### Dengan Laba/Rugi
```
AKTIVA = KEWAJIBAN + EKUITAS + (PENDAPATAN - BEBAN)
```

### Contoh Balance Sheet
```
AKTIVA                          PASIVA
─────────────────────────────   ─────────────────────────────
Aktiva Lancar                   Kewajiban
  Kas            Rp 10.000.000     Utang Jk Panjang  Rp 5.000.000
  Piutang        Rp  2.000.000   Total Kewajiban     Rp 5.000.000
Total            Rp 12.000.000   
                                Ekuitas
Aktiva Tetap                      Modal             Rp 10.000.000
  Kendaraan      Rp 10.000.000     Laba Ditahan      Rp  2.000.000
  Akm Penyusutan Rp -1.000.000   Total Ekuitas      Rp 12.000.000
Total            Rp  9.000.000   
                                
TOTAL AKTIVA     Rp 21.000.000   TOTAL PASIVA       Rp 21.000.000
                                                     ═════════════
                                                     ✅ BALANCE
```

---

## 🎓 Tips & Best Practices

### 1. Selalu Cek Balance
- Neraca harus selalu balance (Aktiva = Pasiva)
- Jika tidak balance, ada kesalahan jurnal

### 2. Pahami Saldo Normal
- Aset & Beban → Debit
- Kewajiban, Ekuitas, Pendapatan → Kredit

### 3. Hati-hati dengan Kontra Akun
- Akumulasi Penyusutan → Pengurang Aset
- Prive → Pengurang Ekuitas
- Retur Penjualan → Pengurang Pendapatan

### 4. Gunakan Indikator Sistem
- Perhatikan ⚠️ untuk saldo abnormal
- Baca peringatan di bawah neraca
- Segera perbaiki jurnal yang salah

### 5. Review Berkala
- Cek neraca setiap bulan
- Pastikan tidak ada saldo abnormal
- Koreksi kesalahan segera

---

## 📖 Referensi Cepat

### Debit vs Kredit

| Akun | Bertambah | Berkurang |
|------|-----------|-----------|
| Aset | Debit | Kredit |
| Kewajiban | Kredit | Debit |
| Ekuitas | Kredit | Debit |
| Pendapatan | Kredit | Debit |
| Beban | Debit | Kredit |

### Contoh Jurnal Umum

#### Terima Pinjaman Bank
```
Debit:  Kas                 Rp 10.000.000
Kredit: Utang Jk Panjang    Rp 10.000.000
```

#### Bayar Cicilan Utang
```
Debit:  Utang Jk Panjang    Rp 1.000.000
Kredit: Kas                 Rp 1.000.000
```

#### Beli Aset Kredit
```
Debit:  Kendaraan           Rp 20.000.000
Kredit: Utang Jk Panjang    Rp 20.000.000
```

#### Penyusutan Aset
```
Debit:  Beban Penyusutan      Rp 500.000
Kredit: Akumulasi Penyusutan  Rp 500.000
```

---

## 🆘 Troubleshooting

### Q: Mengapa utang saya negatif?
**A:** Kemungkinan jurnal terbalik. Cek apakah saat terima pinjaman, Utang di-debit (seharusnya kredit).

### Q: Kas saya negatif, apa artinya?
**A:** Kas keluar lebih banyak dari kas masuk. Bisa jadi:
- Ada transaksi yang tidak dicatat
- Ada jurnal yang salah
- Memang overdraft (perlu dicatat sebagai utang bank)

### Q: Akumulasi Penyusutan negatif, apakah salah?
**A:** Tidak salah! Akumulasi Penyusutan memang seharusnya negatif di neraca sebagai pengurang aset.

### Q: Bagaimana cara menghapus peringatan saldo abnormal?
**A:** Perbaiki jurnal yang salah. Peringatan akan hilang otomatis setelah saldo kembali normal.

---

**Dibuat:** 3 Mei 2026  
**Untuk:** Sistem Manajemen Aset & Akuntansi NUKUMA SOES
