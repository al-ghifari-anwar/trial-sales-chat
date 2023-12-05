<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'X1CWu_x9GrebaQUxyVGdJF3_4SCsVW9z1QjX-XJ9B6k';
$template_id = 'b47daffc-7caf-4bea-9f36-edf4067b2c08';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['key'])) {
        $key = $_POST['key'];

        if (isset($_POST['id_city'])) {
            $id_city = $_POST['id_city'];

            if (isset($_POST['status'])) {
                $status = $_POST['status'];
                $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE nama LIKE '%$key%' AND id_city = '$id_city' AND store_status = '$status'");
            } else {
                $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE nama LIKE '%$key%' AND id_city = '$id_city'");
            }
        } else {
            $id_distributor = $_POST['dst'];
            if (isset($_POST['status'])) {
                $status = $_POST['status'];
                $result = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE nama LIKE '%$key%' AND store_status = '$status' AND id_distributor = '$id_distributor'");
            } else {
                $result = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE nama LIKE '%$key%' AND id_distributor = '$id_distributor'");
            }
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
    } else {

        echo json_encode(array("status" => "failed", "results" => []));
    }
}
