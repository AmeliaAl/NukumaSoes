# 🧪 Testing & Troubleshooting: Field Satuan Faktur Pembelian

## 🎯 Masalah yang Dilaporkan

**Gejala:**
- Field satuan sudah masuk ke database ✅
- Tapi saat view/edit, yang muncul adalah "unit, pcs, kg, dll" (placeholder) ❌
- Nilai sebenarnya dari database tidak muncul ❌

---

## 🔍 Diagnosis

### Kemungkinan Penyebab:

1. **Data Lama NULL**
   - Data yang dibuat sebelum migration akan memiliki `keterangan` = NULL
   - Saat NULL, placeholder akan muncul (ini normal)

2. **Cache**
   - Form definition di-cache oleh Filament
   - Perlu clear cache setelah update form

3. **Dehydration Issue**
   - Field tidak ter-dehydrate dengan benar
   - Sudah diperbaiki dengan menambahkan `->dehydrated()`

---

## ✅ Solusi yang Sudah Diterapkan

### 1. Tambah `->dehydrated()` ke Field

```php
TextInput::make('keterangan')
    ->label('Satuan')
    ->placeholder('unit, pcs, kg, dll')
    ->maxLength(100)
    ->dehydrated()  // ← DITAMBAHKAN
    ->columnSpan(4),
```

### 2. Clear Cache

```bash
php artisan optimize:clear
```

---

## 🧪 Cara Testing

### Test 1: Input Data Baru

1. **Buka Faktur Pembelian**
   ```
   Transaksi → Faktur Pembelian → Tambah
   ```

2. **Isi Form**
   - Vendor: (pilih vendor)
   - Tanggal: (pilih tanggal)
   - No. Faktur: (otomatis)

3. **Tambah Item**
   - Nama Aset: **Laptop Dell**
   - Kategori: **Elektronik**
   - Qty: **5**
   - **Satuan: unit** ← ISI INI
   - Harga Satuan: **10.000.000**

4. **Simpan**

5. **Cek di List**
   - Klik tombol "View" (mata) pada faktur yang baru dibuat
   - **Lihat field Satuan** → Harus muncul "**unit**", bukan "unit, pcs, kg, dll"

### Test 2: Edit Data Lama

1. **Buka Faktur Lama**
   - Pilih faktur yang dibuat sebelum migration
   - Klik "View" atau "Edit"

2. **Cek Field Satuan**
   - Jika kosong/placeholder → **NORMAL** (karena data lama NULL)
   - Klik "Edit"

3. **Isi Satuan**
   - Isi field satuan (contoh: "pcs")
   - Simpan

4. **View Lagi**
   - Sekarang harus muncul "**pcs**"

### Test 3: Cek Database Langsung

```bash
php artisan tinker
```

```php
// Cek data terbaru
$item = \App\Models\fakturPembelianItem::latest()->first();
echo "Nama: " . $item->nama_aset . "\n";
echo "Qty: " . $item->qty . "\n";
echo "Satuan: " . ($item->keterangan ?? 'NULL') . "\n";
```

**Expected Output:**
```
Nama: Laptop Dell
Qty: 5
Satuan: unit
```

---

## 🐛 Troubleshooting

### Problem 1: Masih Muncul Placeholder

**Penyebab:**
- Data di database masih NULL
- Cache belum di-clear

**Solusi:**
```bash
# 1. Clear cache
php artisan optimize:clear

# 2. Cek database
php artisan tinker --execute="
\$item = \App\Models\fakturPembelianItem::find(ID_ITEM);
echo 'Keterangan: ' . (\$item->keterangan ?? 'NULL');
"

# 3. Jika NULL, edit dan isi ulang
```

### Problem 2: Data Tidak Tersimpan

**Penyebab:**
- Field tidak ada di `$fillable`
- Dehydration issue

**Solusi:**
```php
// Cek model
// File: app/Models/fakturPembelianItem.php

protected $fillable = [
    'id_faktur',
    'nama_aset',
    'id_kategori',
    'qty',
    'keterangan',  // ← HARUS ADA
    'harga_satuan',
    'total_harga',
];
```

### Problem 3: Error Saat Simpan

**Penyebab:**
- Migration belum dijalankan
- Field belum ada di database

