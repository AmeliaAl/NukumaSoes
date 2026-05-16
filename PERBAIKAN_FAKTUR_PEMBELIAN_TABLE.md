# Perbaikan Tabel Faktur Pembelian - Kolom Nama Aset

## 📋 Masalah
Di tabel Faktur Pembelian, kolom "Nama Aset" menampilkan data item dari relasi `items`, namun:
- Nama aset tampil duplikat jika ada beberapa item dengan nama yang sama
- Contoh: Input 2x "Laptop (1)" akan muncul 2 kali di tabel
- Format tampilan kurang jelas untuk multiple items

**Sebelum:**
```
| No. Faktur | Vendor    | Nama Aset                           | Total      |
|------------|-----------|-------------------------------------|------------|
| FP-001     | PT ABC    | Laptop (1), Laptop (1), Mouse (1)   | Rp 15.000  |
| FP-002     | PT XYZ    | Monitor (1), Monitor (1), Monitor(1)| Rp 9.000   |
```

**Sesudah:**
```
| No. Faktur | Vendor    | Nama Aset                           | Total      |
|------------|-----------|-------------------------------------|------------|
| FP-001     | PT ABC    | • Laptop (2)                        | Rp 15.000  |
|            |           | • Mouse (1)                         |            |
| FP-002     | PT XYZ    | Monitor (3)                         | Rp 9.000   |
```

## ✅ Solusi
Memperbaiki format tampilan kolom "Nama Aset" dengan:
1. **Group by nama aset** - Item dengan nama sama digabung
2. **Sum quantity** - Total qty untuk nama aset yang sama dijumlahkan
3. Format bullet list untuk multiple items
4. Limit 3 item pertama dengan indikator "..." jika lebih banyak

**Contoh:**
- Input: Laptop qty 1, Laptop qty 1, Mouse qty 2
- Output: Laptop (2), Mouse (2)

## 🔧 Perubahan yang Dilakukan

### File: `app/Filament/Admin/Resources/FakturPembelians/Tables/FakturPembeliansTable.php`

#### 1. Memperbaiki Format Tampilan Nama Aset

```php
TextColumn::make('items')
    ->label('Nama Aset')
    ->formatStateUsing(function ($record) {
        if ($record->items->isEmpty()) {
            return '-';
        }
        
        // Group by nama_aset dan sum qty
        $grouped = $record->items->groupBy('nama_aset')->map(function ($items, $namaAset) {
            $totalQty = $items->sum('qty');
            return $namaAset . ' (' . $totalQty . ')';
        })->values();
        
        // Jika hanya 1 jenis item, tampilkan langsung
        if ($grouped->count() === 1) {
            return $grouped->first();
        }
        
        // Jika lebih dari 1, tampilkan dengan bullet
        return $grouped->take(3)->join("\n• ", '• ') . 
               ($grouped->count() > 3 ? "\n• ..." : '');
    })
    ->wrap()
    ->html()
    ->limit(50),
```

**Fitur:**
- ✅ **Group by nama**: Item dengan nama sama digabung jadi satu
- ✅ **Sum quantity**: Total qty dijumlahkan untuk nama yang sama
- ✅ **Bullet List**: Format bullet untuk multiple items
- ✅ **Limit**: Maksimal 3 item ditampilkan, sisanya "..."
- ✅ **Empty State**: Tampilkan "-" jika tidak ada item
- ✅ **Single Item**: Format sederhana untuk 1 jenis item saja

#### 2. Menambahkan Eager Loading

```php
public static function configure(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn ($query) => $query->with(['vendor', 'items']))
        ->columns([
            // ...
        ]);
}
```

**Manfaat:**
- Mengurangi jumlah query ke database (N+1 problem)
- Meningkatkan performa loading tabel
- Lebih efisien untuk data dalam jumlah banyak

## 📝 Contoh Output

### Kasus 1: Single Item
```
Laptop (2)
```

### Kasus 2: Multiple Items (≤3)
```
• Laptop (2)
• Mouse (1)
• Keyboard (1)
```

### Kasus 3: Multiple Items (>3)
```
• Laptop (2)
• Mouse (1)
• Keyboard (1)
• ...
```

### Kasus 4: No Items
```
-
```

## 🎯 Logic Penghilangan Duplikat

```php
// Group by nama_aset dan sum qty
$grouped = $record->items->groupBy('nama_aset')->map(function ($items, $namaAset) {
    $totalQty = $items->sum('qty');
    return $namaAset . ' (' . $totalQty . ')';
})->values();
```

**Cara Kerja:**
1. `groupBy('nama_aset')` - Kelompokkan items berdasarkan nama aset
2. Loop setiap group
3. `sum('qty')` - Jumlahkan total qty untuk nama yang sama
4. Format: `nama_aset (total_qty)`
5. `values()` - Reset index array

**Contoh:**

**Input (3 items):**
```php
[
    ['nama_aset' => 'Laptop', 'qty' => 1],
    ['nama_aset' => 'Laptop', 'qty' => 1],
    ['nama_aset' => 'Mouse', 'qty' => 2],
]
```

**Proses:**
```php
// Setelah groupBy('nama_aset')
[
    'Laptop' => [
        ['nama_aset' => 'Laptop', 'qty' => 1],
        ['nama_aset' => 'Laptop', 'qty' => 1],
    ],
    'Mouse' => [
        ['nama_aset' => 'Mouse', 'qty' => 2],
    ]
]

// Setelah sum qty dan format
[
    'Laptop (2)',  // 1 + 1 = 2
    'Mouse (2)',   // 2
]
```

**Output:**
```
• Laptop (2)
• Mouse (2)
```

## ⚠️ Constraint yang Dipatuhi

✅ **Tidak mengubah struktur database**  
✅ **Tidak mengubah relasi model**  
✅ **Tidak mengubah data asli**  
✅ **Fokus hanya pada tampilan di tabel Filament**

## 🧪 Testing

1. Buka halaman Faktur Pembelian (`/admin/faktur-pembelians`)
2. Cek kolom "Nama Aset":
   - Tidak ada duplikat
   - Menampilkan quantity dalam kurung
   - Format bullet untuk multiple items
   - Maksimal 3 item ditampilkan

## 📁 File yang Diubah

1. `app/Filament/Admin/Resources/FakturPembelians/Tables/FakturPembeliansTable.php`

## 🔗 Relasi Terkait

- Model: `app/Models/FakturPembelian.php` (relasi `items()`)
- Model: `app/Models/fakturPembelianItem.php` (item detail)
- Tabel: `faktur_pembelian` (header)
- Tabel: `faktur_pembelian_item` (detail items)
