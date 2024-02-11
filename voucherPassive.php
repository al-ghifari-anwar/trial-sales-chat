<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');
$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $getLastOrder = mysqli_query($conn, "SELECT MAX(date_closing) AS last_order, id_contact  FROM tb_surat_jalan tsj WHERE is_closing = 1 GROUP BY tsj.id_contact ");

    while ($rowLastOrder = $getLastOrder->fetch_array(MYSQLI_ASSOC)) {
        $lastOrderArr[] = $rowLastOrder;
    }

    if ($lastOrderArr != null) {
        $twoMonths = date("Y-m-d H:i:s", strtotime("-3 month"));
        // echo $twoMonths;
        foreach ($lastOrderArr as $lastOrder) {
            $id_contact = $lastOrder['id_contact'];

            $getContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE tb_contact.id_contact = '$id_contact'");
            $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);
            $reputation = $rowContact['reputation'];
            $id_distributor = $rowContact['id_distributor'];
            $nama = $rowContact['nama'];
            $nomor_hp = $rowContact['nomorhp'];

            $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
            $integration_id = $rowQontak['integration_id'];

            if ($id_distributor == 1) {
                if ($lastOrder['last_order'] < $twoMonths) {
                    // echo json_encode($rowContact);
                    $getSak = mysqli_query($conn, "SELECT SUM(qty_produk) AS total_qty FROM tb_detail_surat_jalan JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan WHERE tb_surat_jalan.id_contact = '$id_contact'");
                    $rowSak = $getSak->fetch_array(MYSQLI_ASSOC);
                    $pembelianSak = $rowSak['total_qty'];

                    // if ($id_contact == 1598) {
                    // echo json_encode($rowContact);
                    if ($pembelianSak != null) {
                        $jmlVoucher = 0;
                        if ($reputation == 'good') {
                            if ($pembelianSak <= 100) {
                                $jmlVoucher = 1;
                            } else if ($pembelianSak > 100) {
                                $jmlVoucher = 2;
                            }
                        }

                        $cekVoucherArr = null;
                        $cekVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_contact = '$id_contact' AND is_claimed = 0");
                        while ($rowCekVoucher = $cekVoucher->fetch_array(MYSQLI_ASSOC)) {
                            $cekVoucherArr[] = $rowCekVoucher;
                        }
                        // echo "ID:" . $id_contact;
                        // echo json_encode($cekVoucherArr);
                        if ($cekVoucherArr == null) {
                            // echo "Voucher sent";
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

                                $template_id = "c4504076-8fc7-44a0-9534-9f6ebc3e56e5";

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
                                                            "value":"https://saleswa.topmortarindonesia.com/img/vc_passive.jpg"
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

                                $status = $res['status'];

                                if ($status == "success") {
                                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim voucher passive!"];
                                    echo json_encode($response);
                                } else {
                                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim pesan voucher passive!"];
                                    echo json_encode($response);
                                }
                            } else {
                                $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim voucher passive!"];
                                echo json_encode($response);
                            }
                        } else {
                            $response = ["response" => 200, "status" => "failed", "message" => "Cant input voucher!", "detail" => "This store already have voucher"];
                            echo json_encode($response);
                        }
                    }
                    // }
                } else {
                    // $response = ["response" => 200, "status" => "failed", "message" => "Last order not more than 3 months!"];
                    // echo json_encode($response);
                }
            }
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "No stores!", "detail" => mysqli_error($conn)];
        echo json_encode($response);
    }
}