**Solusi:**
```bash
# Cek apakah field ada
php artisan tinker --execute="
echo json_encode(DB::select('DESCRIBE faktur_pembelian_item'), JSON_PRETTY_PRINT);
"

# Jika tidak ada field 'keterangan', jalankan migration
php artisan migrate
```

---

## 📊 Perbedaan Placeholder vs Nilai

### Placeholder (Data NULL)
```
┌─────────────────────────────┐
│ Satuan                      │
│ unit, pcs, kg, dll          │ ← Abu-abu/tipis (placeholder)
└─────────────────────────────┘
```

### Nilai Sebenarnya (Data Ada)
```
┌─────────────────────────────┐
│ Satuan                      │
│ unit                        │ ← Hitam/tebal (nilai)
└─────────────────────────────┘
```

**Cara Membedakan:**
- **Placeholder**: Teks abu-abu, italic, hilang saat klik
- **Nilai**: Teks hitam, normal, tetap ada saat klik

---

## 🔄 Update Data Lama

Jika Anda punya banyak data lama yang perlu diisi satuan:

### Option 1: Manual via UI
1. Edit satu per satu
2. Isi field satuan
3. Simpan

### Option 2: Bulk Update via Tinker
```bash
php artisan tinker
```

```php
// Update semua item dengan satuan default
\App\Models\fakturPembelianItem::whereNull('keterangan')
    ->update(['keterangan' => 'unit']);

// Atau update berdasarkan kategori
\App\Models\fakturPembelianItem::whereNull('keterangan')
    ->whereHas('kategoriAset', function($q) {
        $q->where('nama_kategori', 'like', '%ATK%');
    })
    ->update(['keterangan' => 'pcs']);
```

### Option 3: Seeder
```php
// database/seeders/UpdateSatuanSeeder.php

public function run()
{
    // Elektronik → unit
    \App\Models\fakturPembelianItem::whereNull('keterangan')
        ->whereHas('kategoriAset', function($q) {
            $q->where('nama_kategori', 'like', '%Elektronik%');
        })
        ->update(['keterangan' => 'unit']);

    // ATK → pcs
    \App\Models\fakturPembelianItem::whereNull('keterangan')
        ->whereHas('kategoriAset', function($q) {
            $q->where('nama_kategori', 'like', '%ATK%');
        })
        ->update(['keterangan' => 'pcs']);

    // Furniture → unit
    \App\Models\fakturPembelianItem::whereNull('keterangan')
        ->whereHas('kategoriAset', function($q) {
            $q->where('nama_kategori', 'like', '%Furniture%');
        })
        ->update(['keterangan' => 'unit']);
}
```

```bash
php artisan db:seed --class=UpdateSatuanSeeder
```

---

## ✅ Checklist Testing

- [ ] Clear cache (`php artisan optimize:clear`)
- [ ] Buat faktur baru dengan satuan terisi
- [ ] Simpan dan cek database
- [ ] View faktur → satuan harus muncul (bukan placeholder)
- [ ] Edit faktur → satuan harus ter-load
- [ ] Update satuan → perubahan tersimpan
- [ ] Cek data lama → placeholder muncul (normal jika NULL)
- [ ] Edit data lama → isi satuan → simpan → view lagi

---

## 📝 Catatan Penting

### Behavior Normal
- ✅ Data baru dengan satuan terisi → muncul nilai
- ✅ Data lama tanpa satuan (NULL) → muncul placeholder
- ✅ Placeholder hilang saat field diklik/diisi

### Behavior Abnormal
- ❌ Data baru dengan satuan terisi → muncul placeholder
- ❌ Nilai tidak tersimpan ke database
- ❌ Error saat simpan

### Jika Masih Bermasalah

1. **Screenshot**
   - Ambil screenshot form saat view
   - Ambil screenshot database (phpMyAdmin/Tinker)

2. **Check Log**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Debug Mode**
   ```php
   // Tambahkan di form untuk debug
   TextInput::make('keterangan')
       ->label('Satuan')
       ->placeholder('unit, pcs, kg, dll')
       ->maxLength(100)
       ->dehydrated()
       ->afterStateHydrated(function ($state) {
           \Log::info('Keterangan loaded: ' . ($state ?? 'NULL'));
       })
       ->columnSpan(4),
   ```

---

**Status:** ✅ Sudah diperbaiki dengan `->dehydrated()`  
**Next Step:** Test dengan data baru  
**Tanggal:** 3 Mei 2026
