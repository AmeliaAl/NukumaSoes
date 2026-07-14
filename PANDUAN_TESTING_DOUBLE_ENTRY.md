# 🧪 Panduan Testing - Double Entry Accounting Saldo Awal

## 📋 Pre-Testing Setup

Pastikan data sudah ada:
- ✓ COA 111 (Kas Kecil) sudah dibuat
- ✓ COA 112 (Bank BCA) sudah dibuat  
- ✓ COA 311 (Modal Pemilik) sudah ada (seeded)
- ✓ Saldo Awal sudah diinput untuk akun 111 dan 112 pada periode Mei 2026

---

## 🎯 Test Case 1: Buku Besar Akun Kas Kecil (111)

### Setup
- Akun: 111 - Kas Kecil
- Saldo Awal: Rp 100.000.000
- Tanggal: 01-05-2026

### Langkah Testing
1. Buka aplikasi → Menu **Buku Besar**
2. Pilih **Akun**: 111 - Kas Kecil
3. Pilih **Periode**: Mei 2026 (01-05-2026 s/d 31-05-2026)
4. Klik **Filter Data**

### Verifikasi ✓

**Expected Output:**

#### Baris Pertama (Jurnal Pembukaan)
| Tanggal | Keterangan | Debit | Kredit | Saldo (Debit) |
|---------|-----------|-------|--------|---|
| 01-05-2026 | Jurnal Pembukaan - Kas Kecil | **Rp 100.000.000** | - | **Rp 100.000.000** |

#### Saldo Awal
- ✓ SALDO AWAL = Rp 100.000.000 (Debit)
- ✓ Baris pertama transaksi = Jurnal pembukaan dengan Rp 100.000.000 (Debit)
- ✓ Saldo berjalan = Rp 100.000.000 (mulai dari jurnal pembukaan)

#### Screenshot Check
- ✓ Terdapat baris "SALDO AWAL"
- ✓ Diikuti baris transaksi dengan keterangan "Jurnal Pembukaan - Kas Kecil"
- ✓ Nilai debit = Rp 100.000.000
- ✓ Kolom saldo menunjukkan Rp 100.000.000 di sebelah Debit

---

## 🎯 Test Case 2: Buku Besar Akun Modal Pemilik (311)

### Setup
- Akun: 311 - Modal Pemilik
- Periode: Mei 2026 (periode yang sama dengan input Saldo Awal)

### Langkah Testing
1. Buka aplikasi → Menu **Buku Besar**
2. Pilih **Akun**: 311 - Modal Pemilik
3. Pilih **Periode**: Mei 2026 (01-05-2026 s/d 31-05-2026)
4. Klik **Filter Data**

### Verifikasi ✓

**Expected Output:**

#### Baris Pertama (Jurnal Pembukaan)
| Tanggal | Keterangan | Debit | Kredit | Saldo (Kredit) |
|---------|-----------|-------|--------|---|
| 01-05-2026 | Jurnal Pembukaan - Dari Kas Kecil | - | **Rp 100.000.000** | **Rp 100.000.000** |

Jika ada 2 Saldo Awal (Kas Kecil + Bank BCA):
| Tanggal | Keterangan | Debit | Kredit | Saldo (Kredit) |
|---------|-----------|-------|--------|---|
| 01-05-2026 | Jurnal Pembukaan - Dari Kas Kecil | - | Rp 100.000.000 | Rp 100.000.000 |
| 01-05-2026 | Jurnal Pembukaan - Dari Bank BCA | - | Rp 50.000.000 | **Rp 150.000.000** |

#### Saldo Awal
- ✓ SALDO AWAL = Rp 100.000.000 (Kredit) [atau Rp 150.000.000 jika ada 2 akun]
- ✓ Baris pertama transaksi = Jurnal pembukaan dengan nilai Kredit
- ✓ Saldo berjalan mulai dari jurnal pembukaan

#### Screenshot Check
- ✓ Terdapat baris "SALDO AWAL"
- ✓ Diikuti baris transaksi "Jurnal Pembukaan - Dari Kas Kecil" (dan akun lain jika ada)
- ✓ Nilai kredit = Rp 100.000.000 (per akun)
- ✓ Kolom saldo menunjukkan nilai di sebelah Kredit

---

## 🎯 Test Case 3: Jurnal Umum

