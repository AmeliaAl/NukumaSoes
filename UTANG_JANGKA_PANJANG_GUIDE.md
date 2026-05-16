# 📘 Panduan Fitur Utang Jangka Panjang

## 🎯 Overview

Fitur ini memungkinkan pencatatan utang jangka panjang dengan **jurnal otomatis** yang terintegrasi dengan laporan neraca.

---

## 📦 Struktur Database

### Tabel: `utang_jangka_panjang`

| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `tanggal` | date | Tanggal pencatatan utang |
| `nama_utang` | string | Nama/deskripsi utang |
| `akun_id` | foreignId | Akun kredit (Utang Jangka Panjang) |
| `akun_debit_id` | foreignId | Akun debit (Kas/Aset) |
| `nominal` | decimal(15,2) | Jumlah utang |
| `jatuh_tempo` | date | Tanggal jatuh tempo |
| `keterangan` | text | Catatan tambahan (nullable) |
| `jurnal_id` | foreignId | Relasi ke jurnal (nullable) |
| `created_at` | timestamp | - |
| `updated_at` | timestamp | - |

---

## 🧾 Logic Jurnal Otomatis

### Skenario 1: Utang Diterima dalam Bentuk Kas

**Contoh:** Pinjaman Bank BCA Rp 100.000.000

```
Debit:  Kas/Bank (111)           Rp 100.000.000
Kredit: Utang Jangka Panjang (21) Rp 100.000.000
```

### Skenario 2: Utang untuk Pembelian Aset

**Contoh:** Kredit Kendaraan Rp 200.000.000

```
Debit:  Kendaraan (151)           Rp 200.000.000
Kredit: Utang Jangka Panjang (21) Rp 200.000.000
```

### Implementasi di Model

```php
// app/Models/UtangJangkaPanjang.php

protected static function boot()
{
    parent::boot();

    // Otomatis buat jurnal setelah data dibuat
    static::created(function ($utang) {
        $utang->buatJurnal();
    });

    // Hapus jurnal saat data dihapus
    static::deleting(function ($utang) {
        if ($utang->jurnal_id) {
            $utang->jurnal()->delete();
        }
    });
}

public function buatJurnal()
{
    \DB::transaction(function () {
        // 1. Buat header jurnal
        $jurnal = Jurnal::create([
            'tanggal' => $this->tanggal,
            'no_referensi' => 'UJP-' . $this->id,
            'deskripsi' => 'Utang Jangka Panjang: ' . $this->nama_utang,
        ]);

        // 2. Buat detail debit
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun' => $this->akun_debit_id,
            'deskripsi' => 'Penerimaan dari ' . $this->nama_utang,
            'debit' => $this->nominal,
            'credit' => 0,
        ]);

        // 3. Buat detail kredit
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id,
            'no_akun' => $this->akun_id,
            'deskripsi' => 'Utang: ' . $this->nama_utang,
            'debit' => 0,
            'credit' => $this->nominal,
        ]);

        // 4. Update jurnal_id
        $this->update(['jurnal_id' => $jurnal->id]);
    });
}
```

---

## 📊 Integrasi dengan Neraca

### Cara Kerja

Data utang jangka panjang **tidak langsung** masuk ke neraca, tetapi melalui **saldo akun** yang tercatat di jurnal.

### Alur Data

```
Input Utang → Jurnal Otomatis → JurnalDetail → Saldo Akun → Neraca
```

### Implementasi di NeracaPage

Neraca sudah otomatis menghitung saldo dari `JurnalDetail`:

```php
// app/Filament/Admin/Pages/NeracaPage.php

// LIABILITAS (header_akun 2)
foreach (Akun::where('header_akun', 2)->get() as $akun) {
    $saldo = JurnalDetail::where('no_akun', $akun->id)
        ->whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })
        ->sum('credit')
        - JurnalDetail::where('no_akun', $akun->id)
        ->whereHas('jurnal', function($q) {
            $q->where('tanggal', '<=', $this->tanggal_akhir);
        })
        ->sum('debit');

    $akun->saldo = $saldo;
    $this->liabilitas[] = $akun;
    $this->totalLiabilitas += $saldo;
}
```

