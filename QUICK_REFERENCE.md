# 🎯 Quick Reference Card

## 📊 Saldo Normal Akun

| Akun | Bertambah | Berkurang | Saldo Normal | Rumus |
|------|-----------|-----------|--------------|-------|
| **Aset** | Debit | Kredit | Debit | `D - K` |
| **Kewajiban** | Kredit | Debit | Kredit | `K - D` |
| **Modal** | Kredit | Debit | Kredit | `K - D` |
| **Pendapatan** | Kredit | Debit | Kredit | `K - D` |
| **Beban** | Debit | Kredit | Debit | `D - K` |

---

## 💰 Jurnal Utang Jangka Panjang

### Terima Pinjaman (Kas)
```
Debit:  Kas                 Rp xxx
Kredit: Utang Jk Panjang    Rp xxx
```

### Terima Pinjaman (Aset)
```
Debit:  Kendaraan           Rp xxx
Kredit: Utang Jk Panjang    Rp xxx
```

### Bayar Cicilan
```
Debit:  Utang Jk Panjang    Rp xxx
Kredit: Kas                 Rp xxx
```

---

## ⚠️ Indikator Neraca

| Simbol | Arti | Tindakan |
|--------|------|----------|
| ✅ Hijau | Balance | OK |
| ❌ Merah | Tidak Balance | Cek jurnal |
| ⚠️ Warning | Saldo Abnormal | Perbaiki jurnal |

---

## 🔍 Troubleshooting Cepat

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| Utang negatif | Jurnal terbalik | Kredit utang, bukan debit |
| Kas negatif | Overdraft | Catat sebagai utang bank |
| Tidak balance | Jurnal salah | Cek D = K di setiap jurnal |
| Akm Penyusutan positif | Jurnal terbalik | Kredit akm penyusutan |

---

## 📍 Menu Navigasi

```
Keuangan
└── Utang Jangka Panjang

Laporan
├── Jurnal
├── Buku Besar
└── Laporan Posisi Keuangan (Neraca)
```

---

## 🧪 Testing Checklist

- [ ] Buat utang → cek jurnal
- [ ] Cek neraca → utang positif?
- [ ] Ada ⚠️? → perbaiki jurnal
- [ ] Hapus utang → jurnal ikut terhapus?
- [ ] Neraca balance?

---

## 📚 Dokumentasi

| Topik | File |
|-------|------|
| Utang Jangka Panjang | `UTANG_JANGKA_PANJANG_GUIDE.md` |
| Perbaikan Neraca | `PERBAIKAN_NERACA_CHANGELOG.md` |
| Saldo Normal | `NERACA_SALDO_NORMAL_GUIDE.md` |
| Troubleshooting | `TROUBLESHOOTING_NERACA.md` |
| Summary | `SUMMARY_PERBAIKAN_NERACA.md` |

---

## 🆘 Emergency

```bash
# Backup database
php artisan backup:run

# Clear cache
php artisan cache:clear

# Check log
tail -f storage/logs/laravel.log
```

---

**Print & Tempel di Meja!** 📌
