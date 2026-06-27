<?php

$outputPath = __DIR__ . DIRECTORY_SEPARATOR . 'BAB_4_2_Pengujian_Revisi_Format_PNG.docx';

function x(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
}

function paragraph(string $text, array $options = []): string
{
    $align = $options['align'] ?? null;
    $bold = !empty($options['bold']);
    $size = (int)($options['size'] ?? 22);
    $before = (int)($options['before'] ?? 0);
    $after = (int)($options['after'] ?? 120);

    $jc = $align ? '<w:jc w:val="' . x($align) . '"/>' : '';
    $rPr = '<w:rPr><w:rFonts w:ascii="Aptos" w:hAnsi="Aptos" w:eastAsia="Aptos" w:cs="Aptos"/><w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/>' . ($bold ? '<w:b/><w:bCs/>' : '') . '</w:rPr>';

    return '<w:p><w:pPr><w:spacing w:before="' . $before . '" w:after="' . $after . '" w:line="276" w:lineRule="auto"/>' . $jc . '</w:pPr><w:r>' . $rPr . '<w:t xml:space="preserve">' . x($text) . '</w:t></w:r></w:p>';
}

function tableCell(string $text, bool $bold = false): string
{
    return '<w:tc><w:tcPr><w:tcW w:w="2400" w:type="dxa"/><w:tcMar><w:top w:w="80" w:type="dxa"/><w:left w:w="80" w:type="dxa"/><w:bottom w:w="80" w:type="dxa"/><w:right w:w="80" w:type="dxa"/></w:tcMar></w:tcPr>' . paragraph($text, ['bold' => $bold, 'after' => 0]) . '</w:tc>';
}

function table(array $headers, array $rows): string
{
    $xml = '<w:tbl><w:tblPr><w:tblStyle w:val="TableGrid"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="000000"/><w:left w:val="single" w:sz="4" w:space="0" w:color="000000"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="000000"/><w:right w:val="single" w:sz="4" w:space="0" w:color="000000"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="000000"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="000000"/></w:tblBorders></w:tblPr><w:tblGrid>';
    foreach ($headers as $_) {
        $xml .= '<w:gridCol w:w="2400"/>';
    }
    $xml .= '</w:tblGrid><w:tr>';
    foreach ($headers as $header) {
        $xml .= tableCell($header, true);
    }
    $xml .= '</w:tr>';
    foreach ($rows as $row) {
        $xml .= '<w:tr>';
        foreach ($row as $cell) {
            $xml .= tableCell($cell);
        }
        $xml .= '</w:tr>';
    }
    return $xml . '</w:tbl>' . paragraph('', ['after' => 0]);
}

function blackboxTable(string $title, array $rows): string
{
    return paragraph($title, ['bold' => true])
        . table(['Test No', 'Test Case', 'Input Data', 'Result Expected', 'Actual', 'Status'], $rows);
}

$body = '';
$body .= paragraph('4.2 Pengujian', ['bold' => true, 'size' => 24]);
$body .= paragraph('Pengujian dilakukan untuk mengetahui apakah aplikasi biaya produksi dapat memproses transaksi produksi dari awal sampai menghasilkan laporan biaya produksi. Pengujian ini menggunakan studi kasus produksi Kue Sus Kulit dengan nomor Job Order JOB-20260514-001. Alur pengujian dimulai dari pembuatan job order, pencatatan pemakaian bahan baku, pencatatan biaya tenaga kerja, pencatatan biaya overhead pabrik, penyelesaian produksi, sampai aplikasi menghasilkan laporan biaya produksi.');
$body .= paragraph('Pengujian dibagi menjadi tiga bagian, yaitu pengujian manual, pengujian aplikasi, dan pengujian blackbox. Pengujian manual digunakan untuk menghitung biaya produksi berdasarkan soal transaksi. Pengujian aplikasi digunakan untuk membandingkan hasil perhitungan manual dengan hasil yang ditampilkan aplikasi. Pengujian blackbox digunakan untuk mengetahui apakah fitur aplikasi berjalan sesuai input dan output yang diharapkan.');