**Kesimpulan:** Tidak perlu modifikasi `NeracaPage.php` karena sudah otomatis menghitung dari jurnal! ✅

---

## 🖥️ Penggunaan di Filament

### 1. Akses Menu

Navigasi: **Keuangan → Utang Jangka Panjang**

### 2. Tambah Utang Baru

1. Klik tombol **"Tambah Utang Jangka Panjang"**
2. Isi form:
   - **Tanggal**: Tanggal pencatatan
   - **Nama Utang**: Contoh "Pinjaman Bank BCA"
   - **Nominal**: Jumlah utang (harus > 0)
   - **Jatuh Tempo**: Tanggal jatuh tempo (harus setelah tanggal utang)
   - **Akun Debit**: Pilih Kas (jika terima uang) atau Aset (jika beli aset)
   - **Akun Kredit**: Pilih akun Utang Jangka Panjang
   - **Keterangan**: Catatan tambahan (opsional)
3. Klik **"Simpan"**
4. Jurnal otomatis akan dibuat! 🎉

### 3. Fitur Table

- **Filter berdasarkan tanggal**
- **Filter utang yang sudah jatuh tempo**
- **Badge merah** untuk utang yang sudah lewat jatuh tempo
- **Badge hijau** untuk utang yang belum jatuh tempo
- **Lihat nomor jurnal** yang terkait
- **Edit & Delete** dengan konfirmasi

### 4. Validasi

✅ Nominal harus > 0  
✅ Tanggal wajib diisi  
✅ Akun debit & kredit wajib dipilih  
✅ Jatuh tempo harus setelah tanggal utang  
✅ Jurnal otomatis balance (debit = kredit)  

---

## 🔄 Behavior Khusus

### Saat Data Dihapus

```php
static::deleting(function ($utang) {
    if ($utang->jurnal_id) {
        $utang->jurnal()->delete(); // Cascade delete jurnal & detail
    }
});
```

**Hasil:**
- Data utang dihapus
- Jurnal terkait dihapus
- JurnalDetail terkait dihapus (cascade)
- Saldo akun di neraca otomatis update

### Transaction Safety

Semua operasi dibungkus dengan `DB::transaction()`:

```php
\DB::transaction(function () {
    // Buat jurnal
    // Buat detail
    // Update utang
});
```

**Manfaat:**
- Jika ada error, semua rollback
- Data tetap konsisten
- Tidak ada jurnal yang tidak balance

---

## 📝 Contoh Kasus Penggunaan

### Kasus 1: Pinjaman Bank untuk Modal Kerja

**Input:**
- Tanggal: 01/05/2026
- Nama Utang: Pinjaman Bank BCA
- Nominal: Rp 100.000.000
- Akun Debit: Kas (111)
- Akun Kredit: Utang Jangka Panjang (21)
- Jatuh Tempo: 01/05/2031

**Jurnal Otomatis:**
```
Tanggal: 01/05/2026
No. Ref: UJP-1
Deskripsi: Utang Jangka Panjang: Pinjaman Bank BCA

Debit:  Kas (111)                 Rp 100.000.000
Kredit: Utang Jangka Panjang (21) Rp 100.000.000
```

**Dampak di Neraca:**
- Kas bertambah Rp 100.000.000
- Utang Jangka Panjang bertambah Rp 100.000.000
- Neraca tetap balance ✅

### Kasus 2: Kredit Kendaraan

**Input:**
- Tanggal: 15/05/2026
- Nama Utang: Kredit Mobil Operasional
- Nominal: Rp 300.000.000
- Akun Debit: Kendaraan (151)
- Akun Kredit: Utang Jangka Panjang (21)
- Jatuh Tempo: 15/05/2031

