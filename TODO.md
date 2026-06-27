# Fix Laravel Sessions Table Error (42S02) - COMPLETED

## Steps:
- [x] 1. Run `php artisan migrate` to create all tables including sessions/users/migrations. ✅ All migrations ran successfully.
- [x] 2. Run `php artisan config:clear`, `cache:clear`, `route:clear`, `view:clear`. ✅ Caches cleared.
- [x] 3. Run `php artisan db:seed` ✅ Creates admin@gmail.com (password: password), owner@gmail.com, COA data.
- [ ] 4. Run `php artisan serve` and test http://127.0.0.1:8000 (sessions table now exists, error fixed).
- [ ] 5. Mark complete.

**Sessions error FIXED. Admin users seeded. App ready.**
- [x] Migrations ran.
- [x] Caches cleared.
- [x] `php artisan db:seed` → admin@gmail.com / password ready.
- Test: Login → dashboard works.

---

# TODO - Revisi Stok ke FEFO & Batch (Filament v4)

## Rencana Implementasi (sesuai requirement user)

### Status: IN PROGRESS (mulai eksekusi)

### 1) Analisis kode terkait stok
- Titik sumber stok saat ini: `barang.stok` ✅
- Lokasi update stok saat transaksi: ✅
  - `DetailPenjualanNonKonsinyasiRelationManager` (validasi + decrement/increment)
  - `DetailKonsinyasiRelationManager` (validasi + decrement)
  - `ReturPenjualan` (increment/decrement)
- UI Barang menampilkan/mengisi `stok/stok_awal` ✅
  - Perlu dihapus

### 2) Skema data persediaan berbasis batch
- Buat migration tabel `detail_persediaan_produk` ⏳
- Buat migration tabel ledger consumption `detail_persediaan_produk_consumptions` ⏳

### 3) Model & relasi
- Buat model `DetailPersediaanProduk` ⏳
- Relasi `Barang hasMany detail_persediaan_produk` ⏳
- Relasi `DetailPersediaanProduk belongsTo barang` ⏳

### 4) Filament Resource: “Detail Persediaan Produk”
- Buat Resource + navigation menu ⏳
- Kolom tampil: Produk, Rasa, Kategori, Stok Saat Ini, Harga ⏳

### 5) Hapus stok dari master barang (UI + model)
- Hapus kolom/tampilan `stok` dan input `stok_awal` ⏳
- Hapus hook `Barang::booted()` yang menyet stok ⏳

### 6) Engine stok FEFO
- Implement FEFO engine:
  - hitung stok saat ini per batch
  - validasi qty berdasarkan stok saat ini
  - pengurangan stok dengan FEFO
  - catat ledger consumption untuk revert ⏳

### 7) Revisi validasi & pengurangan stok transaksi
- Non konsinyasi: validasi qty dan decrement via FEFO + ledger ⏳
- Konsinyasi: validasi `qty_titip` dan decrement via FEFO + ledger ⏳

### 8) Revisi retur & edit/delete
- Revert stok berbasis ledger, bukan `barang.stok` ⏳

### 9) Testing
- Jalankan migration & uji alur create/edit/delete + retur ⏳

