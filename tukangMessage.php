<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = '4a58a270-09a2-4c54-af90-385a61265e2c';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_order = '$id'");

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
        $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE tb_contact.status = 'waiting'");

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
    $nama = $_POST['nama'];
    $nomor_hp = $_POST['nomorhp'];
    $message = $_POST['message_body'];
    $id_city = $_POST['id_city'];
    $mapsUrl = $_POST['mapsUrl'];
    $address = $_POST['address'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $id_skill = $_POST['id_skill'];
    $id_user = $_POST['id_user'];

    if (isset($_POST['full_name'])) {
        $full_name = $_POST['full_name'];
    } else {
        $full_name = 'Sales Top Mortar';
    }

    if (isset($_POST['tgl_lahir'])) {
        $tgl_lahir = $_POST['tgl_lahir'];
    } else {
        $tgl_lahir = '0000-00-00';
    }

    if (isset($_POST['mapsUrl'])) {
        $mapsUrl = $_POST['mapsUrl'];
    } else {
        $mapsUrl = '';
    }

    $id_tukang = null;

    $checkKontak = mysqli_query($conn, "SELECT * FROM tb_tukang WHERE nomorhp = '$nomor_hp'");

    $row = $checkKontak->fetch_array(MYSQLI_ASSOC);
    if ($row == null) {
        $result = mysqli_query($conn, "INSERT INTO tb_tukang(nama, nomorhp, tgl_lahir, id_city, maps_url, id_skill, nama_lengkap, id_user_post) VALUES('$nama', '$nomor_hp', '$tgl_lahir', $id_city, '$mapsUrl', $id_skill, '$nama_lengkap', $id_user)");
        $id_tukang = mysqli_insert_id($conn);
    } else {
        $result = mysqli_query($conn, "SELECT * FROM tb_tukang WHERE nomorhp = '$nomor_hp'");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $id_tukang =  $row['id_tukang'];
    }

    $getUserData = mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user = '$id_user'");
    $rowUserData = $getUserData->fetch_array(MYSQLI_ASSOC);

    $id_distributor = $rowUserData['id_distributor'];

    $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
    $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);


    if (isset($_POST['message_body'])) {
        $integration_id = $rowQontak['integration_id'];
        $wa_token = $rowQontak['token'];
        if ($id_tukang != null) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);

            $resultMsg = mysqli_query($conn, "INSERT INTO tb_messages(id_tukang, message_body) VALUES($id_tukang, '$message')");

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                    "to_number": "' . $nomor_hp . '",
                    "to_name": "' . $nama . '",
                    "message_template_id": "' . $template_id . '",
                    "channel_integration_id": "' . $integration_id . '",
                    "language": {
                        "code": "id"
                    },
                    "parameters": {
                        "body": [
                        {
                            "key": "1",
                            "value": "nama",
                            "value_text": "' . $nama . '"
                        },
                        {
                            "key": "2",
                            "value": "message",
                            "value_text": "' . $message . '"
                        },
                        {
                            "key": "3",
                            "value": "sales",
                            "value_text": "' . $full_name . '"
                        }
                        ]
                    }
                    }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $wa_token,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data tukang!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data tukang!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
    }

    mysqli_close($conn);
}
