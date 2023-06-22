<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = '9ac4e6a5-0a71-4d00-981b-6cf05e5637da';
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
    $id_contact = null;

    $checkKontak = mysqli_query($conn, "SELECT * FROM tb_contact WHERE nomorhp = '$nomor_hp'");

    $row = $checkKontak->fetch_array(MYSQLI_ASSOC);
    if ($row == null) {
        $result = mysqli_query($conn, "INSERT INTO tb_contact(nama, nomorhp) VALUES('$nama', '$nomor_hp')");
        $id_contact = mysqli_insert_id($conn);
    } else {
        $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE nomorhp = '$nomor_hp'");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $id_contact =  $row['id_contact'];
    }


    if ($id_contact != null) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        $resultMsg = mysqli_query($conn, "INSERT INTO tb_messages(id_contact, message_body) VALUES($id_contact, '$message')");

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

        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data transaksi!"];
        echo json_encode($response);
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data transaksi!"];
        echo json_encode($response);
    }

    mysqli_close($conn);
}
