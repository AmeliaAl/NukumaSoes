# Double Entry Accounting Implementation - Saldo Awal

## 📋 Ringkasan Implementasi

Sistem sekarang mengimplementasikan **double entry accounting (pembukuan berpasangan)** untuk Setoran Modal Awal. Ketika user menyimpan saldo awal untuk sebuah akun kas/bank, sistem otomatis membuat jurnal pembukaan dengan pasangan akun Modal Pemilik.

---

## 🔄 Alur Double Entry Accounting

### Input Setoran Modal Awal

**Tanggal:** 01-05-2026  
**Akun:** 111 - Kas Kecil  
**Nominal:** Rp 100.000.000

### Jurnal yang Terbentuk Secara Otomatis

```
No Bukti: SA-001
Tanggal: 01-05-2026

Akun 111 - Kas Kecil
  Debit: Rp 100.000.000

Akun 311 - Modal Pemilik
  Kredit: Rp 100.000.000
```

### Tampilan di Buku Besar

#### **Buku Besar Akun 111 - Kas Kecil**
```
Tanggal       Keterangan                    Debit          Kredit         Saldo (Debit)
────────────────────────────────────────────────────────────────────────────────────
01-05-2026    Jurnal Pembukaan - Kas Kecil Rp 100.000.000 -              Rp 100.000.000
```

#### **Buku Besar Akun 311 - Modal Pemilik**
```
Tanggal       Keterangan                         Debit   Kredit            Saldo (Kredit)
─────────────────────────────────────────────────────────────────────────────────────
01-05-2026    Jurnal Pembukaan - Dari Kas Kecil -       Rp 100.000.000   Rp 100.000.000
```

---

## ⚙️ Cara Kerjanya di Backend

### 1. **File: `app/Controllers/BukuBesarController.php`**

**Section Baru: JURNAL PEMBUKAAN (SALDO AWAL DENGAN DOUBLE ENTRY)**

```php
// Ambil semua SaldoAwal pada periode yang dipilih
$saldoAwalQuery = SaldoAwal::with('coa');

if ($periodeAwal && $periodeAkhir) {
    $saldoAwalQuery->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);
}

foreach ($saldoAwalQuery->get() as $saldoAwal) {
    $saldoAwalAkunKode = $saldoAwal->coa->kode_akun ?? '';

    // Jika akun yang dipilih = akun kas/bank (dari SaldoAwal)
    if ((string) $akunId === (string) $saldoAwalAkunKode) {
        // Tampilkan sebagai DEBIT
        $jurnals[] = [
            'tanggal'    => $saldoAwal->tanggal,
            'bukti'      => $saldoAwal->no_bukti,
            'keterangan' => 'Jurnal Pembukaan - ' . ($saldoAwal->coa->nama_akun ?? ''),
            'ref'        => $saldoAwalAkunKode,
            'debit'      => (float) $saldoAwal->nominal,  // ← DEBIT
            'kredit'     => 0,
        ];
    }

    // Jika akun yang dipilih = Modal Pemilik (311)
    if ($coa311 && (string) $akunId === (string) $coa311->kode_akun) {
        // Tampilkan sebagai KREDIT
        $jurnals[] = [
            'tanggal'    => $saldoAwal->tanggal,
            'bukti'      => $saldoAwal->no_bukti,
            'keterangan' => 'Jurnal Pembukaan - Dari ' . ($saldoAwal->coa->nama_akun ?? ''),
            'ref'        => $coa311->kode_akun,
            'debit'      => 0,
            'kredit'     => (float) $saldoAwal->nominal,  // ← KREDIT
        ];
    }
}
```

**Logika:**
1. ✅ Loop semua SaldoAwal dalam periode yang dipilih
2. ✅ Jika akun yang dipilih = akun kas/bank → tampilkan sebagai DEBIT
3. ✅ Jika akun yang dipilih = Modal Pemilik → tampilkan sebagai KREDIT
4. ✅ Jurnal pembukaan akan otomatis diurutkan berdasarkan tanggal dan no_bukti

### 2. **File: `app/Http/Controllers/JurnalUmumController.php`**

**Sudah ada section untuk JURNAL SALDO AWAL** (tidak perlu diubah):

