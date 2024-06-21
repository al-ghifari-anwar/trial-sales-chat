<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');
// putenv('GDFONTPATH=' . realpath('.'));

// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';

// $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

$result = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE tgl_lahir IS NOT NULL");

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $transArray[] = $row;
}


foreach ($transArray as $arr) {
    if ($arr['tgl_lahir'] != '0000-00-00') {
        $id_contact = $arr['id_contact'];
        $reputation = $arr['reputation'];
        $id_distributor = $arr['id_distributor'];
        $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
        $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
        $integration_id = $rowQontak['integration_id'];

        $nomor_forward = '';
        if ($id_distributor == 1) {
            $nomor_forward = '6287757904850';
        } else {
            $nomor_forward = '6281128500888';
        }

        $tgl_lahir = date("m-d", strtotime($arr['tgl_lahir']));
        $tgl_skrg = date("m-d");
        $nomor_hp = $arr['nomorhp'];
        $nama = $arr['store_owner'];
        $toko = $arr['nama'];

        if ($tgl_lahir == $tgl_skrg) {
            $img = imagecreatefrompng("img/bday.png");
            // echo "A";
            // (B) WRITE TEXT
            $txt = $nama . "\n" . $toko;
            $fontFile = __DIR__ . "/font/CoffeCake.ttf"; // CHANGE TO YOUR OWN!
            $fontSize = 35;
            $fontColor = imagecolorallocate($img, 0, 0, 0);
            $posX = 212;
            $posY = 610;
            $angle = 0;
            // (C) CALCULATE TEXT BOX POSITION
            // (C1) GET IMAGE DIMENSIONS
            $iWidth = imagesx($img);
            $iHeight = imagesy($img);

            // (C2) GET TEXT BOX DIMENSIONS
            $tSize = imagettfbbox($fontSize, $angle, $fontFile, $txt);
            $tWidth = max([$tSize[2], $tSize[4]]) - min([$tSize[0], $tSize[6]]);
            $tHeight = max([$tSize[5], $tSize[7]]) - min([$tSize[1], $tSize[3]]);
            // (C3) CENTER THE TEXT BLOCK
            $centerX = ceil(($iWidth - $tWidth) / 2);
            $centerX = $centerX < 0 ? 0 : $centerX;

            imagettftext($img, $fontSize, $angle, $centerX, $posY, $fontColor, $fontFile, $txt);

            // (C2) OR SAVE TO A FILE
            $quality = 80; // 0 to 100
            imagejpeg($img, "img/bday_" . $nomor_hp . ".jpg", $quality);

            $getSak = mysqli_query($conn, "SELECT SUM(qty_produk) AS total_qty FROM tb_detail_surat_jalan JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan WHERE tb_surat_jalan.id_contact = '$id_contact'");
            $rowSak = $getSak->fetch_array(MYSQLI_ASSOC);
            $pembelianSak = $rowSak['total_qty'];

            if ($reputation == 'good') {
                $jmlVoucher = 0;
                if ($pembelianSak != null) {
                    if ($pembelianSak <= 100) {
                        $jmlVoucher = 1;
                    } else if ($pembelianSak > 100) {
                        $jmlVoucher = 2;
                    }
                } else {
                    if ($reputation == 'good') {
                        $jmlVoucher = 1;
                    }
                }

                $curl = curl_init();

                curl_setopt_array(
                    $curl,
                    array(
                        CURLOPT_URL => 'https://saleswa.topmortarindonesia.com/insertVoucher.php?j=' . $jmlVoucher . '&s=' . $id_contact,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    )
                );

                $response = curl_exec($curl);

                curl_close($curl);

                $res = json_decode($response, true);

                $status = $res['status'];

                if ($status == 'ok') {
                    $voucherArr = array();
                    $dateNow = date("m-d");
                    $getVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_contact = '$id_contact' AND is_claimed = 0 AND date_voucher LIKE '%$dateNow%' ");
                    while ($rowVoucher = $getVoucher->fetch_array(MYSQLI_ASSOC)) {
                        $voucherArr[] = $rowVoucher;
                    }
                    $vouchers = "";
                    foreach ($voucherArr as $voucherArr) {
                        $vouchers .= $voucherArr['no_voucher'] . ",";
                    }
                    // $template_id = "52df213b-b75e-4175-b3ec-be7963b8e93a";
                    // Send message
                    // $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
                    // $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
                    // $integration_id = $rowQontak['integration_id'];
                    $template_vc = "52df213b-b75e-4175-b3ec-be7963b8e93a";
                    $message = "Selamat ulang tahun! Selamat anda mendapatkan Voucher. Tukarkan voucher anda dengan produk-produk unggulan kami sebelum tanggal " . date("d M, Y", strtotime("+30 days")) . ". Kode voucher: " . $vouchers;
                    // Send message
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
                                "message_template_id": "' . $template_vc . '",
                                "channel_integration_id": "' . $integration_id . '",
                                "language": {
                                    "code": "id"
                                },
                                "parameters": {
                                    "header":{
                                        "format":"IMAGE",
                                        "params": [
                                            {
                                                "key":"url",
                                                "value":"https://saleswa.topmortarindonesia.com/img/bday_' . $nomor_hp . '.jpg"
                                            },
                                            {
                                                "key":"filename",
                                                "value":"bday.jpg"
                                            }
                                        ]
                                    },
                                    "body": [
                                        {
                                            "key": "1",
                                            "value": "nama",
                                            "value_text": "' . $nama . '"
                                        },
                                        {
                                            "key": "2",
                                            "value": "jml_voucher",
                                            "value_text": "' . $jmlVoucher . '"
                                        },
                                        {
                                            "key": "3",
                                            "value": "no_voucher",
                                            "value_text": "' . $vouchers . '"
                                        },
                                        {
                                            "key": "4",
                                            "value": "date_voucher",
                                            "value_text": "' . date("d M, Y", strtotime("+30 days")) . '"
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
                    echo $response;

                    $status = $res['status'];

                    if ($status == "success") {
                        // Send forward message
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
                                "to_number": "' . $nomor_forward . '",
                                "to_name": "' . $nama . '",
                                "message_template_id": "' . $template_vc . '",
                                "channel_integration_id": "' . $integration_id . '",
                                "language": {
                                    "code": "id"
                                },
                                "parameters": {
                                    "header":{
                                        "format":"IMAGE",
                                        "params": [
                                            {
                                                "key":"url",
                                                "value":"https://saleswa.topmortarindonesia.com/img/bday_' . $nomor_hp . '.jpg"
                                            },
                                            {
                                                "key":"filename",
                                                "value":"bday.jpg"
                                            }
                                        ]
                                    },
                                    "body": [
                                        {
                                            "key": "1",
                                            "value": "nama",
                                            "value_text": "' . $nama . '"
                                        },
                                        {
                                            "key": "2",
                                            "value": "jml_voucher",
                                            "value_text": "' . $jmlVoucher . '"
                                        },
                                        {
                                            "key": "3",
                                            "value": "no_voucher",
                                            "value_text": "' . $vouchers . '"
                                        },
                                        {
                                            "key": "4",
                                            "value": "date_voucher",
                                            "value_text": "' . date("d M, Y", strtotime("+30 days")) . '"
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
                        echo $response;

                        $status = $res['status'];

                        if ($status == 'success') {
                            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim forward ucapan ultah!"];
                            echo json_encode($response);
                        } else {
                            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim forward ucapan ultah!"];
                            echo json_encode($response);
                        }
                    } else {
                        $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim ucapan ultah!"];
                        echo json_encode($response);
                    }
                }
            } else {
                $template_id = '9e5f403d-a064-475e-b172-74ce62a56ede';
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
                        "header":{
                            "format":"IMAGE",
                            "params": [
                                {
                                    "key":"url",
                                    "value":"https://saleswa.topmortarindonesia.com/img/bday_' . $nomor_hp . '.jpg"
                                },
                                {
                                    "key":"filename",
                                    "value":"bday.jpg"
                                }
                            ]
                        },
                        "body": [
                        {
                            "key": "1",
                            "value": "nama",
                            "value_text": "' . $nama . '"
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

                // echo $response;
                // die;
                // $status = "success";

                if ($status == 'success') {
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
                        "to_number": "' . $nomor_forward . '",
                        "to_name": "' . $nama . '",
                        "message_template_id": "' . $template_id . '",
                        "channel_integration_id": "' . $integration_id . '",
                        "language": {
                            "code": "id"
                        },
                        "parameters": {
                            "header":{
                                "format":"IMAGE",
                                "params": [
                                    {
                                        "key":"url",
                                        "value":"https://saleswa.topmortarindonesia.com/img/bday_' . $nomor_hp . '.jpg"
                                    },
                                    {
                                        "key":"filename",
                                        "value":"bday.jpg"
                                    }
                                ]
                            },
                            "body": [
                            {
                                "key": "1",
                                "value": "nama",
                                "value_text": "Forwarding from - ' . $nama . '"
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
                    // $status = "success";

                    if ($status == "success") {
                        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim ucapan ultah!"];
                        echo json_encode($response);
                    } else {
                        $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim forward ucapan ultah!"];
                        echo json_encode($response);
                    }
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim ucapan ultah!"];
                    echo json_encode($response);
                }
            }
        }
    }
}