### Setup
- Saldo Awal (111): Rp 100.000.000
- Saldo Awal (112): Rp 50.000.000 (opsional)
- Periode: Mei 2026

### Langkah Testing
1. Buka aplikasi → Menu **Jurnal Umum**
2. Pilih **Periode**: Mei 2026 (01-05-2026 s/d 31-05-2026)
3. Klik **Filter Data**

### Verifikasi ✓

**Expected Output:**

#### Jurnal Pembukaan (Jika hanya Kas Kecil)
| Tanggal | No Bukti | Keterangan | Akun | Debit | Kredit |
|---------|----------|-----------|------|-------|--------|
| 01-05-2026 | SA-001 | Jurnal Pembukaan - Kas Kecil | 111 Kas Kecil | Rp 100.000.000 | - |
| 01-05-2026 | SA-001 | Modal Pemilik | 311 Modal Pemilik | - | Rp 100.000.000 |

#### Jurnal Pembukaan (Jika Kas Kecil + Bank BCA)
| Tanggal | No Bukti | Keterangan | Akun | Debit | Kredit |
|---------|----------|-----------|------|-------|--------|
| 01-05-2026 | SA-001 | Jurnal Pembukaan - Kas Kecil | 111 Kas Kecil | Rp 100.000.000 | - |
| 01-05-2026 | SA-001 | Modal Pemilik | 311 Modal Pemilik | - | Rp 100.000.000 |
| 01-05-2026 | SA-002 | Jurnal Pembukaan - Bank BCA | 112 Bank BCA | Rp 50.000.000 | - |
| 01-05-2026 | SA-002 | Modal Pemilik | 311 Modal Pemilik | - | Rp 50.000.000 |

#### Balance Check
- ✓ Total Debit = Total Kredit
  - Jika 1 akun: Debit = Kredit = Rp 100.000.000
  - Jika 2 akun: Debit = Kredit = Rp 150.000.000
- ✓ Setiap jurnal pembukaan memiliki no_bukti (SA-001, SA-002, dst)
- ✓ Tanggal semua jurnal pembukaan = tanggal input Saldo Awal

---

## 🎯 Test Case 4: Carry Forward Balance (Periode Berikutnya)

### Setup
- Periode Mei 2026:
  - Saldo Awal (111): Rp 100.000.000
  - Transaksi Mei: +Rp 50.000.000 (Debit)
  - Saldo Akhir Mei: Rp 150.000.000

- Periode Juni 2026:
  - Saldo Awal harus otomatis = Rp 150.000.000

### Langkah Testing

**Bagian A: Verifikasi Saldo Akhir Mei**
1. Buka **Buku Besar** → Akun 111 → Periode Mei
2. Lihat baris terakhir: SALDO AKHIR = Rp 150.000.000

**Bagian B: Verifikasi Saldo Awal Juni = Saldo Akhir Mei**
1. Buka **Buku Besar** → Akun 111 → Periode Juni
2. Lihat baris SALDO AWAL = Rp 150.000.000
3. **PENTING:** Baris pertama transaksi TIDAK boleh menampilkan jurnal pembukaan lagi

### Verifikasi ✓

#### Buku Besar Periode Juni
| Tanggal | Keterangan | Debit | Kredit | Saldo (Debit) |
|---------|-----------|-------|--------|---|
| (SALDO AWAL) | - | - | - | **Rp 150.000.000** |
| 15-06-2026 | [Transaksi Juni] | ... | ... | ... |

#### Key Points
- ✓ Tidak ada jurnal pembukaan di Periode Juni (baris pertama transaksi adalah transaksi biasa)
- ✓ SALDO AWAL Juni = SALDO AKHIR Mei = Rp 150.000.000
- ✓ Akun 311 di Periode Juni juga tidak ada transaksi tambahan
- ✓ Carry forward berjalan dengan sempurna

---

## ✅ Acceptance Criteria

### ✓ Test Case 1: Buku Besar 111 PASS Jika:
- [ ] Baris pertama menampilkan "Jurnal Pembukaan - Kas Kecil"
- [ ] Nilai Debit = Rp 100.000.000 (sesuai input)
- [ ] Saldo awal = Rp 100.000.000 (Debit)
- [ ] Tidak ada error atau tampilan aneh

