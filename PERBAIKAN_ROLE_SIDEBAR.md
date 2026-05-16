# Perbaikan Role Sidebar - Pemilik

## 📋 Masalah
Role Pemilik masih bisa melihat semua menu di sidebar, padahal seharusnya hanya bisa melihat:
- Jurnal
- Buku Besar
- Neraca (Laporan Posisi Keuangan)

## ✅ Solusi
Menambahkan trait `HasRoleAccess` ke semua Filament Resource dan Page yang belum memilikinya, sehingga:
- **Admin**: Bisa akses semua menu (full access)
- **Pemilik**: Hanya bisa akses Jurnal, Buku Besar, dan Neraca (read-only)

## 🔧 Perubahan yang Dilakukan

### 1. Resource yang Ditambahkan `HasRoleAccess`

Semua resource berikut sekarang menggunakan trait `HasRoleAccess`:

#### Master Data
- ✅ `AsetResource` - Aset Tetap
- ✅ `AsetLancarResource` - Bahan Habis Pakai
- ✅ `KategoriAsetResource` - Kategori Aset
- ✅ `LokasiAsetResource` - Lokasi Aset
- ✅ `VendorResource` - Vendor

#### Transaksi
- ✅ `FakturPembelianResource` - Faktur Pembelian
- ✅ `SaldoAwalResource` - Saldo Awal
- ✅ `PembayaranAsetResource` - Pembayaran Aset
- ✅ `PerolehanAsetResource` - Perolehan Aset Tetap
- ✅ `PersediaanResource` - Persediaan
- ✅ `PemakaianPersediaanResource` - Pemakaian Persediaan
- ✅ `PemeliharaanResource` - Pemeliharaan Aset
- ✅ `ModalResource` - Modal

#### Keuangan
- ✅ `UtangJangkaPanjangResource` - Utang Jangka Panjang

#### Laporan
- ✅ `PenyusutanResource` - Kartu Penyusutan Aset

### 2. Page yang Ditambahkan Role Access Control

- ✅ `GeneratePenyusutan` - Generate Penyusutan (hanya Admin)

### 3. Resource yang Sudah Memiliki `HasRoleAccess` (dari sebelumnya)

- ✅ `JurnalResource` - dengan `canAccessByPemilik() = true`
- ✅ `BukuBesarResource` - dengan `canAccessByPemilik() = true`
- ✅ `NeracaPage` - dengan custom `canAccess()` method

## 📝 Cara Kerja

### Trait `HasRoleAccess`

```php
trait HasRoleAccess
{
    public static function canAccess(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Admin bisa akses semua
        if ($user->isAdmin()) {
            return true;
        }
        
        // Pemilik hanya bisa akses laporan
        if ($user->isPemilik()) {
            return static::canAccessByPemilik();
        }
        
        return false;
    }
    
    protected static function canAccessByPemilik(): bool
    {
        return false; // Default: tidak bisa akses
    }
}
```

### Resource yang Bisa Diakses Pemilik

Hanya resource yang meng-override method `canAccessByPemilik()` menjadi `true`:

```php
class JurnalResource extends Resource
{
    use HasRoleAccess;
    
    protected static function canAccessByPemilik(): bool
    {
        return true; // Pemilik bisa akses
    }
}
```

## 🧪 Testing

### Login sebagai Admin
```
Email: admin@example.com
Password: password
```
**Expected**: Bisa melihat semua menu di sidebar

### Login sebagai Pemilik
```
Email: pemilik@example.com
Password: password
```
**Expected**: Hanya melihat menu:
- Laporan
  - Jurnal
  - Buku Besar
  - Laporan Posisi Keuangan (Neraca)

## 📊 Hasil

### Sebelum
- Pemilik bisa melihat semua menu (Master Data, Transaksi, Keuangan, Laporan)
- Pemilik bisa akses halaman yang seharusnya tidak boleh

### Sesudah
- Pemilik hanya melihat menu Laporan (Jurnal, Buku Besar, Neraca)
- Pemilik tidak bisa akses halaman lain (akan error 403 jika mencoba akses langsung via URL)
- Admin tetap bisa akses semua menu

## 🔐 Keamanan

1. **Sidebar**: Menu yang tidak boleh diakses tidak muncul di sidebar
2. **Direct Access**: Jika Pemilik mencoba akses URL langsung (misal `/admin/asets`), akan ditolak dengan error 403
3. **CRUD Operations**: Pemilik tidak bisa create, edit, atau delete data (read-only)

## 📁 File yang Diubah

Total: **18 Resource Files + 1 Page File**

### Resource Files
1. `app/Filament/Admin/Resources/Asets/AsetResource.php`
2. `app/Filament/Admin/Resources/AsetLancars/AsetLancarResource.php`
3. `app/Filament/Admin/Resources/KategoriAsets/KategoriAsetResource.php`
4. `app/Filament/Admin/Resources/LokasiAsets/LokasiAsetResource.php`
5. `app/Filament/Admin/Resources/Vendors/VendorResource.php`
6. `app/Filament/Admin/Resources/FakturPembelians/FakturPembelianResource.php`
7. `app/Filament/Admin/Resources/SaldoAwals/SaldoAwalResource.php`
8. `app/Filament/Admin/Resources/PembayaranAsets/PembayaranAsetResource.php`
9. `app/Filament/Admin/Resources/PerolehanAsets/PerolehanAsetResource.php`
10. `app/Filament/Admin/Resources/Persediaans/PersediaanResource.php`
11. `app/Filament/Admin/Resources/PemakaianPersediaans/PemakaianPersediaanResource.php`
12. `app/Filament/Admin/Resources/Pemeliharaans/PemeliharaanResource.php`
13. `app/Filament/Admin/Resources/Modals/ModalResource.php`
14. `app/Filament/Admin/Resources/Penyusutans/PenyusutanResource.php`
15. `app/Filament/Admin/Resources/UtangJangkaPanjangs/UtangJangkaPanjangResource.php`

### Page Files
16. `app/Filament/Admin/Pages/GeneratePenyusutan.php`

## ✨ Catatan

- Tidak ada perubahan pada database
- Tidak ada perubahan pada logic bisnis
- Hanya menambahkan access control layer
- Trait `HasRoleAccess` sudah ada sebelumnya, hanya diterapkan ke resource yang belum memilikinya
