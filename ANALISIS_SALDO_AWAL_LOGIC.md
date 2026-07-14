# Analisis Logika Saldo Awal - Trait SaldoAwalCalculator

## 📌 Pertanyaan Utama
Apakah saldo awal periode yang dipilih dihitung berdasarkan **saldo akhir terakhir sebelum periode tersebut** (rolling/beruntun), atau hanya mengecek apakah ada transaksi?

## ✅ Jawaban
Seharusnya **ROLLING/BERUNTUN**: Saldo Awal Juli = Saldo Akhir Juni, Saldo Awal Agustus = Saldo Akhir Juli, dst.

---

## 🔍 Analisis Alur Kode Saat Ini

### Method `hitungSaldoAwal($akunId, $periodeAwal, $periodeAkhir)`
```
1. Ambil tanggal periode sebelumnya (bulan -1)
2. Cek: Ada transaksi di periode sebelumnya?
   - YA → Panggil hitungSaldoAkhir(periode_sebelumnya)
   - TIDAK → Ambil dari tabel saldo_awals
```

### Method `hitungSaldoAkhir($akunId, $periodeAwal, $periodeAkhir)`
```
1. Hitung Saldo Awal = ambilSaldoAwalDariTabel($akunId)  ⚠️ SELALU dari tabel
2. Hitung Transaksi dalam periode = totalDebit - totalKredit
3. Saldo Akhir = Saldo Awal + Transaksi
```

---

## ⚠️ MASALAH DITEMUKAN

**Dalam method `hitungSaldoAkhir()`, saldo awal SELALU diambil dari tabel `saldo_awals`.**

Ini menyebabkan **ERROR untuk periode ketiga ke atas**!

### Contoh Skenario (Akun: Kas Kecil 111)

#### Setup Awal
- Setoran Modal Awal (Tanggal: 1 Mei 2026): **Rp 100.000.000**

#### Periode 1 - MEI 2026
- Saldo Awal Mei = Rp 100.000.000 (dari tabel saldo_awals)
- Transaksi Mei: +Rp 50.000.000 (debit), -Rp 20.000.000 (kredit)
- **Saldo Akhir Mei = Rp 100.000.000 + 50.000.000 - 20.000.000 = Rp 130.000.000** ✅

#### Periode 2 - JUNI 2026
- **Saldo Awal Juni** dihitung melalui:
  - `hitungSaldoAwal('111', '2026-06-01', '2026-06-30')`
  - Ada transaksi di Mei? → YA
  - Panggil: `hitungSaldoAkhir('111', '2026-05-01', '2026-05-31')`
    - Saldo Awal Mei = ambilSaldoAwalDariTabel() = Rp 100.000.000 ✓
    - Transaksi Mei = +50jt - 20jt = +30jt
    - Saldo Akhir Mei = Rp 130.000.000 ✓
  - Saldo Awal Juni = Rp 130.000.000 ✓

- Transaksi Juni: +Rp 40.000.000 (debit), -Rp 10.000.000 (kredit)
- **Saldo Akhir Juni = Rp 130.000.000 + 40.000.000 - 10.000.000 = Rp 160.000.000** ✅

#### Periode 3 - JULI 2026 ⚠️ KESALAHAN MULAI DI SINI
- **Saldo Awal Juli** dihitung melalui:
  - `hitungSaldoAwal('111', '2026-07-01', '2026-07-31')`
  - Ada transaksi di Juni? → YA
  - Panggil: `hitungSaldoAkhir('111', '2026-06-01', '2026-06-30')`
    - **Saldo Awal Juni = ambilSaldoAwalDariTabel() = Rp 100.000.000** ❌ SALAH!
    - (Seharusnya Rp 130.000.000 dari saldo akhir Mei)
    - Transaksi Juni = +40jt - 10jt = +30jt
    - Saldo Akhir Juni = Rp 100.000.000 + 30.000.000 = Rp 130.000.000 ❌ SALAH!
    - (Seharusnya Rp 160.000.000)
  - **Saldo Awal Juli = Rp 130.000.000** ❌ SALAH!
  - (Seharusnya Rp 160.000.000)

- Transaksi Juli: +Rp 30.000.000 (debit), -Rp 5.000.000 (kredit)
- **Saldo Akhir Juli = Rp 130.000.000 + 30.000.000 - 5.000.000 = Rp 155.000.000** ❌ SALAH!
- (Seharusnya Rp 160.000.000 + 30.000.000 - 5.000.000 = Rp 185.000.000)

#### Periode 4 - AGUSTUS 2026 (Error Berlanjut & Membesar)
- Saldo Awal Agustus = hitungSaldoAkhir(Juli) = Rp 155.000.000 ❌ SALAH!
- Dan error terus berlanjut...

