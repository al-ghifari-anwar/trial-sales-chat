<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = '85f17083-255d-4340-af32-5dd22f483960';
// $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

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
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE tb_contact.id_city = '$id_city' AND store_status = '$status'");
            } else {
                $result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE tb_contact.id_city = '$id_city'");
            }
        } else {
            $id_distributor = $_GET['dst'];
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $result = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status = '$status' AND id_distributor = '$id_distributor'");
            } else {
                $result = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_distributor = '$id_distributor'");
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
        $status = $_POST['status'];
        $id_distributor = $rowContact['id_distributor'];
        // NEW
        $termin_payment = $_POST['termin_payment'];
        if (isset($_POST['id_promo'])) {
            $id_promo = $_POST['id_promo'];
        } else {
            $id_promo = 0;
        }

        $reputation = $_POST['reputation'];

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

            $status = $res['status'];

            if ($status == 'ok') {
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

                $message = "Terimakasih telah bargabung menjadi bagian dari TOP Mortar! Nikmati layanan kilat 1 hari kerja 'Pesan Hari Ini, Kirim Hari Ini' hanya dengan pembelian 10 sak. Nantikan promo-promo menarik lainnya Bersama Top Mortar, mari kita maju bersama! Selamat anda kode voucher. Tukarkan voucher anda dengan produk-produk unggulan kami sebelum tanggal " . date("d M, Y", strtotime("+30 days")) . ". Kode Voucher: " . $vouchers;
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
                            "value_text": "' . "Automated Message" . '"
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
            }
        } else {
            $imgNewName = $rowContact['ktp_owner'];
        }

        $result = mysqli_query($conn, "UPDATE tb_contact SET nama = '$nama', tgl_lahir = '$tgl_lahir', store_owner = '$store_owner', id_city = '$id_city', maps_url = '$mapsUrl', address = '$address', store_status = '$status', nomorhp = '$nomor_hp', termin_payment = $termin_payment, ktp_owner = '$imgNewName', id_promo = '$id_promo', reputation = '$reputation' WHERE id_contact = '$id'");

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
        $id_city = $_POST['id_city'];
        $mapsUrl = $_POST['mapsUrl'];
        $address = $_POST['address'];
        $status = $_POST['status'];
        // $reputation = $_POST['reputation'];

        $result = mysqli_query($conn, "INSERT INTO tb_contact(nama, nomorhp, id_city, maps_url, address,store_status) VALUES('$nama', '$nomor_hp','$id_city', '$mapsUrl', '$address','$status')");

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
