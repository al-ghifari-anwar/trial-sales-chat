<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $resultInv = mysqli_query($conn, "SELECT * FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE status_invoice = 'waiting'");

    while ($rowInv = $resultInv->fetch_array(MYSQLI_ASSOC)) {
        $invArray[] = $rowInv;
    }

    foreach ($invArray as $invArray) {
        $id_invoice = $invArray['id_invoice'];
        $mingguan = $invArray['tagih_mingguan'];
        if ($mingguan == 0) {

            // Calculate sisa hari jatuh tempo
            $jatuhTempo = date('d M Y', strtotime("+" . $invArray['termin_payment'] . " days", strtotime($invArray['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;

            if (date("Y-m-d", strtotime("+" . $invArray['termin_payment'] . " days", strtotime($invArray['date_invoice']))) >= date("Y-m-d")) {
                if ($days >= "0" && $days <= "7") {
                    $getTotalPayment = mysqli_query($conn, "SELECT SUM(amount_payment + potongan_payment + adjustment_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'");
                    $rowPayment = $getTotalPayment->fetch_array(MYSQLI_ASSOC);

                    $id_contact = $invArray['id_contact'];
                    $id_surat_jalan = $invArray['id_surat_jalan'];
                    $id_distributor = $invArray['id_distributor'];
                    $id_invoice = $invArray['id_invoice'];

                    $sisaHutang = number_format($invArray['total_invoice'] - $rowPayment['amount_total'], 0, '.', ',');

                    if ($sisaHutang > 0) {
                        $cekRenvis = mysqli_query($conn, "SELECT * FROM tb_renvis_jatem WHERE id_contact = '$id_contact' AND type_renvis = 'jatem1' AND is_visited = 0");

                        // while ($rowRenvis = $cekRenvis->fetch_array(MYSQLI_ASSOC)) {
                        $renvisArray = $cekRenvis->fetch_array(MYSQLI_ASSOC);
                        // }

                        if ($renvisArray == null) {
                            $insertRenvis = mysqli_query($conn, "INSERT INTO tb_renvis_jatem(id_contact,id_surat_jalan,type_renvis,id_distributor,id_invoice) VALUES($id_contact,$id_surat_jalan,'jatem1',$id_distributor,$id_invoice)");

                            if ($insertRenvis) {
                                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan data rencana visit! jatem1, " . $days];
                                echo json_encode($response);
                            } else {
                                $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit! jatem1, " . $days . " idContact: " . $id_contact];
                                echo json_encode($response);
                            }
                        } else {
                            $response = ["message" => "Sudah ada jatem1", "days" => $days, "no_inv" => $invArray['no_invoice'] . "-id-" . $invArray['id_contact']];
                            echo json_encode($response);
                        }
                    }
                } else {
                    $response = ["message" => "Jatem masih belum", "days" => $days, "no_inv" => $invArray['no_invoice'] . "-id-" . $invArray['id_contact']];
                    echo json_encode($response);
                }
            } else if ($days > "7" && $days <= "15") {
                $getTotalPayment = mysqli_query($conn, "SELECT SUM(amount_payment + potongan_payment + adjustment_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'");
                $rowPayment = $getTotalPayment->fetch_array(MYSQLI_ASSOC);

                $id_contact = $invArray['id_contact'];
                $id_surat_jalan = $invArray['id_surat_jalan'];
                $id_distributor = $invArray['id_distributor'];
                $id_invoice = $invArray['id_invoice'];

                $sisaHutang = number_format($invArray['total_invoice'] - $rowPayment['amount_total'], 0, '.', ',');

                if ($sisaHutang > 0) {
                    $cekRenvis = mysqli_query($conn, "SELECT * FROM tb_renvis_jatem WHERE id_contact = '$id_contact' AND type_renvis = 'jatem2' AND is_visited = 0");

                    // while ($rowRenvis = $cekRenvis->fetch_array(MYSQLI_ASSOC)) {
                    $renvisArray = $cekRenvis->fetch_array(MYSQLI_ASSOC);
                    // }

                    $deleteOldRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1 WHERE id_contact = '$id_contact' AND type_renvis = 'jatem1' ");

                    if ($renvisArray == null) {
                        $insertRenvis = mysqli_query($conn, "INSERT INTO tb_renvis_jatem(id_contact,id_surat_jalan,type_renvis,id_distributor,id_invoice) VALUES($id_contact,$id_surat_jalan,'jatem2',$id_distributor,$id_invoice)");

                        if ($insertRenvis) {
                            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan data rencana visit jatem2!, " . $days];
                            echo json_encode($response);
                        } else {
                            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit jatem2!, " . $days];
                            echo json_encode($response);
                        }
                    } else {
                        $response = ["message" => "Sudah ada jatem2", "days" => $days, "no_inv" => $invArray['no_invoice'] . "-id-" . $invArray['id_contact']];
                        echo json_encode($response);
                    }
                }
            } else if ($days > "15") {
                $getTotalPayment = mysqli_query($conn, "SELECT SUM(amount_payment + potongan_payment + adjustment_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'");
                $rowPayment = $getTotalPayment->fetch_array(MYSQLI_ASSOC);

                $id_contact = $invArray['id_contact'];
                $id_surat_jalan = $invArray['id_surat_jalan'];
                $id_distributor = $invArray['id_distributor'];
                $id_invoice = $invArray['id_invoice'];

                $sisaHutang = number_format($invArray['total_invoice'] - $rowPayment['amount_total'], 0, '.', ',');

                if ($sisaHutang > 0) {
                    $cekRenvis = mysqli_query($conn, "SELECT * FROM tb_renvis_jatem WHERE id_contact = '$id_contact' AND type_renvis = 'jatem3' AND is_visited = 0");

                    // while ($rowRenvis = $cekRenvis->fetch_array(MYSQLI_ASSOC)) {
                    $renvisArray = $cekRenvis->fetch_array(MYSQLI_ASSOC);
                    // }

                    $deleteOldRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1 WHERE id_contact = '$id_contact' AND type_renvis = 'jatem2' ");

                    if ($renvisArray == null) {
                        $insertRenvis = mysqli_query($conn, "INSERT INTO tb_renvis_jatem(id_contact,id_surat_jalan,type_renvis,id_distributor,id_invoice) VALUES($id_contact,$id_surat_jalan,'jatem3',$id_distributor,$id_invoice)");

                        if ($insertRenvis) {
                            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan data rencana visit jatem3!, " . $days];
                            echo json_encode($response);
                        } else {
                            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit jatem3!, " . $days];
                            echo json_encode($response);
                        }
                    } else {
                        $response = ["message" => "Sudah ada jatem3", "days" => $days, "no_inv" => $invArray['no_invoice'] . "-id-" . $invArray['id_contact']];
                        echo json_encode($response);
                    }
                }
            } else {
                $response = ["message" => "Belum waktunya", "days" => $days, "no_inv" => $invArray['no_invoice']];
                echo json_encode($response);
            }
        }
    }
}