```php
// ─── JURNAL SALDO AWAL ────────────────────────────────────────────────

foreach ($saldoQuery->get() as $saldoAwal) {
    // Debit: akun kas/bank yang dipilih
    $jurnals[] = [
        'tanggal'    => $saldoAwal->tanggal,
        'no_bukti'   => $saldoAwal->no_bukti,
        'keterangan' => $saldoAwal->keterangan ?: ('Saldo Awal ' . ($saldoAwal->coa->nama_akun ?? '')),
        'kode_akun'  => $saldoAwal->coa->kode_akun ?? '',
        'debit'      => $saldoAwal->nominal,
        'kredit'     => 0,
    ];

    // Kredit: Modal Pemilik (311)
    $jurnals[] = [
        'tanggal'    => $saldoAwal->tanggal,
        'no_bukti'   => $saldoAwal->no_bukti,
        'keterangan' => 'Modal Pemilik',
        'kode_akun'  => $coa311->kode_akun ?? '311',
        'debit'      => 0,
        'kredit'     => $saldoAwal->nominal,
    ];
}
```

---

## 📊 Contoh Skenario Lengkap

### Setup Awal
- Saldo Awal Kas Kecil (111): Rp 100.000.000
- Saldo Awal Bank BCA (112): Rp 50.000.000

### Jurnal Pembukaan Otomatis
```
SA-001  01-05-2026  111 Kas Kecil (Debit)       Rp 100.000.000
SA-001  01-05-2026  311 Modal Pemilik (Kredit)  Rp 100.000.000

SA-002  01-05-2026  112 Bank BCA (Debit)        Rp 50.000.000
SA-002  01-05-2026  311 Modal Pemilik (Kredit)  Rp 50.000.000
```

### Tampilan Jurnal Umum
| Tanggal | No Bukti | Keterangan | Akun | Debit | Kredit |
|---------|----------|-----------|------|-------|--------|
| 01-05-2026 | SA-001 | Jurnal Pembukaan - Kas Kecil | 111 Kas Kecil | 100.000.000 | - |
| 01-05-2026 | SA-001 | Modal Pemilik | 311 Modal Pemilik | - | 100.000.000 |
| 01-05-2026 | SA-002 | Jurnal Pembukaan - Bank BCA | 112 Bank BCA | 50.000.000 | - |
| 01-05-2026 | SA-002 | Modal Pemilik | 311 Modal Pemilik | - | 50.000.000 |

### Tampilan Buku Besar Akun 111 - Kas Kecil
| Tanggal | Keterangan | Debit | Kredit | Saldo Debit |
|---------|-----------|-------|--------|------------|
| 01-05-2026 | Jurnal Pembukaan - Kas Kecil | 100.000.000 | - | 100.000.000 |
| (transaksi berikutnya) | ... | ... | ... | ... |

### Tampilan Buku Besar Akun 311 - Modal Pemilik
| Tanggal | Keterangan | Debit | Kredit | Saldo Kredit |
|---------|-----------|-------|--------|------------|
| 01-05-2026 | Jurnal Pembukaan - Dari Kas Kecil | - | 100.000.000 | 100.000.000 |
| 01-05-2026 | Jurnal Pembukaan - Dari Bank BCA | - | 50.000.000 | 150.000.000 |
| (transaksi berikutnya) | ... | ... | ... | ... |

---

## ✅ Fitur yang Sudah Diimplementasikan

### Buku Besar
- ✅ Menampilkan jurnal pembukaan (SaldoAwal) untuk akun yang dipilih
- ✅ Akun kas/bank menampilkan SaldoAwal di sisi DEBIT
- ✅ Akun Modal Pemilik menampilkan SaldoAwal di sisi KREDIT
- ✅ Jurnal pembukaan otomatis diurutkan berdasarkan tanggal
- ✅ Saldo berjalan konsisten (SALDO AWAL → Transaksi → SALDO AKHIR)

### Jurnal Umum
- ✅ Menampilkan jurnal pembukaan (SaldoAwal) untuk semua akun
- ✅ Double entry: Debit pada akun kas/bank, Kredit pada Modal Pemilik
- ✅ Jurnal pembukaan ditampilkan dengan no_bukti (SA-001, SA-002, dst)

### Kompatibilitas
- ✅ Tetap kompatibel dengan logika carry forward balance
- ✅ Saldo awal hanya diinput satu kali
- ✅ Periode berikutnya mengambil saldo akhir periode sebelumnya
- ✅ Siap untuk integrasi modul lain (Penjualan, Produksi)

---

## 🔍 Verifikasi yang Dilakukan

### Test Case 1: Buku Besar Akun Kas Kecil (111)
```
Input: Saldo Awal Kas Kecil = Rp 100.000.000 (01-05-2026)

Expected:
- Buku Besar periode Mei 2026
- Baris pertama: Jurnal Pembukaan dengan Debit Rp 100.000.000
- Saldo awal Rp 100.000.000 pada sisi Debit ✓
```