### ✓ Test Case 2: Buku Besar 311 PASS Jika:
- [ ] Baris pertama menampilkan "Jurnal Pembukaan - Dari Kas Kecil"
- [ ] Nilai Kredit = Rp 100.000.000 (sesuai input)
- [ ] Saldo awal = Rp 100.000.000 (Kredit)
- [ ] Jika ada 2+ akun input, semua ditampilkan
- [ ] Total saldo = sum dari semua Saldo Awal

### ✓ Test Case 3: Jurnal Umum PASS Jika:
- [ ] Menampilkan 2 baris per Saldo Awal (Debit & Kredit)
- [ ] Total Debit = Total Kredit
- [ ] No Bukti terurut (SA-001, SA-002, dst)
- [ ] Keterangan jelas ("Jurnal Pembukaan - ...")

### ✓ Test Case 4: Carry Forward PASS Jika:
- [ ] Saldo Awal Juni = Saldo Akhir Mei
- [ ] Tidak ada jurnal pembukaan tambahan di Juni
- [ ] Buku Besar konsisten antar periode

---

## 🐛 Troubleshooting

### Problem: Jurnal Pembukaan Tidak Tampil di Buku Besar Akun 111
**Penyebab:**
- SaldoAwal belum diinput
- Akun 111 COA belum direlasikan dengan SaldoAwal

**Solusi:**
1. Verifikasi SaldoAwal sudah ada di tabel
2. Pastikan `coa_id` di SaldoAwal = id COA 111
3. Pastikan tanggal SaldoAwal dalam range periode yang dipilih

### Problem: Buku Besar Akun 311 Kosong
**Penyebab:**
- COA 311 tidak ada
- SaldoAwal tidak diinput

**Solusi:**
1. Verifikasi COA 311 sudah ada (check `coas` table)
2. Pastikan seeding untuk COA 311 sudah berjalan: `php artisan migrate:refresh` (development only!)
3. Verifikasi SaldoAwal ada di tabel

### Problem: Total Debit ≠ Total Kredit di Jurnal Umum
**Penyebab:**
- Ada transaksi lain yang belum difilter dengan benar
- SaldoAwal tidak lengkap (hanya satu sisi)

**Solusi:**
1. Pastikan filter periode benar
2. Check `jurnals.php` atau file testing untuk verifikasi manual
3. Lihat log: `storage/logs/laravel.log`

### Problem: Jurnal Pembukaan Tampil di Periode Juni (Seharusnya Tidak)
**Penyebab:**
- SaldoAwal juga diinput di Juni (tidak sesuai requirement)
- Filter periode tidak bekerja dengan benar

**Solusi:**
1. SaldoAwal hanya boleh diinput di periode pertama
2. Jika sudah input di Juni, delete dan ulang
3. Check tanggal SaldoAwal di database

---

## 📝 Test Report Template

Gunakan template ini untuk dokumentasi hasil testing:

```
TEST REPORT - DOUBLE ENTRY ACCOUNTING SALDO AWAL
Tanggal: [TGL]
Tester: [NAMA]
Status: [PASS/FAIL]

Test Case 1 - Buku Besar 111:
  Status: [PASS/FAIL]
  Detail: [Deskripsi hasil]

Test Case 2 - Buku Besar 311:
  Status: [PASS/FAIL]
  Detail: [Deskripsi hasil]

Test Case 3 - Jurnal Umum:
  Status: [PASS/FAIL]
  Detail: [Deskripsi hasil]

Test Case 4 - Carry Forward:
  Status: [PASS/FAIL]
  Detail: [Deskripsi hasil]

Overall: [PASS/FAIL]
Note: [Catatan tambahan]
```

---

## 🚀 Next Steps

### Jika Semua Test PASS ✅
1. ✅ Double entry accounting sudah working dengan benar
2. ✅ Siap untuk production
3. ✅ Dokumentasi selesai
4. ✅ Siap integrasi modul lain

### Jika Ada Test FAIL ❌
1. ❌ Beri tahu error message & expected vs actual output
2. ❌ Provide screenshot untuk reference
3. ❌ Kami akan debug & fix
4. ❌ Re-test sampai PASS

---

**🎯 Testing Start Point: Buka Buku Besar Akun 111 Periode Mei**

Good luck! Let me know the results! 💪