$body .= paragraph('4.2.1 Pengujian Manual', ['bold' => true, 'size' => 24]);
$body .= paragraph('Pada bagian ini dibuat soal pengujian berdasarkan transaksi produksi yang dimulai dari awal proses produksi. Soal ini digunakan sebagai dasar untuk menghitung biaya produksi secara manual sebelum dibandingkan dengan hasil perhitungan pada aplikasi.');
$body .= paragraph('Soal Pengujian', ['bold' => true]);
$body .= paragraph('Pada tanggal 14 Mei 2026, bagian produksi membuat Job Order JOB-20260514-001 untuk memproduksi Kue Sus Kulit sebanyak 15 unit dengan jumlah 2 batch. Produksi selesai pada tanggal 20 Mei 2026. Selama proses produksi, perusahaan menggunakan bahan baku, tenaga kerja, dan biaya overhead pabrik sebagai berikut.');
$body .= paragraph('Data pemakaian bahan baku:', ['bold' => true]);
$body .= table(
    ['No', 'Nama Bahan', 'Jumlah Pakai', 'Harga Satuan', 'Total Biaya'],
    [
        ['1', 'Air', '7,50 Liter', 'Rp16.583,33', 'Rp124.374,98'],
        ['2', 'Baking Powder', '1.500 Gram', 'Rp46,67', 'Rp70.005'],
        ['3', 'Telur', '45 Butir', 'Rp920', 'Rp41.400'],
        ['4', 'Mentega', '1.500 Gram', 'Rp30', 'Rp45.000'],
        ['5', 'Tepung Terigu', '1.875 Gram', 'Rp12,80', 'Rp24.000'],
        ['6', 'Garam', '5,85 Ml', 'Rp20', 'Rp117'],
    ]
);
$body .= paragraph('Data biaya tenaga kerja:', ['bold' => true]);
$body .= table(
    ['No', 'Nama Tenaga Kerja', 'Jenis Tenaga', 'Keterangan', 'Total Biaya'],
    [
        ['1', 'Ahmad', 'Langsung', 'Operator Produksi', 'Rp48.000'],
        ['2', 'Srii', 'Langsung', 'Operator Filling', 'Rp48.000'],
        ['3', 'Ahmad', 'Langsung', 'Operator Produksi', 'Rp30.000'],
        ['4', 'Budi', 'Tidak Langsung', 'Bagian Gudang', 'Rp48.000'],
        ['5', 'King', 'Tidak Langsung', 'Admin', 'Rp48.000'],
    ]
);
$body .= paragraph('Data biaya overhead pabrik:', ['bold' => true]);
$body .= table(
    ['No', 'Jenis Overhead', 'Tanggal', 'Satuan Periode', 'Total Biaya'],
    [
        ['1', 'Listrik', '20 Mei 2026', 'Per bulan', 'Rp10.526'],
        ['2', 'Air', '20 Mei 2026', 'Per bulan', 'Rp9.474'],
        ['3', 'Gas', '20 Mei 2026', 'Per hari', 'Rp16.667'],
    ]
);
$body .= paragraph('Berdasarkan data tersebut, hitunglah total biaya bahan baku langsung, biaya tenaga kerja langsung, biaya overhead pabrik, total biaya produksi, dan harga pokok produksi per unit.');
$body .= paragraph('Jawaban Perhitungan Manual', ['bold' => true]);
$body .= paragraph('1. Biaya bahan baku langsung');
$body .= paragraph('Biaya bahan baku langsung = Rp124.374,98 + Rp70.005 + Rp41.400 + Rp45.000 + Rp24.000 + Rp117');
$body .= paragraph('Biaya bahan baku langsung = Rp304.896,98');
$body .= paragraph('2. Biaya tenaga kerja langsung');
$body .= paragraph('Biaya tenaga kerja langsung = Rp48.000 + Rp48.000 + Rp30.000');
$body .= paragraph('Biaya tenaga kerja langsung = Rp126.000');
$body .= paragraph('3. Biaya overhead pabrik');
$body .= paragraph('BOP umum = Rp10.526 + Rp9.474 + Rp16.667');
$body .= paragraph('BOP umum = Rp36.667');
$body .= paragraph('BTK tidak langsung = Rp48.000 + Rp48.000');
$body .= paragraph('BTK tidak langsung = Rp96.000');
$body .= paragraph('Total overhead pabrik = BOP umum + BTK tidak langsung');
$body .= paragraph('Total overhead pabrik = Rp36.667 + Rp96.000');
$body .= paragraph('Total overhead pabrik = Rp132.667');
$body .= paragraph('4. Total biaya produksi');
$body .= paragraph('Total Biaya Produksi = BBB Langsung + BTK Langsung + BOP');
$body .= paragraph('Total Biaya Produksi = Rp304.896,98 + Rp126.000 + Rp132.667');
$body .= paragraph('Total Biaya Produksi = Rp563.563,98');
$body .= paragraph('5. Harga pokok produksi per unit');
$body .= paragraph('HPP per Unit = Total Biaya Produksi / Jumlah Produksi');
$body .= paragraph('HPP per Unit = Rp563.563,98 / 15');
$body .= paragraph('HPP per Unit = Rp37.570,93');
$body .= table(
    ['Komponen Biaya', 'Total'],
    [
        ['Biaya bahan baku langsung', 'Rp304.896,98'],
        ['Biaya tenaga kerja langsung', 'Rp126.000'],
        ['Biaya overhead pabrik', 'Rp132.667'],
        ['Total biaya produksi', 'Rp563.563,98'],
        ['Harga pokok produksi per unit', 'Rp37.570,93'],
    ]
);

