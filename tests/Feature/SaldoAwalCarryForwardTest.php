<?php

namespace Tests\Feature;

use App\Models\Pembelian;
use App\Models\Overhead;
use App\Models\OverheadDetail;
use App\Models\SaldoAwal;
use App\Models\BahanBaku;
use App\Models\Coa;
use App\Models\Supplier;
use App\Http\Controllers\BukuBesarController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaldoAwalCarryForwardTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new BukuBesarController();
    }

    /**
     * Test Carry Forward Balance untuk 4 periode berturut-turut
     * Memverifikasi bahwa Saldo Awal setiap periode = Saldo Akhir periode sebelumnya
     * 
     * Scenario:
     * - Akun: Kas Kecil (111)
     * - Saldo Awal Tabel: Rp 100.000.000 (1 Mei 2026)
     * - Periode: Mei, Juni, Juli, Agustus 2026
     */
    public function test_carry_forward_balance_mei_juni_juli_agustus()
    {
        // Setup: Buat COA dan Saldo Awal
        $coaKasKecil = Coa::create([
            'kode_akun' => '111',
            'nama_akun' => 'Kas Kecil',
            'jenis_akun' => 'Aktiva',
        ]);

        $coaBahanBaku = Coa::create([
            'kode_akun' => '552',
            'nama_akun' => 'Bahan Baku',
            'jenis_akun' => 'Aktiva',
        ]);

        // Saldo Awal Tabel (1 Mei 2026)
        SaldoAwal::create([
            'no_bukti' => 'SA-001',
            'tanggal' => '2026-05-01',
            'coa_id' => $coaKasKecil->id,
            'nominal' => 100000000, // Rp 100 juta
            'keterangan' => 'Saldo Awal',
        ]);

        // Setup: Data Supplier dan Bahan Baku
        $supplier = Supplier::create([
            'nama_supplier' => 'PT Supplier Test',
            'alamat' => 'Jl. Test',
            'no_telp' => '08123456789',
            'email' => 'test@supplier.com',
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Material Test',
            'kode_bahan' => 'MBK-001',
        ]);

        // ========== PERIODE MEI 2026 ==========
        // Transaksi Mei: Debit +50jt, Kredit -20jt
        Pembelian::create([
            'tanggal' => '2026-05-10',
            'supplier_id' => $supplier->id,
            'bahan_baku_id' => $bahanBaku->id,
            'qty' => 1,
            'harga' => 50000000, // Rp 50 juta
            'total' => 50000000,
            'subtotal' => 50000000,
            'diskon' => 20000000, // Rp 20 juta
            'ongkir' => 0,
            'total_bersih' => 30000000,
            'grand_total' => 30000000,
            'coa_id' => $coaKasKecil->id, // Pembayaran dari Kas Kecil
        ]);

        // Test Mei
        $saldoAwalMei = $this->controller->hitungSaldoAwal('111', '2026-05-01', '2026-05-31');
        $saldoAkhirMei = $this->controller->hitungSaldoAkhir('111', '2026-05-01', '2026-05-31');

        $this->assertEquals(100000000, $saldoAwalMei, 'Saldo Awal Mei seharusnya Rp 100 juta (dari tabel)');
        $this->assertEquals(130000000, $saldoAkhirMei, 'Saldo Akhir Mei seharusnya Rp 130 juta (100jt + 50jt - 20jt)');

        // ========== PERIODE JUNI 2026 ==========
        // Transaksi Juni: Debit +40jt, Kredit -10jt
        Pembelian::create([
            'tanggal' => '2026-06-15',
            'supplier_id' => $supplier->id,
            'bahan_baku_id' => $bahanBaku->id,
            'qty' => 1,
            'harga' => 40000000, // Rp 40 juta
            'total' => 40000000,
            'subtotal' => 40000000,
            'diskon' => 10000000, // Rp 10 juta
            'ongkir' => 0,
            'total_bersih' => 30000000,
            'grand_total' => 30000000,
            'coa_id' => $coaKasKecil->id,
        ]);

        // Test Juni
        $saldoAwalJuni = $this->controller->hitungSaldoAwal('111', '2026-06-01', '2026-06-30');
        $saldoAkhirJuni = $this->controller->hitungSaldoAkhir('111', '2026-06-01', '2026-06-30');

        $this->assertEquals(130000000, $saldoAwalJuni, 'Saldo Awal Juni seharusnya Rp 130 juta (dari Saldo Akhir Mei)');
        $this->assertEquals(160000000, $saldoAkhirJuni, 'Saldo Akhir Juni seharusnya Rp 160 juta (130jt + 40jt - 10jt)');

        // ========== PERIODE JULI 2026 ==========
        // Transaksi Juli: Debit +30jt, Kredit -5jt
        Pembelian::create([
            'tanggal' => '2026-07-20',
            'supplier_id' => $supplier->id,
            'bahan_baku_id' => $bahanBaku->id,
            'qty' => 1,
            'harga' => 30000000, // Rp 30 juta
            'total' => 30000000,
            'subtotal' => 30000000,
            'diskon' => 5000000, // Rp 5 juta
            'ongkir' => 0,
            'total_bersih' => 25000000,
            'grand_total' => 25000000,
            'coa_id' => $coaKasKecil->id,
        ]);

        // Test Juli
        $saldoAwalJuli = $this->controller->hitungSaldoAwal('111', '2026-07-01', '2026-07-31');
        $saldoAkhirJuli = $this->controller->hitungSaldoAkhir('111', '2026-07-01', '2026-07-31');

        $this->assertEquals(160000000, $saldoAwalJuli, 'Saldo Awal Juli seharusnya Rp 160 juta (dari Saldo Akhir Juni)');
        $this->assertEquals(185000000, $saldoAkhirJuli, 'Saldo Akhir Juli seharusnya Rp 185 juta (160jt + 30jt - 5jt)');

        // ========== PERIODE AGUSTUS 2026 ==========
        // Transaksi Agustus: Debit +20jt, Kredit -8jt
        Pembelian::create([
            'tanggal' => '2026-08-25',
            'supplier_id' => $supplier->id,
            'bahan_baku_id' => $bahanBaku->id,
            'qty' => 1,
            'harga' => 20000000, // Rp 20 juta
            'total' => 20000000,
            'subtotal' => 20000000,
            'diskon' => 8000000, // Rp 8 juta
            'ongkir' => 0,
            'total_bersih' => 12000000,
            'grand_total' => 12000000,
            'coa_id' => $coaKasKecil->id,
        ]);

        // Test Agustus
        $saldoAwalAgustus = $this->controller->hitungSaldoAwal('111', '2026-08-01', '2026-08-31');
        $saldoAkhirAgustus = $this->controller->hitungSaldoAkhir('111', '2026-08-01', '2026-08-31');

        $this->assertEquals(185000000, $saldoAwalAgustus, 'Saldo Awal Agustus seharusnya Rp 185 juta (dari Saldo Akhir Juli)');
        $this->assertEquals(197000000, $saldoAkhirAgustus, 'Saldo Akhir Agustus seharusnya Rp 197 juta (185jt + 20jt - 8jt)');

        // ========== VERIFIKASI CARRY FORWARD ==========
        // Verifikasi bahwa Saldo Awal setiap periode = Saldo Akhir periode sebelumnya
        $this->assertEquals($saldoAkhirMei, $saldoAwalJuni, 'Saldo Awal Juni harus sama dengan Saldo Akhir Mei ✓');
        $this->assertEquals($saldoAkhirJuni, $saldoAwalJuli, 'Saldo Awal Juli harus sama dengan Saldo Akhir Juni ✓');
        $this->assertEquals($saldoAkhirJuli, $saldoAwalAgustus, 'Saldo Awal Agustus harus sama dengan Saldo Akhir Juli ✓');
    }

    /**
     * Test: Periode pertama tanpa data saldo awal di tabel
     * Seharusnya return 0 sebagai base case
     */
    public function test_periode_pertama_tanpa_data_saldo_awal()
    {
        // Tidak ada data SaldoAwal di tabel
        $saldoAwal = $this->controller->hitungSaldoAwal('999', '2026-05-01', '2026-05-31');

        $this->assertEquals(0, $saldoAwal, 'Jika tidak ada data saldo awal dan tidak ada transaksi, return 0');
    }

    /**
     * Test: Periode tanpa transaksi
     * Jika tidak ada transaksi di periode tersebut, saldo awal harus tetap dari tabel
     */
    public function test_periode_tanpa_transaksi_ambil_dari_tabel()
    {
        // Setup COA dan Saldo Awal
        $coaKasKecil = Coa::create([
            'kode_akun' => '111',
            'nama_akun' => 'Kas Kecil',
            'jenis_akun' => 'Aktiva',
        ]);

        SaldoAwal::create([
            'no_bukti' => 'SA-001',
            'tanggal' => '2026-05-01',
            'coa_id' => $coaKasKecil->id,
            'nominal' => 100000000,
            'keterangan' => 'Saldo Awal',
        ]);

        // Test Periode Mei (punya saldo awal tapi tidak ada transaksi)
        $saldoAwalMei = $this->controller->hitungSaldoAwal('111', '2026-05-01', '2026-05-31');
        $saldoAkhirMei = $this->controller->hitungSaldoAkhir('111', '2026-05-01', '2026-05-31');

        $this->assertEquals(100000000, $saldoAwalMei, 'Saldo Awal dari tabel');
        $this->assertEquals(100000000, $saldoAkhirMei, 'Saldo Akhir sama dengan Saldo Awal (tidak ada transaksi)');

        // Test Periode Juni (tidak ada transaksi di Mei, jadi ambil dari tabel)
        $saldoAwalJuni = $this->controller->hitungSaldoAwal('111', '2026-06-01', '2026-06-30');
        $saldoAkhirJuni = $this->controller->hitungSaldoAkhir('111', '2026-06-01', '2026-06-30');

        $this->assertEquals(100000000, $saldoAwalJuni, 'Saldo Awal Juni juga dari tabel (tidak ada transaksi Mei)');
        $this->assertEquals(100000000, $saldoAkhirJuni, 'Saldo Akhir Juni tetap sama (tidak ada transaksi)');
    }
}
