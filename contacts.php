<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
$template_id = '2eead557-1b0b-4613-b89c-5c153f5bfe55';
// $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact = '$id'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $id_contact = $row['id_contact'];
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
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND tb_contact.id_city = '$id_city' AND store_status = '$status'");
            } else {
                $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND tb_contact.id_city = '$id_city'");
            }
        } else {
            $key = isset($_GET['key']) ? $_GET['key'] : '';
            $id_distributor = $_GET['dst'];
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $result = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND store_status = '$status' AND id_distributor = '$id_distributor'");
            } else {
                $result = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND id_distributor = '$id_distributor'");
            }
        }

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $id_contact = $row['id_contact'];
            $getBadScore = mysqli_query($conn, "SELECT * FROM tb_bad_score WHERE id_contact = '$id_contact'");
            $rowBadscore = $getBadScore->fetch_array(MYSQLI_ASSOC);

            if ($rowBadscore) {
                if ($rowBadscore['is_approved'] != 1) {
                    $transArray[] = $row;
                }
            } else {
                $transArray[] = $row;
            }
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

        $getContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_contact = '$id'");
        $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);
        $nama = $_POST['nama'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $store_owner = $_POST['owner_name'];
        $id_city = $_POST['id_city'];
        $mapsUrl = $_POST['mapsUrl'];
        $address = $_POST['address'];
        $nomor_hp = $_POST['nomorhp'];
        $nomor_hp2 = $_POST['nomorhp_2'];
        $status = $_POST['status'];
        $id_distributor = $rowContact['id_distributor'];
        $payment_method = $_POST['payment_method'];
        $tagih_mingguan = $_POST['tagih_mingguan'];
        $hari_bayar = "-";
        if (isset($_POST['hari_bayar'])) {
            $hari_bayar = $_POST['hari_bayar'];
        }
        if ($id_city == 0) {
            $response = ["response" => 200, "status" => "failed", "message" => "ID CITY 0!"];
            echo json_encode($response);
            die;
        }
        // NEW
        $termin_payment = $_POST['termin_payment'];
        if (isset($_POST['id_promo'])) {
            $id_promo = $_POST['id_promo'];
        } else {
            $id_promo = 0;
        }

        if ($tagih_mingguan == 0) {
            mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1 WHERE id_contact = '$id' AND type_rencana = 'tagih_mingguan'");
        }

        $reputation = $_POST['reputation'];

        $nomor_cat_1 = isset($_POST['nomor_cat_1']) ? $_POST['nomor_cat_1'] : '';
        $nomor_cat_2 = isset($_POST['nomor_cat_2']) ? $_POST['nomor_cat_2'] : '';

        if (isset($_FILES['ktp']['name'])) {
            $proof_closing = $_FILES['ktp']['name'];
            $dateFile = date("Y-m-d-H-i-s");

            if (move_uploaded_file($_FILES['ktp']['tmp_name'], 'img/' . $dateFile . $_FILES['ktp']['name'])) {
                $sourceImage = 'img/' . $dateFile . $_FILES['ktp']['name'];
                $imageDestination = 'img/min-' . $dateFile . $_FILES['ktp']['name'];
                $createImage = imagecreatefromjpeg($sourceImage);
                imagejpeg($createImage, $imageDestination, 60);
            }

            $imgNewName = 'min-' . $dateFile . $_FILES['ktp']['name'];

            if ($id_distributor != 3) {

                $jmlVoucher = 5;
                $curl = curl_init();

                curl_setopt_array(
                    $curl,
                    array(
                        CURLOPT_URL => 'https://saleswa.topmortarindonesia.com/insertVoucher.php?j=' . $jmlVoucher . '&s=' . $id,
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

                $statusRes = $res['status'];
                // $status = "ok";

                if ($statusRes == 'ok') {
                    $voucherArr = array();
                    $dateNow = date("m-d");
                    $getVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_contact = '$id' AND is_claimed = 0 AND date_voucher LIKE '%$dateNow%' ");
                    while ($rowVoucher = $getVoucher->fetch_array(MYSQLI_ASSOC)) {
                        $voucherArr[] = $rowVoucher;
                    }
                    $vouchers = "";
                    foreach ($voucherArr as $voucherArr) {
                        $vouchers .= $voucherArr['no_voucher'] . ",";
                    }

                    $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
                    $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
                    $integration_id = $rowQontak['integration_id'];
                    $wa_token = $rowQontak['token'];
                    $template_id = "77b9cbfa-4ea7-48d6-a081-da07e7901802";

                    $message = "Terimakasih telah bargabung menjadi bagian dari TOP Mortar! Nikmati layanan kilat 1 hari kerja 'Pesan Hari Ini, Kirim Hari Ini' hanya dengan pembelian 10 sak. Nantikan promo-promo menarik lainnya Bersama Top Mortar, mari kita maju bersama! Selamat anda kode voucher. Tukarkan voucher anda dengan produk-produk unggulan kami sebelum tanggal " . date("d M, Y", strtotime("+30 days")) . ". Kode Voucher: " . $vouchers;
                    // Send message
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
                                            "format":"VIDEO",
                                            "params": [
                                                {
                                                    "key":"url",
                                                    "value":"https://saleswa.topmortarindonesia.com/vids/send_voucher.png"
                                                },
                                                {
                                                    "key":"filename",
                                                    "value":"bday.mp4"
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

                    // echo $response;
                }
            }
        } else {
            $imgNewName = $rowContact['ktp_owner'];
        }

        $updatedAt = date('Y-m-d H:i:s');

        $result = mysqli_query($conn, "UPDATE tb_contact SET nama = '$nama', tgl_lahir = '$tgl_lahir', store_owner = '$store_owner', id_city = '$id_city', maps_url = '$mapsUrl', address = '$address', nomorhp = '$nomor_hp', termin_payment = $termin_payment, ktp_owner = '$imgNewName', id_promo = '$id_promo', reputation = '$reputation', payment_method = '$payment_method', store_status = '$status', tagih_mingguan = $tagih_mingguan, nomorhp_2 = '$nomor_hp2', nomor_cat_1 = '$nomor_cat_1', nomor_cat_2 = '$nomor_cat_2', updated_at = '$updatedAt' hari_bayar = '$hari_bayar' WHERE id_contact = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data kontak!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data kontak!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $nama = $_POST['nama'];
        $nomor_hp = $_POST['nomorhp'];
        $store_owner = $_POST['owner_name'];
        $tgl_lahir = '0000-00-00';
        if (isset($_POST['tgl_lahir'])) {
            $tgl_lahir = $_POST['tgl_lahir'];
        }
        $id_city = $_POST['id_city'];
        $mapsUrl = $_POST['mapsUrl'];
        $termin_payment = $_POST['termin_payment'];
        $nomor_cat_1 = isset($_POST['nomor_cat_1']) ? $_POST['nomor_cat_1'] : '';
        $address = $_POST['address'];
        // $reputation = $_POST['reputation'];

        $getCity = mysqli_query($conn, "SELECT * FROM tb_city WHERE id_city = '$id_city'");
        $rowCity = $getCity->fetch_array(MYSQLI_ASSOC);

        $id_distributor = $rowCity['id_distributor'];

        $resultCek = mysqli_query($conn, "SELECT * FROM tb_contact WHERE nomorhp = '$nomor_hp'");
        $rowCek = $resultCek->fetch_array(MYSQLI_ASSOC);

        $id_user = $_POST['id_user'];

        if ($id_distributor != 4) {
            if ($rowCek == null) {
                $result = mysqli_query($conn, "INSERT INTO tb_contact(nama, nomorhp, store_owner, tgl_lahir, id_city, maps_url,termin_payment, nomor_cat_1,address) VALUES('$nama', '$nomor_hp','$store_owner', '$tgl_lahir', $id_city, '$mapsUrl', $termin_payment, '$nomor_cat_1','$address')");
                $id_contact = mysqli_insert_id($conn);

                if ($result) {
                    $insertRenvi = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,0,'passive',$id_distributor,0)");

                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data kontak!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data kontak!", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
                // mysqli_close($conn);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Nomor hp sudah terdaftar!"];
                echo json_encode($response);
            }
        } else {
            $result = mysqli_query($conn, "INSERT INTO tb_contact(nama, nomorhp, store_owner, tgl_lahir, id_city, maps_url,termin_payment, nomor_cat_1,address) VALUES('$nama', '$nomor_hp','$store_owner', '$tgl_lahir', $id_city, '$mapsUrl', $termin_payment, '$nomor_cat_1','$address')");
            $id_contact = mysqli_insert_id($conn);

            if ($result) {
                $insertRenvi = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,0,'passive',$id_distributor,0)");

                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data kontak!"];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data kontak!", "detail" => mysqli_error($conn)];
                echo json_encode($response);
            }
        }
    }
}
