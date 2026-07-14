# 🎯 PANDUAN TESTING CARRY FORWARD BALANCE

## ✅ Status Implementasi
- [x] Logika recursive sudah diperbaiki
- [x] Base case sudah terpasang (aman dari infinite recursion)
- [x] Syntax check: ✓ No errors
- [x] Test file sudah dibuat
- [x] Dokumentasi lengkap
- ⏳ **Menunggu: Manual testing dari user**

---

## 🧪 CARA TESTING (PILIH SALAH SATU)

### **OPSI 1: Testing Otomatis (RECOMMENDED) ⭐**

Paling cepat dan akurat. Jalankan di terminal:

```bash
php artisan test tests/Feature/SaldoAwalCarryForwardTest.php
```

**Output yang diharapkan:**
```
PASS  Tests\Feature\SaldoAwalCarryForwardTest
  ✓ carry forward balance mei juni juli agustus
  ✓ periode pertama tanpa data saldo awal
  ✓ periode tanpa transaksi ambil dari tabel

Tests:  3 passed (8 assertions)
```

**Jika PASS ✅:** Logika sudah benar, go to production!  
**Jika FAIL ❌:** Check log & beri tahu error message

---

### **OPSI 2: Testing Manual via Tinker**

Untuk verifikasi manual step-by-step. Jalankan:

```bash
php artisan tinker
```

Kemudian copy-paste kode berikut:

```php
$c = new \App\Http\Controllers\BukuBesarController();

// TEST 1: Periode Mei (BASE CASE)
echo "=== PERIODE MEI (BASE CASE) ===\n";
$sa_mei = $c->hitungSaldoAwal('111', '2026-05-01', '2026-05-31');
$sa_akhir_mei = $c->hitungSaldoAkhir('111', '2026-05-01', '2026-05-31');
echo "Saldo Awal Mei: " . number_format($sa_mei) . "\n";
echo "Saldo Akhir Mei: " . number_format($sa_akhir_mei) . "\n";
echo "Expected: SA=100,000,000 / SA Akhir=130,000,000\n";
echo ($sa_mei === 100000000 && $sa_akhir_mei === 130000000 ? "✓ PASS\n" : "✗ FAIL\n");
echo "\n";

// TEST 2: Periode Juni (RECURSIVE - Carry Forward)
echo "=== PERIODE JUNI (CARRY FORWARD) ===\n";
$sa_juni = $c->hitungSaldoAwal('111', '2026-06-01', '2026-06-30');
$sa_akhir_juni = $c->hitungSaldoAkhir('111', '2026-06-01', '2026-06-30');
echo "Saldo Awal Juni: " . number_format($sa_juni) . "\n";
echo "Saldo Akhir Juni: " . number_format($sa_akhir_juni) . "\n";
echo "Expected: SA=130,000,000 / SA Akhir=160,000,000\n";
echo ($sa_juni === 130000000 && $sa_akhir_juni === 160000000 ? "✓ PASS\n" : "✗ FAIL\n");
echo "\n";

// TEST 3: Carry Forward Verification
echo "=== VERIFIKASI CARRY FORWARD ===\n";
echo "Saldo Akhir Mei: " . number_format($sa_akhir_mei) . "\n";
echo "Saldo Awal Juni: " . number_format($sa_juni) . "\n";
echo ($sa_akhir_mei === $sa_juni ? "✓ CARRY FORWARD CORRECT\n" : "✗ CARRY FORWARD WRONG\n");
echo "\n";

// TEST 4: Periode Juli
echo "=== PERIODE JULI (RECURSIVE #2) ===\n";
$sa_juli = $c->hitungSaldoAwal('111', '2026-07-01', '2026-07-31');
$sa_akhir_juli = $c->hitungSaldoAkhir('111', '2026-07-01', '2026-07-31');
echo "Saldo Awal Juli: " . number_format($sa_juli) . "\n";
echo "Saldo Akhir Juli: " . number_format($sa_akhir_juli) . "\n";
echo "Expected: SA=160,000,000 / SA Akhir=185,000,000\n";
echo ($sa_juli === 160000000 && $sa_akhir_juli === 185000000 ? "✓ PASS\n" : "✗ FAIL\n");
echo "\n";

// TEST 5: Agustus
echo "=== PERIODE AGUSTUS (RECURSIVE #3) ===\n";
$sa_agustus = $c->hitungSaldoAwal('111', '2026-08-01', '2026-08-31');
$sa_akhir_agustus = $c->hitungSaldoAkhir('111', '2026-08-01', '2026-08-31');
echo "Saldo Awal Agustus: " . number_format($sa_agustus) . "\n";
echo "Saldo Akhir Agustus: " . number_format($sa_akhir_agustus) . "\n";
echo "Expected: SA=185,000,000 / SA Akhir=197,000,000\n";
echo ($sa_agustus === 185000000 && $sa_akhir_agustus === 197000000 ? "✓ PASS\n" : "✗ FAIL\n");
echo "\n";

// FINAL VERIFICATION
echo "=== FINAL CARRY FORWARD CHECK ===\n";
echo "Mei: SA=" . number_format($sa_mei) . " / SA Akhir=" . number_format($sa_akhir_mei) . "\n";
echo "Juni: SA=" . number_format($sa_juni) . " / SA Akhir=" . number_format($sa_akhir_juni) . "\n";
echo "Juli: SA=" . number_format($sa_juli) . " / SA Akhir=" . number_format($sa_akhir_juli) . "\n";
echo "Agustus: SA=" . number_format($sa_agustus) . " / SA Akhir=" . number_format($sa_akhir_agustus) . "\n";

$allCorrect = (
    $sa_akhir_mei === $sa_juni &&
    $sa_akhir_juni === $sa_juli &&
    $sa_akhir_juli === $sa_agustus
);

echo "\n" . ($allCorrect ? "✅ ALL CARRY FORWARD CORRECT!" : "❌ CARRY FORWARD ERROR") . "\n";
```

