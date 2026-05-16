# 📘 Panduan: Sistem Role Admin & Pemilik

## 🎯 Overview

Sistem role sederhana dengan 2 jenis user:
- **Admin** → Full akses (CRUD semua data)
- **Pemilik** → Read-only laporan (Jurnal, Buku Besar, Neraca)

---

## 👥 Role & Akses

### 1. Admin
**Akses Penuh:**
- ✅ Lihat semua menu
- ✅ Tambah data (Create)
- ✅ Edit data (Update)
- ✅ Hapus data (Delete)
- ✅ Lihat laporan

**Menu yang Bisa Diakses:**
- Master Data (Akun, Kategori Aset, Lokasi, dll)
- Transaksi (Faktur Pembelian, Pembayaran, dll)
- Keuangan (Utang Jangka Panjang, Saldo Awal)
- Laporan (Jurnal, Buku Besar, Neraca)

### 2. Pemilik
**Akses Terbatas (Read-Only):**
- ✅ Lihat Jurnal
- ✅ Lihat Buku Besar
- ✅ Lihat Neraca
- ❌ Tidak bisa tambah/edit/hapus
- ❌ Tidak bisa akses menu lain

**Menu yang Bisa Diakses:**
- Laporan → Jurnal (read-only)
- Laporan → Buku Besar (read-only)
- Laporan → Laporan Posisi Keuangan (read-only)

---

## 🔧 Implementasi Teknis

### 1. Database

**Tabel `users`:**
```sql
ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'admin';
```

**Field:**
- `role`: 'admin' atau 'pemilik'

### 2. Model User

**File:** `app/Models/User.php`

**Methods:**
```php
// Check if user is admin
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

// Check if user is pemilik
public function isPemilik(): bool
{
    return $this->role === 'pemilik';
}
```

### 3. Enum UserRole

**File:** `app/Enums/UserRole.php`

```php
enum UserRole: string
{
    case ADMIN = 'admin';
    case PEMILIK = 'pemilik';
}
```

### 4. Trait HasRoleAccess

**File:** `app/Traits/HasRoleAccess.php`

**Methods:**
- `canAccess()` - Cek apakah user bisa akses resource
- `canCreate()` - Cek apakah user bisa create (hanya admin)
- `canEdit()` - Cek apakah user bisa edit (hanya admin)
- `canDelete()` - Cek apakah user bisa delete (hanya admin)
- `canView()` - Cek apakah user bisa view

**Cara Pakai di Resource:**
```php
use App\Traits\HasRoleAccess;

class JurnalResource extends Resource
{
    use HasRoleAccess;

    /**
     * Pemilik bisa akses Jurnal (read-only)
     */
    protected static function canAccessByPemilik(): bool
    {
        return true;
    }
}
```

### 5. Update Resources

**Resources yang Bisa Diakses Pemilik:**
1. ✅ `JurnalResource` - Tambah trait & method `canAccessByPemilik()`
2. ✅ `BukuBesarResource` - Tambah trait & method `canAccessByPemilik()`
3. ✅ `NeracaPage` - Tambah method `canAccess()`

**Resources yang Tidak Bisa Diakses Pemilik:**
- Semua resource lain (default: `canAccessByPemilik()` return false)

---

## 🚀 Cara Menggunakan

### 1. Jalankan Migration & Seeder

```bash
# Migration sudah dijalankan ✅
php artisan migrate

# Jalankan seeder untuk create user
php artisan db:seed --class=UserRoleSeeder
```

**Output:**
```
✅ Admin user created: admin@example.com / password
✅ Pemilik user created: pemilik@example.com / password
🎉 User roles seeded successfully!
```

### 2. Login sebagai Admin

```
Email: admin@example.com
Password: password
```

**Akses:**
- Semua menu muncul
- Bisa tambah/edit/hapus data
- Bisa lihat laporan

### 3. Login sebagai Pemilik

```
Email: pemilik@example.com
Password: password
```

**Akses:**
- Hanya menu Laporan yang muncul:
  - Jurnal
  - Buku Besar
  - Laporan Posisi Keuangan
- Tidak ada tombol "Tambah", "Edit", "Hapus"
- Read-only

---

## 📊 Contoh Tampilan

### Admin Dashboard
```
┌─────────────────────────────────────────┐
│ MENU                                    │
├─────────────────────────────────────────┤
│ Master Data                             │
│   ├─ Akun                               │
│   ├─ Kategori Aset                      │
│   └─ Lokasi Aset                        │
│                                         │
│ Transaksi                               │
│   ├─ Faktur Pembelian                   │
│   └─ Pembayaran Aset                    │
│                                         │
│ Keuangan                                │
│   ├─ Utang Jangka Panjang               │
│   └─ Saldo Awal                         │
│                                         │
│ Laporan                                 │
│   ├─ Jurnal                             │
│   ├─ Buku Besar                         │
│   └─ Laporan Posisi Keuangan            │
└─────────────────────────────────────────┘
```

### Pemilik Dashboard
```
┌─────────────────────────────────────────┐
│ MENU                                    │
├─────────────────────────────────────────┤
│ Laporan                                 │
│   ├─ Jurnal                             │
│   ├─ Buku Besar                         │
│   └─ Laporan Posisi Keuangan            │
└─────────────────────────────────────────┘
```

---

## 🔐 Keamanan

### 1. Middleware

Filament otomatis handle authentication:
- User harus login untuk akses
- Setiap resource cek `canAccess()`

### 2. Authorization

