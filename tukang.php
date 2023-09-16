<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'X1CWu_x9GrebaQUxyVGdJF3_4SCsVW9z1QjX-XJ9B6k';
$template_id = 'b47daffc-7caf-4bea-9f36-edf4067b2c08';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_tukang JOIN tb_skill ON tb_skill.id_skill = tb_tukang.id_skill WHERE id_tukang = '$id'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    } else {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $result = mysqli_query($conn, "SELECT * FROM tb_tukang JOIN tb_skill ON tb_skill.id_skill = tb_tukang.id_skill WHERE tb_tukang.id_city = '$id_city'");
        } else {
            $result = mysqli_query($conn, "SELECT * FROM tb_tukang");
        }

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $nomor_hp = $_POST['nomorhp'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $id_city = $_POST['id_city'];
        $mapsUrl = $_POST['mapsUrl'];
        $address = $_POST['address'];
        $status = $_POST['status'];
        $id_skill = $_POST['id_skill'];
        $nama_lengkap = $_POST['nama_lengkap'];
        // NEW
        $proof_closing = $_FILES['ktp']['name'];
        $dateFile = date("Y-m-d-H-i-s");

        if (move_uploaded_file($_FILES['ktp']['tmp_name'], 'img/' . $dateFile . $_FILES['ktp']['name'])) {
            $sourceImage = 'img/' . $dateFile . $_FILES['ktp']['name'];
            $imageDestination = 'img/min-' . $dateFile . $_FILES['ktp']['name'];
            $createImage = imagecreatefromjpeg($sourceImage);
            imagejpeg($createImage, $imageDestination, 60);
        }

        $imgNewName = $dateFile . $_FILES['ktp']['name'];

        $result = mysqli_query($conn, "UPDATE tb_tukang SET nama = '$nama', tgl_lahir = '$tgl_lahir', id_city = '$id_city', maps_url = '$mapsUrl', address = '$address', tukang_status = '$status', nomorhp = '$nomor_hp', ktp_tukang = 'min-$imgNewName', id_skill = $id_skill, nama_lengkap = '$nama_lengkap' WHERE id_tukang = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data tukang!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data tukang!"];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $nama = $_POST['nama'];
        $nomor_hp = $_POST['nomorhp'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $id_city = $_POST['id_city'];
        $mapsUrl = $_POST['mapsUrl'];
        $address = $_POST['address'];
        $status = $_POST['status'];
        $id_skill = $_POST['id_skill'];
        $nama_lengkap = $_POST['nama_lengkap'];

        $result = mysqli_query($conn, "INSERT INTO tb_tukang(nama, nomorhp, tgl_lahir, id_city, maps_url, address,tukang_status, id_skill, nama_lengkap) VALUES('$nama', '$nomor_hp', '$tgl_lahir','$id_city', '$mapsUrl', '$address','$status','$id_skill', '$nama_lengkap')");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data tukang!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data tukang!"];
            echo json_encode($response);
        }
        mysqli_close($conn);
    }
}