$body .= paragraph('4.2.2 Pengujian Aplikasi', ['bold' => true, 'size' => 24]);
$body .= paragraph('Pengujian aplikasi dilakukan dengan memasukkan data berdasarkan soal pengujian manual ke dalam aplikasi. Proses pengujian dilakukan dari awal transaksi produksi sampai laporan biaya produksi terbentuk. Pada bagian ini, gambar atau screenshot yang perlu dicantumkan adalah tampilan yang membuktikan bahwa data telah dimasukkan dan hasil perhitungan aplikasi sudah sesuai. Setiap screenshot disimpan menggunakan format PNG agar gambar yang dimasukkan ke dokumen tetap jelas.');
$body .= paragraph('Daftar screenshot dalam format PNG yang perlu diambil pada aplikasi adalah sebagai berikut.');
$body .= table(
    ['No', 'Nama File PNG', 'Tahap Pengujian Aplikasi', 'Menu atau Tampilan yang Di-screenshot', 'Data yang Harus Terlihat'],
    [
        ['1', 'Gambar_4_1_Login.png', 'Login ke aplikasi', 'Halaman login', 'Form username dan password terlihat'],
        ['2', 'Gambar_4_2_Dashboard.png', 'Masuk ke dashboard', 'Dashboard setelah login berhasil', 'Admin berhasil masuk ke sistem'],
        ['3', 'Gambar_4_3_Buat_Job_Order.png', 'Membuat job order produksi', 'Menu Job Order - Buat Job Order Baru', 'Nomor job otomatis, tanggal mulai, produk, jumlah produksi, jumlah batch, kapasitas batch per hari, jenis produksi, tujuan produksi, tahap produksi, dan customer/pemesan'],
        ['4', 'Gambar_4_4_Detail_Job_Order.png', 'Melihat detail job order', 'Detail Permintaan Produksi', 'Data Job Order JOB-20260514-001, produk Kue Sus Kulit, jumlah produksi 15 unit, dan status produksi terlihat'],
        ['5', 'Gambar_4_5_Pemakaian_Bahan_Baku.png', 'Mencatat pemakaian bahan baku', 'Menu Transaksi - Pemakaian Bahan Baku', 'Daftar bahan Air, Baking Powder, Telur, Mentega, Tepung Terigu, dan Garam beserta jumlah pakai dan total biaya'],
        ['6', 'Gambar_4_6_Biaya_Tenaga_Kerja.png', 'Mencatat biaya tenaga kerja', 'Menu Transaksi - Biaya Tenaga Kerja', 'Data Ahmad, Srii, Budi, dan King, jenis tenaga kerja, serta total biaya masing-masing'],
        ['7', 'Gambar_4_7_Biaya_Overhead.png', 'Mencatat biaya overhead pabrik', 'Menu Transaksi - Biaya Overhead Pabrik', 'Biaya Listrik, Air, dan Gas dengan total masing-masing'],
        ['8', 'Gambar_4_8_Job_Order_Selesai.png', 'Menyelesaikan job order', 'Detail Permintaan Produksi atau tombol Selesaikan Produksi', 'Status job order berubah menjadi selesai'],
        ['9', 'Gambar_4_9_Daftar_Laporan_Biaya_Produksi.png', 'Menampilkan daftar laporan biaya produksi', 'Menu Laporan - Laporan Biaya Produksi', 'Job Order JOB-20260514-001 muncul pada daftar laporan'],
        ['10', 'Gambar_4_10_Detail_Laporan_Biaya_Produksi.png', 'Melihat detail laporan biaya produksi', 'Detail Laporan Biaya Produksi', 'Biaya bahan baku Rp304.896,98, biaya tenaga kerja Rp126.000, overhead Rp132.667, total Rp563.563,98, HPP per unit Rp37.570,93'],
        ['11', 'Gambar_4_11_Export_PDF.png', 'Mencetak atau export laporan', 'Tampilan Kartu Biaya atau Export PDF', 'Laporan dapat ditampilkan atau diekspor dalam bentuk PDF'],
    ]
);
$body .= paragraph('Screenshot halaman Buat Job Order Baru seperti pada gambar pengujian dapat digunakan sebagai Gambar_4_3_Buat_Job_Order.png. Setelah data diisi dan disimpan, ambil screenshot detail job order sebagai bukti bahwa data berhasil tersimpan.');
$body .= paragraph('Hasil pengujian aplikasi kemudian dibandingkan dengan hasil pengujian manual sebagai berikut.');
$body .= table(
    ['Data yang Diuji', 'Hasil Perhitungan Manual', 'Hasil Aplikasi', 'Keterangan'],
    [
        ['Biaya bahan baku', 'Rp304.896,98', 'Rp304.896,98', 'Sesuai'],
        ['Biaya tenaga kerja langsung', 'Rp126.000', 'Rp126.000', 'Sesuai'],
        ['Biaya overhead pabrik', 'Rp132.667', 'Rp132.667', 'Sesuai'],
        ['Total biaya produksi', 'Rp563.563,98', 'Rp563.563,98', 'Sesuai'],
        ['HPP per unit', 'Rp37.570,93', 'Rp37.570,93', 'Sesuai'],
    ]
);
$body .= paragraph('Berdasarkan tabel perbandingan tersebut, hasil perhitungan aplikasi sama dengan hasil perhitungan manual. Hal ini menunjukkan bahwa aplikasi dapat memproses transaksi produksi dari awal sampai menghasilkan laporan biaya produksi dengan benar.');

