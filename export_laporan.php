<?php
require_once 'config.php';
require_once 'fungsi/view/view.php';

$lihat = new view($config);

// Ambil filter
$filter = $_GET['filter'] ?? '';
$bln = $_GET['bln'] ?? '';
$thn = $_GET['thn'] ?? '';
$hari = $_GET['hari'] ?? '';
$type = $_GET['type'] ?? 'xls';

// Ambil data dari tabel nota sesuai filter
if ($filter == 'bulan' && $bln && $thn) {
    $periode = $thn . '-' . $bln;
    $hasil = $lihat->periode_nota($periode);
    $judul = "Laporan Penjualan Bulan $bln-$thn";
} elseif ($filter == 'tahun' && $thn) {
    $hasil = $lihat->tahun_nota($thn);
    $judul = "Laporan Penjualan Tahun $thn";
} elseif ($filter == 'hari' && $hari) {
    $hasil = $lihat->hari_nota($hari);
    $judul = "Laporan Penjualan Tanggal $hari";
} else {
    $hasil = $lihat->nota();
    $judul = "Laporan Penjualan";
}

// Siapkan data
$no = 1;
$bayar = 0;
$jumlah = 0;
$modal = 0;
$rows = '';
foreach ($hasil as $isi) {
    $bayar += $isi['total'];
    $modal += $isi['harga_beli'] * $isi['jumlah'];
    $jumlah += $isi['jumlah'];
    $rows .= "<tr>
        <td>{$no}</td>
        <td>{$isi['id_barang']}</td>
        <td>{$isi['nama_barang']}</td>
        <td>{$isi['jumlah']}</td>
        <td>Rp." . number_format($isi['harga_beli'] * $isi['jumlah']) . ",-</td>
        <td>Rp." . number_format($isi['total']) . ",-</td>
        <td>{$isi['nm_member']}</td>
        <td>{$isi['tanggal_input']}</td>
    </tr>";
    $no++;
}
$rows .= "<tr>
    <td>-</td>
    <td>-</td>
    <td><b>Total Terjual</b></td>
    <td><b>$jumlah</b></td>
    <td><b>Rp." . number_format($modal) . ",-</b></td>
    <td><b>Rp." . number_format($bayar) . ",-</b></td>
    <td><b>Keuntungan</b></td>
    <td><b>Rp." . number_format($bayar - $modal) . ",-</b></td>
</tr>";

// Export XLS
if ($type == 'xls') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=laporan-penjualan.xls");
    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<h3 style='text-align:center;'>$judul</h3>";
    echo "<table border='1' width='100%' cellpadding='3' cellspacing='4'>
        <thead>
            <tr bgcolor='yellow'>
                <th>No</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Modal</th>
                <th>Total</th>
                <th>Kasir</th>
                <th>Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            $rows
        </tbody>
    </table>";
    echo "</body></html>";
    exit;
}

// Export PDF
if ($type == 'pdf') {
    require_once __DIR__ . '/vendor/autoload.php'; // pastikan mPDF sudah diinstall via composer
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
    $html = "<h3 style='text-align:center;'>$judul</h3>
    <table border='1' width='100%' cellpadding='3' cellspacing='4'>
        <thead>
            <tr bgcolor='yellow'>
                <th>No</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Modal</th>
                <th>Total</th>
                <th>Kasir</th>
                <th>Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            $rows
        </tbody>
    </table>";
    $mpdf->WriteHTML($html);
    $mpdf->Output('laporan-penjualan.pdf', 'I');
    exit;
}

// Jika tidak ada type, tampilkan error
echo "Tipe export tidak dikenali.";