**Level Resource:**
```php
public static function canAccess(): bool
{
    $user = Auth::user();
    
    if ($user->isAdmin()) {
        return true; // Admin bisa akses semua
    }
    
    if ($user->isPemilik()) {
        return static::canAccessByPemilik(); // Cek per resource
    }
    
    return false;
}
```

**Level Action:**
```php
public static function canCreate(): bool
{
    return Auth::user()->isAdmin(); // Hanya admin
}

public static function canEdit($record): bool
{
    return Auth::user()->isAdmin(); // Hanya admin
}

public static function canDelete($record): bool
{
    return Auth::user()->isAdmin(); // Hanya admin
}
```

### 3. Navigation

Menu otomatis hide jika user tidak punya akses:
- Pemilik tidak akan lihat menu Master Data, Transaksi, Keuangan
- Hanya menu Laporan yang muncul

---

## 🧪 Testing

### Test Admin
1. Login sebagai admin
2. Cek semua menu muncul
3. Buka Faktur Pembelian → ada tombol "Tambah"
4. Buka Jurnal → bisa lihat data
5. Buka Neraca → bisa lihat laporan

### Test Pemilik
1. Login sebagai pemilik
2. Cek hanya menu Laporan yang muncul
3. Buka Jurnal → bisa lihat data, tidak ada tombol "Tambah"
4. Buka Buku Besar → bisa lihat data
5. Buka Neraca → bisa lihat laporan
6. Coba akses URL resource lain (misal /admin/asets) → redirect/forbidden

---

## 🔄 Menambah Resource Baru

### Jika Resource Hanya untuk Admin

**Tidak perlu tambah apa-apa**, default sudah tidak bisa diakses pemilik.

### Jika Resource Bisa Diakses Pemilik

**Tambahkan trait dan method:**

```php
use App\Traits\HasRoleAccess;

class NewResource extends Resource
{
    use HasRoleAccess;

    /**
     * Pemilik bisa akses resource ini
     */
    protected static function canAccessByPemilik(): bool
    {
        return true; // ← Set true jika pemilik bisa akses
    }
}
```

---

## 📝 Menambah User Baru

### Via Tinker

```bash
php artisan tinker
```

```php
// Create admin
User::create([
    'name' => 'Admin Baru',
    'email' => 'admin2@example.com',
    'password' => Hash::make('password123'),
    'role' => 'admin',
]);

// Create pemilik
User::create([
    'name' => 'Pemilik Baru',
    'email' => 'pemilik2@example.com',
    'password' => Hash::make('password123'),
    'role' => 'pemilik',
]);
```

### Via Seeder

Edit `database/seeders/UserRoleSeeder.php` dan tambahkan user baru.

---

## ⚠️ Catatan Penting

### 1. User Existing

Semua user yang sudah ada akan otomatis jadi **admin** (via seeder).

### 2. Default Role

User baru default role = **admin** (via migration).

### 3. Password Default

Password default untuk user seeder: **password**

**⚠️ GANTI PASSWORD SETELAH LOGIN PERTAMA!**

### 4. Tidak Ada UI untuk Manage User

Saat ini belum ada UI untuk:
- Tambah user baru
- Edit role user
- Hapus user

**Solusi sementara:** Gunakan tinker atau database langsung.

**Solusi permanen:** Buat UserResource dengan Filament (opsional).

---

## 🔧 Troubleshooting

### Problem 1: Pemilik Bisa Akses Semua Menu

**Penyebab:**
- Trait `HasRoleAccess` belum ditambahkan ke resource

**Solusi:**
```php
use App\Traits\HasRoleAccess;

class YourResource extends Resource
{
    use HasRoleAccess; // ← Tambahkan ini
}
```

### Problem 2: Admin Tidak Bisa Create/Edit

**Penyebab:**
- Method `canCreate()` atau `canEdit()` di-override tanpa cek role

**Solusi:**
```php
// Hapus method ini jika ada
public static function canCreate(): bool
{
    return false; // ← Ini yang bikin admin tidak bisa create
}

// Atau gunakan dari trait
// (tidak perlu override jika pakai trait)
```

### Problem 3: Menu Tidak Muncul

**Penyebab:**
- Method `canAccess()` return false

**Solusi:**
- Pastikan trait sudah ditambahkan
- Pastikan `canAccessByPemilik()` return true untuk resource yang boleh diakses pemilik

---

## 📁 File yang Dibuat/Diubah

### Baru
1. ✅ `database/migrations/2026_05_04_145551_add_role_to_users_table.php`
2. ✅ `app/Enums/UserRole.php`
3. ✅ `app/Traits/HasRoleAccess.php`
4. ✅ `database/seeders/UserRoleSeeder.php`

### Diubah
1. ✅ `app/Models/User.php` (tambah methods)
2. ✅ `app/Filament/Admin/Resources/Jurnals/JurnalResource.php` (tambah trait)
3. ✅ `app/Filament/Admin/Resources/BukuBesars/BukuBesarResource.php` (tambah trait)
4. ✅ `app/Filament/Admin/Pages/NeracaPage.php` (tambah canAccess)

---

## ✅ Status

**SELESAI** - Sistem role sudah aktif!

- ✅ Migration berhasil
- ✅ Model User updated
- ✅ Enum & Trait dibuat
- ✅ Resources updated
- ✅ Seeder tersedia
- ✅ Dokumentasi lengkap

**Tanggal:** 4 Mei 2026

---

**Silakan test dengan login sebagai admin dan pemilik!** 🚀