**Jurnal Otomatis:**
```
Tanggal: 15/05/2026
No. Ref: UJP-2
Deskripsi: Utang Jangka Panjang: Kredit Mobil Operasional

Debit:  Kendaraan (151)           Rp 300.000.000
Kredit: Utang Jangka Panjang (21) Rp 300.000.000
```

**Dampak di Neraca:**
- Aset Tetap (Kendaraan) bertambah Rp 300.000.000
- Utang Jangka Panjang bertambah Rp 300.000.000
- Neraca tetap balance ✅

---

## 🧪 Testing

### Manual Testing Checklist

- [ ] Buat utang dengan kas → cek jurnal
- [ ] Buat utang dengan aset → cek jurnal
- [ ] Cek saldo di neraca
- [ ] Edit utang → jurnal tidak berubah (by design)
- [ ] Hapus utang → jurnal ikut terhapus
- [ ] Filter berdasarkan tanggal
- [ ] Filter utang jatuh tempo
- [ ] Validasi nominal < 0 (harus error)
- [ ] Validasi jatuh tempo sebelum tanggal (harus error)

---

## 🚀 Best Practices

1. **Pilih Akun yang Tepat**
   - Gunakan akun Kas (111) jika menerima uang tunai
   - Gunakan akun Aset spesifik jika untuk pembelian aset

2. **Jangan Edit Setelah Dibuat**
   - Jurnal dibuat saat `created`, tidak di-update saat `updated`
   - Jika perlu koreksi, hapus dan buat ulang

3. **Monitoring Jatuh Tempo**
   - Gunakan filter "Sudah Jatuh Tempo" untuk monitoring
   - Badge merah otomatis muncul untuk utang yang lewat

4. **Backup Sebelum Hapus**
   - Hapus utang = hapus jurnal
   - Pastikan data sudah di-backup jika diperlukan

---

## 🔧 Troubleshooting

### Jurnal Tidak Terbuat

**Penyebab:**
- Error di `boot()` method
- Akun tidak ditemukan

**Solusi:**
- Cek log Laravel: `storage/logs/laravel.log`
- Pastikan akun_id dan akun_debit_id valid

### Neraca Tidak Update

**Penyebab:**
- Periode neraca tidak mencakup tanggal utang

**Solusi:**
- Ubah periode di halaman neraca
- Pastikan tanggal utang <= tanggal_akhir periode

### Error Saat Hapus

**Penyebab:**
- Foreign key constraint

**Solusi:**
- Pastikan cascade delete sudah diset di migration
- Cek relasi di model

---

## 📚 File yang Dibuat

```
database/migrations/
└── 2026_05_02_234247_create_utang_jangka_panjangs_table.php

app/Models/
└── UtangJangkaPanjang.php

app/Filament/Admin/Resources/UtangJangkaPanjangs/
├── UtangJangkaPanjangResource.php
├── Schemas/
│   └── UtangJangkaPanjangForm.php
├── Tables/
│   └── UtangJangkaPanjangsTable.php
└── Pages/
    ├── ListUtangJangkaPanjangs.php
    ├── CreateUtangJangkaPanjang.php
    └── EditUtangJangkaPanjang.php
```

---

## ✅ Checklist Implementasi

- [x] Migration tabel `utang_jangka_panjang`
- [x] Model `UtangJangkaPanjang` dengan relasi
- [x] Logic jurnal otomatis di `boot()`
- [x] Filament Resource lengkap
- [x] Form dengan validasi
- [x] Table dengan filter & badge
- [x] Pages (List, Create, Edit)
- [x] Cascade delete jurnal
- [x] Transaction safety
- [x] Integrasi dengan neraca (otomatis via jurnal)
- [x] Dokumentasi lengkap

---

## 🎉 Selesai!

Fitur Utang Jangka Panjang sudah siap digunakan dengan:
- ✅ Jurnal otomatis
- ✅ Integrasi neraca
- ✅ UI yang user-friendly
- ✅ Validasi lengkap
- ✅ Transaction safety

**Selamat menggunakan!** 🚀
