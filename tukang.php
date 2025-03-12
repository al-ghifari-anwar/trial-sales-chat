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
            $key = isset($_GET['key']) ? $_GET['key'] : '';
            $id_city = $_GET['c'];

            $result = mysqli_query($conn, "SELECT * FROM tb_tukang JOIN tb_skill ON tb_skill.id_skill = tb_tukang.id_skill WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND tb_tukang.id_city = '$id_city' ORDER BY tb_tukang.created_at DESC LIMIT 25");
        } else {
            $key = isset($_GET['key']) ? $_GET['key'] : '';
            $id_dist = $_GET['dst'];
            $result = mysqli_query($conn, "SELECT * FROM tb_tukang JOIN tb_skill ON tb_skill.id_skill = tb_tukang.id_skill JOIN tb_city ON tb_city.id_city = tb_tukang.id_city WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND id_distributor = '$id_dist' ORDER BY tb_tukang.created_at DESC LIMIT 25");
        }

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $is_self = $row['is_self'];
            $id_contact_post = $row['id_contact_post'];
            $id_user_post = $row['id_user_post'];

            if ($is_self == 1) {
                $row['posted_by'] = 'Self';
                $row['posted_name'] = $row['nama'];
            }

            if ($id_contact_post != 0) {
                $contact = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact = '$id_contact_post'");
                $contactRow = $contact->fetch_array(MYSQLI_ASSOC);
                $row['posted_by'] = 'Toko';
                $row['posted_name'] = $contact['nama'];
            }

            if ($id_user_post != 0) {
                $user = mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user = '$id_user_post'");
                $userRow = $user->fetch_array(MYSQLI_ASSOC);
                $row['posted_by'] = $userRow['level_user'];
                $row['posted_name'] = $userRow['full_name'];
            }

            if ($is_self == 0 && $id_contact_post == 0 && $id_user_post == 0) {
                $row['posted_by'] = "null";
                $row['posted_name'] = "null";
            }

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
        $getTukang = mysqli_query($conn, "SELECT * FROM tb_tukang WHERE id_tukang = '$id'");
        $rowTukang = $getTukang->fetch_array(MYSQLI_ASSOC);


        $id_user = $_POST['id_user'];

        $result = mysqli_query($conn, "UPDATE tb_tukang SET id_user_post = '$id_user' WHERE id_tukang = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data tukang!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data tukang!"];
            echo json_encode($response);
        }

        // mysqli_close($conn);
        // $response = ["response" => 200, "status" => "failed", "message" => "Data tukang tidak dapat dirubah"];
        // echo json_encode($response);
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

        $id_user_post = $_POST['id_user'];

        $checkNomor = mysqli_query($conn, "SELECT * FROM tb_tukang WHERE nomorhp = '$nomor_hp'");
        $rowNomor = $checkNomor->fetch_array(MYSQLI_ASSOC);

        if ($rowNomor == null) {

            $result = mysqli_query($conn, "INSERT INTO tb_tukang(nama, nomorhp, tgl_lahir, id_city, maps_url, address,tukang_status, id_skill, nama_lengkap, id_user_post) VALUES('$nama', '$nomor_hp', '$tgl_lahir','$id_city', '$mapsUrl', '$address','$status','$id_skill', '$nama_lengkap', '$id_user_post')");

            if ($result) {
                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data tukang!"];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data tukang!"];
                echo json_encode($response);
            }
            mysqli_close($conn);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Nomor sudah terdaftar!"];
            echo json_encode($response);
        }
    }
}