$body .= paragraph('4.2.3 Pengujian Black Box', ['bold' => true, 'size' => 24]);
$body .= paragraph('Pengujian black box dilakukan untuk mengevaluasi fungsi aplikasi tanpa melihat struktur kode program. Pengujian ini difokuskan pada proses input dan output dari fitur utama aplikasi biaya produksi, yaitu master data, transaksi produksi, jurnal umum, dan laporan biaya produksi.');

$body .= blackboxTable('Tabel 4.28 Pengujian Black Box Login', [
    ['1', 'Login dengan data valid', 'Memasukkan username dan password admin yang benar', 'Sistem menerima input dan menampilkan dashboard admin', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Login dengan password salah', 'Memasukkan username benar dan password salah', 'Sistem menolak login dan menampilkan pesan kesalahan', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Login dengan kolom kosong', 'Username atau password tidak diisi', 'Sistem menampilkan pesan validasi bahwa data wajib diisi', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.29 Pengujian Black Box Master Data Bahan Baku', [
    ['1', 'Menambah bahan baku dengan data valid', 'Kode bahan, nama bahan, satuan, stok minimum, jenis bahan, dan status aktif diisi dengan benar', 'Sistem menyimpan data bahan baku dan menampilkan data pada daftar bahan baku', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambah bahan baku dengan kode yang sudah ada', 'Kode bahan diisi sama dengan kode bahan yang telah tersimpan', 'Sistem menolak input dan menampilkan pesan validasi kode bahan sudah digunakan', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menambah bahan baku tanpa nama bahan', 'Nama bahan dikosongkan', 'Sistem menampilkan pesan validasi bahwa nama bahan wajib diisi', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.30 Pengujian Black Box Master Data Produk', [
    ['1', 'Menambah produk dengan data valid', 'Kode produk, nama produk Kue Sus Kulit, tipe produk, satuan produk, dan status aktif diisi dengan benar', 'Sistem menyimpan data produk dan menampilkan produk pada daftar produk', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambah produk dengan kode duplikat', 'Kode produk diisi sama dengan kode produk yang sudah ada', 'Sistem menolak input dan menampilkan pesan bahwa kode produk sudah digunakan', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menambah produk tanpa satuan', 'Satuan produk dikosongkan', 'Sistem menampilkan pesan validasi bahwa satuan produk wajib diisi', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.31 Pengujian Black Box Master Data Tenaga Kerja', [
    ['1', 'Menambah tenaga kerja dengan data valid', 'Nama tenaga kerja, jabatan, jenis tenaga kerja, dan status aktif diisi dengan benar', 'Sistem menyimpan data tenaga kerja dan menampilkan data pada daftar tenaga kerja', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambah tenaga kerja tanpa nama', 'Nama tenaga kerja dikosongkan', 'Sistem menampilkan pesan validasi bahwa nama tenaga kerja wajib diisi', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menambah tenaga kerja tanpa jenis tenaga', 'Jenis tenaga kerja tidak dipilih', 'Sistem menampilkan pesan validasi bahwa jenis tenaga kerja wajib dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.32 Pengujian Black Box Master Data Akun', [
    ['1', 'Menambahkan akun COA dengan data valid', 'Nomor akun, nama akun, kategori akun, dan saldo awal diisi dengan benar', 'Sistem menyimpan akun dan menampilkan akun pada daftar COA', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambah akun dengan nomor duplikat', 'Nomor akun diisi dengan nomor akun yang sudah tersimpan', 'Sistem menolak input dan menampilkan pesan validasi nomor akun sudah digunakan', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menambahkan akun tanpa nama akun', 'Nama akun dikosongkan', 'Sistem menampilkan pesan validasi bahwa nama akun wajib diisi', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.33 Pengujian Black Box Transaksi Permintaan Produksi', [
    ['1', 'Membuka halaman tambah job order produksi', 'Klik menu Transaksi, pilih Job Order, lalu klik tambah data', 'Sistem menampilkan halaman Buat Job Order Baru dengan nomor job order otomatis', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambahkan job order produksi dengan data valid', 'Nomor Job Order otomatis, tanggal mulai 14 Mei 2026, produk Kue Sus Kulit, jumlah produksi 15 unit, jumlah batch 2, kapasitas batch per hari 2, jenis produksi Brand Sendiri, tujuan produksi Stok Barang Jadi, tahap produksi Persiapan, customer/pemesan diisi sesuai kebutuhan', 'Sistem menyimpan job order produksi dan menampilkan data pada daftar permintaan produksi', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menyimpan data job order', 'Klik button simpan setelah seluruh data wajib diisi', 'Sistem menyimpan data job order dan menampilkan notifikasi bahwa data berhasil disimpan', 'Sesuai yang diharapkan', 'Berhasil'],
    ['4', 'Menambahkan job order tanpa memilih produk', 'Produk tidak dipilih', 'Sistem menampilkan pesan validasi bahwa produk wajib dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['5', 'Menambahkan job order dengan jumlah produksi kosong atau 0', 'Jumlah produksi tidak diisi atau bernilai 0', 'Sistem menolak input dan menampilkan pesan validasi jumlah produksi', 'Sesuai yang diharapkan', 'Berhasil'],
    ['6', 'Menambahkan job order tanpa memilih jenis produksi', 'Jenis produksi dikosongkan', 'Sistem menampilkan pesan validasi bahwa jenis produksi wajib dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['7', 'Menambahkan job order tanpa memilih tujuan produksi', 'Tujuan produksi dikosongkan', 'Sistem menampilkan pesan validasi bahwa tujuan produksi wajib dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['8', 'Menyelesaikan job order produksi', 'Klik tombol selesaikan pada Job Order JOB-20260514-001', 'Sistem mengubah status job order menjadi selesai dan menghitung total biaya produksi', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.34 Pengujian Black Box Transaksi Pemakaian Bahan Baku', [
    ['1', 'Menambahkan pemakaian bahan baku dengan data valid', 'Job Order JOB-20260514-001, bahan baku, tanggal pemakaian, jumlah pakai, dan stok FIFO dipilih dengan benar', 'Sistem menyimpan pemakaian bahan baku dan menghitung total biaya bahan baku', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambahkan pemakaian bahan baku melebihi stok', 'Jumlah pakai lebih besar dari stok tersedia', 'Sistem menolak input dan menampilkan pesan bahwa stok tidak mencukupi', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menambahkan pemakaian bahan baku tanpa memilih job order', 'Job order dikosongkan', 'Sistem menampilkan pesan validasi bahwa job order wajib dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['4', 'Menambahkan pemakaian bahan baku dengan jumlah kosong atau 0', 'Jumlah pakai tidak diisi atau bernilai 0', 'Sistem menampilkan pesan validasi jumlah pakai', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.35 Pengujian Black Box Transaksi Biaya Tenaga Kerja', [
    ['1', 'Menambahkan biaya tenaga kerja langsung dengan data valid', 'Job Order JOB-20260514-001, tenaga kerja Ahmad atau Srii, tanggal kerja, hari kerja, jumlah batch, dan upah diisi dengan benar', 'Sistem menyimpan biaya tenaga kerja dan menghitung biaya tenaga kerja langsung', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambahkan biaya tenaga kerja tidak langsung dengan data valid', 'Job Order JOB-20260514-001, tenaga kerja Budi atau King, tanggal kerja, hari kerja, jumlah batch, dan upah diisi dengan benar', 'Sistem menyimpan biaya tenaga kerja tidak langsung sebagai bagian dari overhead pabrik', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menambahkan biaya tenaga kerja tanpa memilih tenaga kerja', 'Tenaga kerja dikosongkan', 'Sistem menampilkan pesan validasi bahwa tenaga kerja wajib dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['4', 'Menambahkan biaya tenaga kerja dengan upah kosong', 'Upah tidak diisi', 'Sistem menampilkan pesan validasi bahwa upah wajib diisi', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.36 Pengujian Black Box Transaksi Biaya Overhead Pabrik', [
    ['1', 'Menambahkan biaya overhead dengan data valid', 'Job Order JOB-20260514-001, jenis overhead Listrik, tanggal overhead, satuan periode, jumlah batch, dan nominal diisi dengan benar', 'Sistem menyimpan biaya overhead pabrik', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menambahkan biaya overhead tanpa memilih job order', 'Job order dikosongkan', 'Sistem menampilkan pesan validasi bahwa job order wajib dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menambahkan biaya overhead tanpa nominal', 'Nominal biaya overhead dikosongkan', 'Sistem menampilkan pesan validasi bahwa nominal wajib diisi', 'Sesuai yang diharapkan', 'Berhasil'],
    ['4', 'Menambahkan biaya overhead dengan nominal 0', 'Nominal diisi 0', 'Sistem menolak input karena nominal biaya harus lebih dari 0', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.37 Pengujian Black Box Laporan Biaya Produksi', [
    ['1', 'Menampilkan laporan biaya produksi', 'Membuka menu Laporan Biaya Produksi', 'Sistem menampilkan daftar job order yang telah selesai', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menampilkan detail laporan biaya produksi', 'Memilih Job Order JOB-20260514-001', 'Sistem menampilkan detail bahan baku, tenaga kerja, overhead, total biaya produksi, dan HPP per unit', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Melakukan filter laporan berdasarkan tanggal', 'Tanggal mulai dan tanggal akhir dipilih', 'Sistem menampilkan laporan sesuai periode tanggal yang dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['4', 'Mengekspor laporan biaya produksi', 'Klik tombol Export PDF atau Kartu Biaya', 'Sistem menghasilkan laporan dalam bentuk PDF atau kartu biaya', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= blackboxTable('Tabel 4.38 Pengujian Black Box Jurnal Umum dan Buku Besar', [
    ['1', 'Menampilkan jurnal umum', 'Membuka menu Jurnal Umum', 'Sistem menampilkan daftar jurnal umum berdasarkan transaksi produksi yang telah terjadi', 'Sesuai yang diharapkan', 'Berhasil'],
    ['2', 'Menampilkan jurnal umum berdasarkan periode', 'Tanggal mulai dan tanggal selesai dipilih', 'Sistem menampilkan jurnal umum sesuai periode tanggal yang dipilih', 'Sesuai yang diharapkan', 'Berhasil'],
    ['3', 'Menampilkan buku besar', 'Memilih akun COA dan periode', 'Sistem menampilkan mutasi akun sesuai jurnal yang terbentuk', 'Sesuai yang diharapkan', 'Berhasil'],
    ['4', 'Menampilkan neraca saldo', 'Membuka menu Neraca Saldo atau memilih periode laporan', 'Sistem menampilkan saldo akun berdasarkan transaksi yang telah dicatat', 'Sesuai yang diharapkan', 'Berhasil'],
]);

$body .= paragraph('Berdasarkan hasil pengujian black box, setiap fungsi utama aplikasi dapat berjalan sesuai dengan hasil yang diharapkan. Aplikasi dapat menerima input, memproses transaksi produksi, menampilkan jurnal, serta menghasilkan laporan biaya produksi.');

$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape" mc:Ignorable="w14 wp14"><w:body>'
    . $body
    . '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/><w:cols w:space="708"/><w:docGrid w:linePitch="360"/></w:sectPr></w:body></w:document>';

$stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
    . '<w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Aptos" w:hAnsi="Aptos" w:eastAsia="Aptos" w:cs="Aptos"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr></w:rPrDefault><w:pPrDefault><w:pPr><w:spacing w:line="276" w:lineRule="auto"/></w:pPr></w:pPrDefault></w:docDefaults>'
    . '<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:qFormat/><w:rPr><w:rFonts w:ascii="Aptos" w:hAnsi="Aptos" w:eastAsia="Aptos" w:cs="Aptos"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:pPr><w:spacing w:line="276" w:lineRule="auto"/></w:pPr></w:style>'
    . '<w:style w:type="table" w:styleId="TableGrid"><w:name w:val="Table Grid"/><w:basedOn w:val="TableNormal"/><w:uiPriority w:val="59"/><w:tblPr><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tblBorders></w:tblPr></w:style>'
    . '</w:styles>';

$contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
    . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
    . '</Types>';

$relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
    . '</Relationships>';

$wordRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>';

$zip = new ZipArchive();
if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Tidak bisa membuat file: {$outputPath}\n");
    exit(1);
}

$zip->addFromString('[Content_Types].xml', $contentTypesXml);
$zip->addFromString('_rels/.rels', $relsXml);
$zip->addFromString('word/document.xml', $documentXml);
$zip->addFromString('word/styles.xml', $stylesXml);
$zip->addFromString('word/_rels/document.xml.rels', $wordRelsXml);
$zip->close();

echo $outputPath . PHP_EOL;
