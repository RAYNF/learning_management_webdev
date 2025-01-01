<?php
$ambil = mysqli_query($mysqli, "SELECT * FROM inputmhs WHERE id='" . $_GET['id'] . "'");
while ($row = mysqli_fetch_array($ambil)) {
    $id = $row['id'];
    $nama = $row['namaMhs'];
    $nim = $row['nim'];
    $ipk = $row['ipk'];
    $sks = $row['sks'];
    $matkul = $row['matakuliah'];
}
?>

<div class="content px-3 mt-3">
    <!-- start judul -->
    <div class="judul d-flex flex-column justify-content-center align-items-center text-center">
        <h1 class="fw-bold display-6">Sistem Input Kartu Rencana Studi (KRS)</h1>
        <h3 class="text-secondary d-none d-md-block ">Input data KRS mahasiswa dengan mudah dan cepat</h3>
        <h5 class="text-secondary d-block d-md-none">Input data KRS mahasiswa dengan mudah dan cepat</h5>
    </div>
    <!-- end judul -->
    <!-- start header -->
    <div class="header p-3 w-100 bg-info d-flex flex-row justify-content-between align-items-center mt-3">
        <div class="data h4 m-0 d-flex flex-wrap gap-2">
            <span>Mahasiswa: <?php echo $nama ?></span>
            <span>| NIM: <?php echo $nim ?></span>
            <span>| IPK: <?php echo $ipk ?></span>
        </div>
        <a href="index.php" class="btn btn-warning w-25 d-none d-md-block">Kembali ke data mahasiswa</a>
        <a href="index.php" class="btn btn-warning w-25 d-block d-md-none">Kembali</a>
    </div>
    <!-- end header -->
    <!-- start form -->
    <form action="" class="form column mt-5" method="post" name="myForm">
        <input type="hidden" name="id_mahasiswa" value="<?php echo $id ?>">
        <div class="col">
            <label for="inputmatkul" class="sr-only">
                Matakuliah
            </label>
            <select class="form-control w-50" id="inputmatkul" name="id_matkul">
                <?php
                $selected = '';
                $listMatkul = mysqli_query($mysqli, "SELECT * FROM jwl_matakuliah");
                while ($data = mysqli_fetch_array($listMatkul)) {
                    // if ($data["id"] == $matkul) {
                    //     $selected = 'selected="selected"';
                    // } else {
                    //     $selected = '';
                    // }
                    $selected = ($data["id"] == $matkul) ? 'selected="selected"' : '';

                ?>
                    <option value="<?php echo $data['id'] ?>" <?php echo $selected ?>><?php echo $data['matakuliah'] ?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="col mt-3">
            <button type="submit" class="btn btn-primary px-3 w-100" name="simpan_matkul">Simpan</button>
        </div>
    </form>
    <!-- end form -->

    <!-- start tabel -->
    <table class="table table-hover mt-3">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Matakuliah</th>
                <th scope="col">SKS</th>
                <th scope="col">Kelp</th>
                <th scope="col">Ruangan</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $listMatkul = mysqli_query($mysqli, "SELECT * FROM jwl_mhs WHERE mhs_id='$id'");
            while ($data = mysqli_fetch_array($listMatkul)) {
            ?>
                <tr>
                    <th><?php echo $no++ ?></th>
                    <td><?php echo $data['matakuliah'] ?></td>
                    <td><?php echo $data['sks'] ?></td>
                    <td><?php echo $data['kelp'] ?></td>
                    <td><?php echo $data['ruangan'] ?></td>
                    <td>
                        <a class="btn btn-danger px-3" href="index.php?id_mahasiswa=<?php echo $id ?>&aksi=hapus_matkul&id_matkul=<?php echo $data['id'] ?>&page=edit">Hapus</a>
                    </td>
                </tr>
            <?php
                $no++;
            }
            ?>
        </tbody>
    </table>
    <!-- end tabel -->
    <!-- start cetak -->
    <div class="mt-3">
        <a class="btn btn-outline-danger m-3" nama="cetak_Pdf" href="cetak_pdf.php?id=<?php echo $id?>" target="_blank">Export Pdf</a>
    </div>
    <!-- end cetak -->
    <?php
    if (isset($_POST['simpan_matkul'])) {
        $id_mahasiswa = $_POST['id_mahasiswa'];
        $id_matkul = $_POST['id_matkul'];

        $cekMatkul = mysqli_query($mysqli, "SELECT * FROM jwl_mhs WHERE mhs_id = '$id_mahasiswa' AND matakuliah = (SELECT matakuliah FROM jwl_matakuliah WHERE id = '$id_matkul')");
        if (mysqli_num_rows($cekMatkul) > 0) {
            echo "<script>alert('Mata kuliah sudah diambil!');</script>";
        } else {
            $matkulDetail = mysqli_query($mysqli, "SELECT matakuliah, sks, kelp, ruangan FROM jwl_matakuliah WHERE id = '$id_matkul'");
            $matkulData = mysqli_fetch_assoc($matkulDetail);

            $simpan = mysqli_query($mysqli, "INSERT INTO jwl_mhs (mhs_id, matakuliah, sks, kelp, ruangan) 
            VALUES ('$id_mahasiswa', '{$matkulData['matakuliah']}', '{$matkulData['sks']}', '{$matkulData['kelp']}', '{$matkulData['ruangan']}')");

            if ($simpan) {
                $getMatkulList = mysqli_query($mysqli, "SELECT matakuliah FROM jwl_mhs WHERE mhs_id = '$id_mahasiswa'");
                $matkulList = [];
                while ($matkulRow = mysqli_fetch_array($getMatkulList)) {
                    $matkulList[] = $matkulRow['matakuliah'];
                }
                $matkulString = implode(', ', $matkulList);
                mysqli_query($mysqli, "UPDATE inputmhs SET matakuliah = '$matkulString' WHERE id = '$id_mahasiswa'");

                echo "<script>alert('Mata kuliah berhasil disimpan!'); document.location='index.php?id=" . $id_mahasiswa . "&page=edit';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan mata kuliah!');</script>";
            }
        }
    }

    if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_matkul' && isset($_GET['id_matkul'])) {
        $id_matkul = $_GET['id_matkul'];
        $id_mahasiswa = $_GET['id_mahasiswa'];

        // Hapus data dari tabel jwl_mhs
        $hapus = mysqli_query($mysqli, "DELETE FROM jwl_mhs WHERE id = '$id_matkul'");
        if ($hapus) {
            // Update kolom matakuliah di tabel inputmhs
            $getMatkulList = mysqli_query($mysqli, "SELECT matakuliah FROM jwl_mhs WHERE mhs_id = '$id_mahasiswa'");
            $matkulList = [];
            while ($matkulRow = mysqli_fetch_array($getMatkulList)) {
                $matkulList[] = $matkulRow['matakuliah'];
            }
            $matkulString = implode(', ', $matkulList);
            mysqli_query($mysqli, "UPDATE inputmhs SET matakuliah = '$matkulString' WHERE id = '$id_mahasiswa'");

            echo "<script>alert('Mata kuliah berhasil dihapus!'); document.location='index.php?id=" . $id_mahasiswa . "&page=edit';</script>";
        } else {
            echo "<script>alert('Gagal menghapus mata kuliah!');</script>";
        }
    }
    ?>

</div>