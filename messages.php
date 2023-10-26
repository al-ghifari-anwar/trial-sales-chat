<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = '85f17083-255d-4340-af32-5dd22f483960';
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
    // NEW
    $termin_payment = $_POST['termin_payment'];
    // Bid
    $id_user = $_POST['id_user'];

    if (isset($_POST['full_name'])) {
        $full_name = $_POST['full_name'];
    } else {
        $full_name = 'Sales Top Mortar';
    }

    if (isset($_POST['owner_name'])) {
        $store_owner = $_POST['owner_name'];
    } else {
        $store_owner = '';
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

    $id_contact = null;

    $checkKontak = mysqli_query($conn, "SELECT * FROM tb_contact WHERE nomorhp = '$nomor_hp'");

    $row = $checkKontak->fetch_array(MYSQLI_ASSOC);
    if ($row == null) {
        $result = mysqli_query($conn, "INSERT INTO tb_contact(nama, nomorhp, store_owner, tgl_lahir, id_city, maps_url,termin_payment) VALUES('$nama', '$nomor_hp','$store_owner', '$tgl_lahir', $id_city, '$mapsUrl', $termin_payment)");
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

        $res = json_decode($response, true);

        $status = $res['status'];

        if ($status == 'success') {
            $checkBid = mysqli_query($conn, "SELECT * FROM tb_bid WHERE id_contact = '$id_contact' AND id_user = '$id_user' AND is_active = 1");
            $rowBid = $checkBid->fetch_array(MYSQLI_ASSOC);

            if ($rowBid == null) {
                $insertBid = mysqli_query($conn, "INSERT INTO tb_bid(id_contact,id_user,is_active) VALUES($id_contact, $id_user, 1)");
                $id_bid = mysqli_insert_id($conn);

                if ($insertBid) {
                    $updateStoreStatus = mysqli_query($conn, "UPDATE tb_contact SET store_status = 'bid' WHERE id_contact = '$id_contact'");

                    if ($updateStoreStatus) {
                        $insertAction = mysqli_query($conn, "INSERT INTO tb_action_bid(id_bid, field_action_bid) VALUES($id_bid, 'Send new message')");

                        if ($updateStoreStatus) {
                            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim pesan!"];
                            echo json_encode($response);
                        } else {
                            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan record bid!"];
                            echo json_encode($response);
                        }
                    } else {
                        $response = ["response" => 200, "status" => "failed", "message" => "Gagal merubah status toko!"];
                        echo json_encode($response);
                    }
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Proses bid gagal, silahkan coba lagi!"];
                    echo json_encode($response);
                }
            } else {
                $id_bid = $rowBid['id_bid'];
                $insertAction = mysqli_query($conn, "INSERT INTO tb_action_bid(id_bid, field_action_bid) VALUES($id_bid, 'Send message')");

                if ($insertAction) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim pesan!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan record bid!"];
                    echo json_encode($response);
                }
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim pesan!"];
            echo json_encode($response);
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data pesan!"];
        echo json_encode($response);
    }

    mysqli_close($conn);
}
