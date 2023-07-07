<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'X1CWu_x9GrebaQUxyVGdJF3_4SCsVW9z1QjX-XJ9B6k';
$template_id = 'b47daffc-7caf-4bea-9f36-edf4067b2c08';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact = '$id'");

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
        $result = mysqli_query($conn, "SELECT * FROM tb_contact ");

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
    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $store_owner = $_POST['owner_name'];

        $result = mysqli_query($conn, "UPDATE tb_contact SET nama = '$nama', tgl_lahir = '$tgl_lahir', store_owner = '$store_owner' WHERE id_contact = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data kontak!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data kontak!"];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $nama = $_POST['nama'];
        $nomor_hp = $_POST['nomorhp'];

        $result = mysqli_query($conn, "INSERT INTO tb_contact(nama, nomorhp) VALUES('$nama', '$nomor_hp')");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data kontak!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data kontak!"];
            echo json_encode($response);
        }
        mysqli_close($conn);
    }
    

} 
