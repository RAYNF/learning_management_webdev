<?php
include_once("koneksi.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        rel="stylesheet"
        href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" />

    <title>Siakad</title>

</head>

<body>


    <!-- start main -->
    <main role="main" class="container w-100">
        <?php
        if (isset($_GET['page'])) {
            include($_GET['page'] . ".php");
        } else { ?>
            <div class="content px-3 mt-3">
                <!-- start judul -->
                <div class="judul d-flex flex-column justify-content-center align-items-center text-center">
                    <h1 class="fw-bold display-6">Sistem Input Kartu Rencana Studi (KRS)</h1>
                    <h3 class="text-secondary d-none d-md-block ">Input data Mahasiswa disini</h3>
                    <h5 class="text-secondary d-block d-md-none">Input data Mahasiswa disini</h5>
                </div>
                <!-- end judul -->
                <!-- start form -->
                <div class="forms mt-3">
                    <form action="" class="form row" method="post" name="myForm">
                        <?php
                        $nama = '';
                        $nim = '';
                        $ipk = '';
                        $sks = '';
                        $matkul = '';

                        if (isset($_GET['id'])) {
                            $ambil = mysqli_query($mysqli, "SELECT * FROM inputmhs WHERE id='" . $_GET['id'] . "'");
                            while ($row = mysqli_fetch_array($ambil)) {
                                $nama = $row['namaMhs'];
                                $nim = $row['nim'];
                                $ipk = $row['ipk'];
                                $sks = $row['sks'];
                                $matkul = $row['matakuliah'];
                            } ?>
                            <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                        <?php
                        }
                        ?>


                        <div class="col mb-2">
                            <label for="namaMahasiswa" class="form-label fw-medium">Nama Mahasiswa</label>
                            <input type="text" class="form-control" name="namaMhs" id="namaMahasiswa" placeholder="Input nama" value="<?php echo $nama ?>">
                        </div>
                        <div class="col mb-2">
                            <label for="nimMahasiswa" class="form-label fw-medium">Nim Mahasiswa</label>
                            <input type="text" class="form-control" name="nim" id="nimMahasiswa" placeholder="Input Nim" value="<?php echo $nim ?>">
                        </div>
                        <div class="col mb-2">
                            <label for="ipkMahasiswa" class="form-label fw-medium">Ipk</label>
                            <input type="text" class="form-control" name="ipk" id="ipkMahasiswa" placeholder="Input Ipk" value="<?php echo $ipk ?>">
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary px-3 w-100" name="simpan">Simpan</button>
                        </div>
                    </form>
                </div>
                <!-- end form -->
                <!-- start tabel -->
                <div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Mahasiswa</th>
                                <th scope="col">IPK</th>
                                <th scope="col">SKS Maksimal</th>
                                <th scope="col">Matkul yang diambil</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($mysqli, "SELECT * FROM inputmhs");
                            $no = 1;
                            while ($data = mysqli_fetch_array($result)) {
                                $matkul = (!empty($data['matakuliah']) && $data['matakuliah'] != '-') ? $data['matakuliah'] : '-';
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $no++ ?></th>
                                    <td><?php echo $data['namaMhs'] ?></td>
                                    <td><?php echo $data['ipk'] ?></td>
                                    <td><?php echo $data['sks'] ?></td>
                                    <td><?php echo $matkul ?></td>
                                    <td class="d-flex flex-column flex-sm-row gap-2">
                                        <a class="btn btn-danger rounded-pill px-3" href="index.php?id=<?php echo $data['id'] ?>&aksi=hapus">Hapus</a>
                                        <a class="btn btn-primary rounded-pill px-3" href="index.php?id=<?php echo $data['id'] ?>&page=edit">Edit</a>
                                        <a class="btn btn-secondary rounded-pill px-3" href="cetak_pdf.php?id=<?php echo $data['id'] ?>" target="_blank">Lihat</a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>

                            <?php
                            if (isset($_POST['simpan'])) {

                                if (isset($_POST['id'])) {
                                    // belum kepake
                                } else {
                                    $namaMHS = trim(mysqli_real_escape_string($mysqli, $_POST['namaMhs']));
                                    $nimMHS = trim(mysqli_real_escape_string($mysqli, $_POST['nim']));
                                    $ipkMHS = trim(mysqli_escape_string($mysqli, $_POST['ipk']));

                                    if (empty($namaMHS) || empty($nimMHS) || empty($ipkMHS)) { ?>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="alert alert-danger alert-dismissible" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    <strong>Register gagal:</strong> Semua field harus diisi
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        $result = mysqli_query($mysqli, "SELECT nim FROM inputmhs WHERE nim='$nimMHS'");
                                        if (mysqli_num_rows($result) > 0) { ?>
                                            <div class="row mt-3">
                                                <div class="col">
                                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                        <strong>Input Mahasiswa gagal:</strong> nim sudah digunakan
                                                    </div>
                                                </div>
                                            </div>
                            <?php
                                        } else {

                                            $sksMHS = ($ipkMHS < 3) ? 20 : 24;

                                            $tambah = mysqli_query($mysqli, "INSERT INTO inputmhs(namaMhs,nim,ipk,sks,matakuliah) VALUES (
                                                '$namaMHS',
                                                '$nimMHS',
                                                '$ipkMHS',
                                                '$sksMHS',
                                                '-'
                                              )");

                                            echo "<script>
                                              document.location='index.php';
                                              </script>";
                                        }
                                    }
                                }
                            }

                            if (isset($_GET['aksi'])) {
                                if ($_GET['aksi'] == 'hapus') {
                                    $hapusMhs = mysqli_query($mysqli, "DELETE FROM inputmhs WHERE id = '" . $_GET['id'] . "'");
                                    $hapusJwlMhs = mysqli_query($mysqli, "DELETE FROM jwl_mhs WHERE mhs_id = '" . $_GET['id'] . "'");

                                    echo "<script>
                                    document.location='index.php';
                                    </script>";
                                } else if ($_GET['aksi'] == 'ubah') {
                                    # code...
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
                <!-- end tabel -->
            </div>
        <?php
        }
        ?>
    </main>
    <!-- end main -->

    <script src="./bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>