**Expected Output:**
```
=== PERIODE MEI (BASE CASE) ===
Saldo Awal Mei: 100,000,000
Saldo Akhir Mei: 130,000,000
Expected: SA=100,000,000 / SA Akhir=130,000,000
✓ PASS

=== PERIODE JUNI (CARRY FORWARD) ===
Saldo Awal Juni: 130,000,000
Saldo Akhir Juni: 160,000,000
Expected: SA=130,000,000 / SA Akhir=160,000,000
✓ PASS

=== VERIFIKASI CARRY FORWARD ===
Saldo Akhir Mei: 130,000,000
Saldo Awal Juni: 130,000,000
✓ CARRY FORWARD CORRECT

=== PERIODE JULI (RECURSIVE #2) ===
Saldo Awal Juli: 160,000,000
Saldo Akhir Juli: 185,000,000
Expected: SA=160,000,000 / SA Akhir=185,000,000
✓ PASS

=== PERIODE AGUSTUS (RECURSIVE #3) ===
Saldo Awal Agustus: 185,000,000
Saldo Akhir Agustus: 197,000,000
Expected: SA=185,000,000 / SA Akhir=197,000,000
✓ PASS

=== FINAL CARRY FORWARD CHECK ===
Mei: SA=100,000,000 / SA Akhir=130,000,000
Juni: SA=130,000,000 / SA Akhir=160,000,000
Juli: SA=160,000,000 / SA Akhir=185,000,000
Agustus: SA=185,000,000 / SA Akhir=197,000,000

✅ ALL CARRY FORWARD CORRECT!
```

**Type `exit` atau `quit` untuk keluar Tinker**

---

### **OPSI 3: Testing via Web Browser**

Untuk verifikasi visual di Buku Besar:

1. **Buka aplikasi**: `http://localhost/nukumasoesss/`
2. **Menu**: Buku Besar
3. **Pilih Akun**: 111 - Kas Kecil
4. **Pilih Periode**: Mei 2026

**Verifikasi:**
- ✓ Saldo Awal Mei = Rp 100.000.000 (dari tabel)
- ✓ Lihat transaksi dan saldo berjalan
- ✓ Saldo Akhir Mei = Rp 130.000.000

**Kemudian pilih Periode: Juni 2026**
- ✓ Saldo Awal Juni = Rp 130.000.000 (dari Saldo Akhir Mei)
- ✓ Lihat transaksi dan saldo berjalan
- ✓ Saldo Akhir Juni = Rp 160.000.000

