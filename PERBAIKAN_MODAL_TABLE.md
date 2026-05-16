# Perbaikan Tabel Modal - Tampilan Kas/Bank

## 📋 Masalah
Di tabel Modal, kolom Kas/Bank menampilkan ID akun (angka), bukan nama bank/kas yang mudah dibaca.

**Sebelum:**
```
| Tanggal    | Jenis    | Kas / Bank | Jumlah        |
|------------|----------|------------|---------------|
| 2026-05-04 | Setoran  | 5          | Rp 1.000.000  |
| 2026-05-03 | Prive    | 3          | Rp 500.000    |
```

**Sesudah:**
```
| Tanggal    | Jenis    | Kas / Bank      | Jumlah        |
|------------|----------|-----------------|---------------|
| 2026-05-04 | Setoran  | Bank BCA        | Rp 1.000.000  |
| 2026-05-03 | Prive    | Kas Kecil       | Rp 500.000    |
```

## ✅ Solusi
Menggunakan relasi `akun` yang sudah ada di model Modal untuk menampilkan nama akun.

## 🔧 Perubahan yang Dilakukan

### File: `app/Filament/Admin/Resources/Modals/Tables/ModalsTable.php`

#### 1. Mengubah Kolom dari ID ke Nama Akun
```php
// ❌ Sebelum
TextColumn::make('id_akun')
    ->label('Kas / Bank'),

// ✅ Sesudah
TextColumn::make('akun.nama_akun')
    ->label('Kas / Bank')
    ->searchable()
    ->sortable(),
```

#### 2. Menambahkan Eager Loading
```php
public static function configure(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn ($query) => $query->with('akun'))
        ->columns([
            // ...
        ]);
}
```

**Manfaat Eager Loading:**
- Mengurangi jumlah query ke database (N+1 problem)
- Meningkatkan performa loading tabel
- Lebih efisien untuk data dalam jumlah banyak

## 📝 Relasi yang Digunakan

Model `Modal` sudah memiliki relasi ke `Akun`:

```php
// app/Models/Modal.php
public function akun()
{
    return $this->belongsTo(Akun::class, 'id_akun');
}
```

## ✨ Fitur Tambahan

Kolom Kas/Bank sekarang memiliki:
- ✅ **Searchable**: Bisa dicari berdasarkan nama bank/kas
- ✅ **Sortable**: Bisa diurutkan berdasarkan nama bank/kas
- ✅ **Readable**: Menampilkan nama yang mudah dibaca, bukan ID

## 🧪 Testing

1. Buka halaman Modal (`/admin/modals`)
2. Cek kolom "Kas / Bank" - seharusnya menampilkan nama akun (misal: "Bank BCA", "Kas Kecil")
3. Coba search berdasarkan nama bank
4. Coba sort kolom Kas / Bank

## 📁 File yang Diubah

1. `app/Filament/Admin/Resources/Modals/Tables/ModalsTable.php`

## 🔗 Relasi Terkait

- Model: `app/Models/Modal.php` (sudah ada relasi `akun()`)
- Model: `app/Models/Akun.php` (tabel master akun)