### Test Case 2: Buku Besar Akun Modal Pemilik (311)
```
Input: Saldo Awal Kas Kecil = Rp 100.000.000 (01-05-2026)

Expected:
- Buku Besar Akun 311 periode Mei 2026
- Baris pertama: Jurnal Pembukaan "Dari Kas Kecil" dengan Kredit Rp 100.000.000
- Saldo awal Rp 100.000.000 pada sisi Kredit ✓
```

### Test Case 3: Jurnal Umum
```
Input: Saldo Awal (111) = Rp 100jt, (112) = Rp 50jt

Expected:
- Jurnal Umum menampilkan 4 baris:
  - SA-001 Debit 111 Rp 100jt, Kredit 311 Rp 100jt
  - SA-002 Debit 112 Rp 50jt, Kredit 311 Rp 50jt
- Balance: Total Debit = Total Kredit = Rp 150jt ✓
```

### Test Case 4: Carry Forward Balance (Periode Berikutnya)
```
Input: Periode Mei dengan Saldo Awal Rp 100jt, Transaksi +Rp 50jt

Expected:
- Periode Mei: Saldo Akhir = Rp 150jt
- Periode Juni: Saldo Awal = Rp 150jt (dari Saldo Akhir Mei)
- Jurnal pembukaan TIDAK bertambah di periode Juni ✓
```

---

## 📌 Catatan Penting

### ✅ Yang Sudah Ditangani
1. Double entry accounting otomatis di Buku Besar
2. Double entry accounting sudah ada di Jurnal Umum
3. Jurnal pembukaan hanya tampil pada periode pertama
4. Akun Modal Pemilik (311) menampilkan kredit dari semua SaldoAwal
5. Kompatibel dengan carry forward balance

### ⚠️ Batasan
1. SaldoAwal hanya untuk akun kas/bank (111, 112) - diatur di controller
2. Modal Pemilik (311) adalah akun pasangan fixed - tidak bisa diubah
3. Jika user input SaldoAwal untuk akun selain kas/bank, sistem tidak akan otomatis membuat double entry (tergantung requirement)

### 🔧 Kustomisasi di Masa Depan
Jika perlu mengubah akun pasangan Modal Pemilik (311) menjadi akun lain:
1. Update di BukuBesarController: `$coa311 = Coa::where('kode_akun', '311')->first();`
2. Ubah kode_akun '311' menjadi kode_akun yang diinginkan
3. Update di JurnalUmumController dengan perubahan yang sama

---

## 🧪 Cara Testing

### Test 1: Buka Buku Besar Akun 111
1. Menu: Buku Besar
2. Pilih Akun: 111 - Kas Kecil
3. Pilih Periode: Mei 2026
4. **Verifikasi:**
   - ✓ Baris pertama: "Jurnal Pembukaan - Kas Kecil"
   - ✓ Debit: Rp 100.000.000
   - ✓ Saldo awal: Rp 100.000.000 (Debit)

### Test 2: Buka Buku Besar Akun 311
1. Menu: Buku Besar
2. Pilih Akun: 311 - Modal Pemilik
3. Pilih Periode: Mei 2026
4. **Verifikasi:**
   - ✓ Baris pertama: "Jurnal Pembukaan - Dari Kas Kecil"
   - ✓ Kredit: Rp 100.000.000
   - ✓ Saldo awal: Rp 100.000.000 (Kredit)

### Test 3: Buka Jurnal Umum
1. Menu: Jurnal Umum
2. Pilih Periode: Mei 2026
3. **Verifikasi:**
   - ✓ Ada 2 baris untuk SA-001 (111 Debit & 311 Kredit)
   - ✓ Saldo akhir berjalan konsisten
   - ✓ Total Debit = Total Kredit

---

## 📝 Ringkasan Implementasi

| Aspek | Status | Keterangan |
|-------|--------|-----------|
| **Double Entry di Buku Besar** | ✅ Selesai | SaldoAwal tampil sebagai Debit/Kredit |
| **Double Entry di Jurnal Umum** | ✅ Sudah Ada | Implementasi sudah benar |
| **Akun Kas/Bank (Debit)** | ✅ Selesai | Tampil di sisi Debit |
| **Akun Modal Pemilik (Kredit)** | ✅ Selesai | Tampil di sisi Kredit |
| **Carry Forward Balance** | ✅ Kompatibel | Jurnal pembukaan hanya di periode pertama |
| **Integrasi Modul Lain** | ✅ Kompatibel | Struktur siap untuk ekspansi |

---

**Status: ✅ IMPLEMENTASI SELESAI - SIAP TESTING**
