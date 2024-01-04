<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'X1CWu_x9GrebaQUxyVGdJF3_4SCsVW9z1QjX-XJ9B6k';
$template_id = 'b47daffc-7caf-4bea-9f36-edf4067b2c08';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_warehouse WHERE id_warehouse = '$id'");

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
            $result = mysqli_query($conn, "SELECT * FROM tb_warehouse WHERE tb_warehouse.id_city = '$id_city'");
        } else {
            $id_distributor = $_GET['dst'];
            $result = mysqli_query($conn, "SELECT * FROM tb_warehouse JOIN tb_city ON tb_city.id_city = tb_warehouse.id_city WHERE id_distributor = '$id_distributor'");
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

        $getGudang = mysqli_query($conn, "SELECT * FROM tb_warehouse WHERE id_warehouse = '$id'");
        $rowGudang = $getGudang->fetch_array(MYSQLI_ASSOC);
        $nama_warehouse = $_POST['nama_warehouse'];
        $location_warehouse = $_POST['location_warehouse'];
        $nomorhp_warehouse = $_POST['nomorhp_warehouse'];
        $id_city = $_POST['id_city'];

        $result = mysqli_query($conn, "UPDATE tb_warehouse SET nama_warehouse = '$nama_warehouse', location_warehouse = '$location_warehouse', nomorhp_warehouse = '$nomorhp_warehouse', id_city = '$id_city' WHERE id_warehouse = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data gudang!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data gudang!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $nama_warehouse = $_POST['nama_warehouse'];
        $location_warehouse = $_POST['location_warehouse'];
        $nomorhp_warehouse = $_POST['nomorhp_warehouse'];
        $id_city = $_POST['id_city'];

        $result = mysqli_query($conn, "INSERT INTO tb_warehouse(nama_warehouse,location_warehouse,nomorhp_warehouse,id_city) VALUES('$nama_warehouse', '$location_warehouse','$nomorhp_warehouse', $id_city)");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data gudang!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data gudang!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
        mysqli_close($conn);
    }
}
