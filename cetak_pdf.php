<?php


// Load mPDF dan koneksi database
require_once __DIR__ . '/vendor/autoload.php';
include 'koneksi.php';

$mpdf = new \Mpdf\Mpdf();

$ambil = mysqli_query($mysqli, "SELECT * FROM inputmhs WHERE id='" . $_GET['id'] . "'");
while ($row = mysqli_fetch_array($ambil)) {
    $id = $row['id'];
    $nama = $row['namaMhs'];
    $nim = $row['nim'];
    $ipk = $row['ipk'];
    $sks = $row['sks'];
    $matkul = $row['matakuliah'];
}

$result = mysqli_query($mysqli, "SELECT * FROM jwl_mhs WHERE mhs_id='$id'");
// Query SQL berdasarkan tabel yang dipilih
$thead = "
        <tr>
            <th>No</th>
            <th>Matakuliah</th>
            <th>Sks</th>
            <th>Kelp</th>
            <th>Ruangan</th>
        </tr>";




// Mulai membuat konten HTML untuk PDF
$html = "

<style>
    h1, h2 {
        text-align: center;
     
    }
    h2{
       font-weight:medium;
    }    
    .header {
        padding: 15px;
        background-color: #ADD8E6; /* Warna biru muda */
        border-radius: 5px;
        margin-top: 20px;
        text-align: center;
    }
    .header .data {
        font-size: 16px;
    }
        
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table th, table td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
    }
</style>

<h1>Kartu Rencana Studi</h1>
<h2>Lihat jadwal matakuliah yang telah diinputkan disini</h2>

 <div class=`header`>
        <div class=`data`>
            <span><strong>Mahasiswa:</strong> $nama</span>
            <span>| <strong>NIM :</strong>  $nim </span>
            <span>| <strong>IPK :</strong> $ipk </span>
        </div>
</div>
<br>
<table border='1' cellspacing='0' cellpadding='5' style='width:100%;'>
    <thead>{$thead}</thead>
    <tbody>";

// Isi tabel dengan data dari database
$no = 1;
$total_sks = 0;
while ($data = mysqli_fetch_assoc($result)) {
    $total_sks += $data['sks'];
    $html .= "
        <tr>
            <td>{$no}</td>
            <td>{$data['matakuliah']}</td>
            <td>{$data['sks']}</td>
            <td>{$data['kelp']}</td>
            <td>{$data['ruangan']}</td>
        </tr>";

    $no++;
}

$html .= "
        <tr>
            <td colspan='2' style='text-align: center; font-weight: bold;'>Total SKS</td>
            <td style='text-align: center; font-weight: bold;'>$total_sks</td>
            <td colspan='2'></td>
        </tr>";

$html .= "</tbody></table>";

// Tulis konten HTML ke PDF
$mpdf->WriteHTML($html);

// Unduh atau tampilkan PDF
$mpdf->Output("Data_{$tabelDipilih}.pdf", 'I'); // 'I' untuk tampil di browser
exit;