**Kemudian pilih Periode: Juli 2026**
- ✓ Saldo Awal Juli = Rp 160.000.000 (dari Saldo Akhir Juni)
- ✓ Saldo Akhir Juli = Rp 185.000.000

**Kemudian pilih Periode: Agustus 2026**
- ✓ Saldo Awal Agustus = Rp 185.000.000 (dari Saldo Akhir Juli)
- ✓ Saldo Akhir Agustus = Rp 197.000.000

---

## 📊 Tabel Verifikasi Expected Results

| Periode | Saldo Awal | Transaksi | Saldo Akhir | Verifikasi |
|---------|-----------|-----------|-----------|-----------|
| **MEI** | 100.000.000 | +50jt-20jt = +30jt | 130.000.000 | ✓ BASE CASE |
| **JUNI** | 130.000.000 | +40jt-10jt = +30jt | 160.000.000 | ✓ = SA Akhir Mei |
| **JULI** | 160.000.000 | +30jt-5jt = +25jt | 185.000.000 | ✓ = SA Akhir Juni |
| **AGUSTUS** | 185.000.000 | +20jt-8jt = +12jt | 197.000.000 | ✓ = SA Akhir Juli |

---

## ✅ SUCCESS CRITERIA

✓ Semua test PASS (3/3)  
✓ Saldo Awal setiap periode = Saldo Akhir periode sebelumnya  
✓ Tidak ada error di log  
✓ Saldo berjalan konsisten (SALDO AWAL → Transaksi → SALDO AKHIR)

---

## ❌ TROUBLESHOOTING

### Error: "FAIL pada test tertentu"
**Kemungkinan penyebab:**
1. Data Pembelian/Overhead belum ada di periode tersebut
2. Tanggal transaksi tidak sesuai dengan periode
3. Nilai debit/kredit salah di database

**Solusi:**
- Verifikasi data di tabel `pembelians` dan `overheads`
- Check kolom `tanggal` harus dalam range periode
- Check nilai `subtotal`, `diskon`, `ongkir`, `grand_total`

### Error: "coa_id tidak match"
**Penyebab:** Akun 111 tidak ada atau COA tidak ter-setup

**Solusi:**
- Pastikan COA 111 (Kas Kecil) sudah dibuat
- Pastikan SaldoAwal untuk akun 111 sudah input

### Error: "Infinite recursion / Stack overflow"
**Tidak akan terjadi karena:**
- ✅ Base case sudah terpasang (BASE CASE = Tidak ada transaksi sebelumnya)
- ✅ Setiap recursive call bergerak ke periode sebelumnya
- ✅ Akhirnya sampai periode pertama dan STOP

---

## 📝 COMMAND QUICK REFERENCE

```bash
# Test otomatis
php artisan test tests/Feature/SaldoAwalCarryForwardTest.php

# Test dengan verbose
php artisan test tests/Feature/SaldoAwalCarryForwardTest.php --verbose

# Tinker interactive
php artisan tinker

# Check error di file
php artisan code:analyze  # (if using PHPStan)

# View log
tail -f storage/logs/laravel.log
```

---

## 📞 NEXT STEPS

**Setelah Testing Berhasil:**
1. ✅ Logika sudah PRODUCTION READY
2. ✅ Deploy ke production environment
3. ✅ Siap untuk integrasi Modul Penjualan/Produksi
4. ✅ Dokumentasi sudah lengkap

**Jika Ada Issue:**
1. ✅ Beri tahu error message & output
2. ✅ Kami akan debug & fix
3. ✅ Re-test sampai PASS

---

## 📄 DOKUMENTASI REFERENSI

- `RINGKASAN_CARRY_FORWARD_IMPLEMENTATION.md` - Ringkasan lengkap
- `TESTING_CARRY_FORWARD_BALANCE.md` - Panduan testing detail
- `ANALISIS_SALDO_AWAL_LOGIC.md` - Analisis teknis
- `tests/Feature/SaldoAwalCarryForwardTest.php` - Test file

---

**🚀 Ready to test? Let's go!**

Choose your testing option above and let me know the results! 🎯