---

## 🔧 SOLUSI

Method `hitungSaldoAkhir()` perlu diperbaiki agar **recursively menghitung saldo awal periode tersebut**, bukan selalu ambil dari tabel:

```php
public function hitungSaldoAkhir($akunId, $periodeAwal, $periodeAkhir)
{
    // 1. Hitung saldo awal periode ini menggunakan logika yang sama
    $saldoAwal = $this->hitungSaldoAwal($akunId, $periodeAwal, $periodeAkhir);
    
    // 2. Hitung transaksi dalam periode
    $totalDebit  = [hitung transaksi...];
    $totalKredit = [hitung transaksi...];
    
    // 3. Saldo akhir
    $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;
    return $saldoAkhir;
}
```

Dengan pendekatan ini, saldo akan **beruntun otomatis** tanpa perlu tracking manual.

---

## 📊 Verifikasi dengan Solusi

#### Dengan Logika yang Diperbaiki:

**Periode 1 - MEI 2026**
- Saldo Awal Mai = hitungSaldoAwal(Mei) 
  - Cek transaksi Mei sebelumnya (April)? → TIDAK
  - Ambil dari tabel = Rp 100.000.000 ✓
- Transaksi Mei = +50jt - 20jt
- Saldo Akhir Mei = Rp 130.000.000 ✓

**Periode 2 - JUNI 2026**
- Saldo Awal Juni = hitungSaldoAwal(Juni)
  - Cek transaksi Mei? → YA
  - hitungSaldoAkhir(Mei) = hitungSaldoAwal(Mei) + transaksi Mei
    - = Rp 100.000.000 + 30jt = Rp 130.000.000 ✓
- Transaksi Juni = +40jt - 10jt
- Saldo Akhir Juni = Rp 130.000.000 + 30jt = Rp 160.000.000 ✓

**Periode 3 - JULI 2026**
- Saldo Awal Juli = hitungSaldoAwal(Juli)
  - Cek transaksi Juni? → YA
  - hitungSaldoAkhir(Juni) = hitungSaldoAwal(Juni) + transaksi Juni
    - hitungSaldoAwal(Juni) → Rp 130.000.000
    - transaksi Juni = +30jt
    - = Rp 130.000.000 + 30jt = Rp 160.000.000 ✓
- Transaksi Juli = +30jt - 5jt
- Saldo Akhir Juli = Rp 160.000.000 + 25jt = Rp 185.000.000 ✓

**Periode 4 - AGUSTUS 2026**
- Saldo Awal Agustus = hitungSaldoAwal(Agustus)
  - Cek transaksi Juli? → YA
  - hitungSaldoAkhir(Juli) = hitungSaldoAwal(Juli) + transaksi Juli
    - hitungSaldoAwal(Juli) → Rp 160.000.000
    - transaksi Juli = +25jt
    - = Rp 160.000.000 + 25jt = Rp 185.000.000 ✓
- Transaksi Agustus = +20jt - 8jt
- Saldo Akhir Agustus = Rp 185.000.000 + 12jt = Rp 197.000.000 ✓

**SEMPURNA! ✅**

---

## 📋 Tabel Ringkasan (Sebelum vs Sesudah Perbaikan)

| Periode | Saldo Awal (Saat Ini) | Saldo Akhir (Saat Ini) | Saldo Awal (Seharusnya) | Saldo Akhir (Seharusnya) | Status |
|---------|----------------------|----------------------|------------------------|-------------------------|--------|
| Mei     | 100.000.000         | 130.000.000         | 100.000.000            | 130.000.000             | ✅     |
| Juni    | 130.000.000         | 160.000.000         | 130.000.000            | 160.000.000             | ✅     |
| Juli    | **130.000.000** ❌  | **155.000.000** ❌  | 160.000.000            | 185.000.000             | ❌     |
| Agustus | **155.000.000** ❌  | **175.000.000** ❌  | 185.000.000            | 197.000.000             | ❌     |

---

## 🎯 Kesimpulan

**Logika saat ini memiliki BUG untuk periode ketiga ke atas.**

Solusi: Ubah method `hitungSaldoAkhir()` agar memanggil `hitungSaldoAwal()` untuk menghitung saldo awal periode tersebut, sehingga terbentuk logika **recursive yang beruntun**.

Dengan cara ini:
- ✅ Saldo otomatis beruntun dari periode ke periode
- ✅ Tidak perlu input manual setiap bulan
- ✅ Siap untuk integrasi modul lain
- ✅ Akurat untuk periode tak terbatas
