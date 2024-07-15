<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['u'])) {
        if (isset($_GET['s'])) {
            $id_user = $_GET['u'];
            $id_contact = $_GET['s'];

            $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE id_user = '$id_user' AND tb_visit.id_contact = '$id_contact' ORDER BY date_visit DESC");

            echo mysqli_error($conn);

            while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                $visitArray[] = $rowVisit;
            }

            if ($visitArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $visitArray));
            }
        } else if (isset($_GET['g'])) {
            $id_user = $_GET['u'];
            $id_contact = $_GET['g'];

            $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_gudang ON tb_gudang.id_gudang = tb_visit.id_contact WHERE id_user = '$id_user' AND tb_visit.id_contact = '$id_contact' ORDER BY date_visit DESC");

            echo mysqli_error($conn);

            while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                $visitArray[] = $rowVisit;
            }

            if ($visitArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $visitArray));
            }
        } else {
            if (isset($_GET['cat']) && $_GET['cat'] == 'sales') {
                $id_user = $_GET['u'];
                $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' ORDER BY date_visit DESC");

                while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                    $visitArray[] = $rowVisit;
                }

                if ($visitArray == null) {
                    echo json_encode(array("status" => "empty", "results" => []));
                } else {
                    echo json_encode(array("status" => "ok", "results" => $visitArray));
                }
            } else if (isset($_GET['cat']) && $_GET['cat'] == 'courier') {
                $id_user = $_GET['u'];
                $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_gudang ON tb_gudang.id_gudang = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' ORDER BY date_visit DESC");

                while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                    $visitArray[] = $rowVisit;
                }

                if ($visitArray == null) {
                    echo json_encode(array("status" => "empty", "results" => []));
                } else {
                    echo json_encode(array("status" => "ok", "results" => $visitArray));
                }
            } else if (!isset($_GET['cat'])) {
                $id_user = $_GET['u'];
                $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' ORDER BY date_visit DESC");

                while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                    $visitArray[] = $rowVisit;
                }

                if ($visitArray == null) {
                    echo json_encode(array("status" => "empty", "results" => []));
                } else {
                    echo json_encode(array("status" => "ok", "results" => $visitArray));
                }
            }
        }
    }

    if (isset($_GET['a']) && isset($_GET['s'])) {
        $id_contact = $_GET['s'];
        $getUser = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_user ON tb_user.id_user = tb_visit.id_user WHERE id_contact = '$id_contact' GROUP BY tb_visit.id_user");

        while ($rowUser = $getUser->fetch_array(MYSQLI_ASSOC)) {
            $userArray[] = $rowUser;
        }

        if ($userArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $userArray));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_contact'])) {
        $id_contact = $_POST['id_contact'] ? $_POST['id_contact'] : 0;
        $distance_visit = $_POST['distance_visit'] ? str_replace(',', '.', $_POST['distance_visit']) : 0;
        $source = $_POST['source'];
        $laporan_visit = "[" . $source . "] " .  $_POST['laporan_visit'];
        $id_user = $_POST['id_user'] ? $_POST['id_user'] : 0;
        $type_renvi = $_POST['type_renvi'];
        $is_pay = isset($_POST['is_pay']) ? $_POST['is_pay'] : '0';

        $getUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user = '$id_user'");
        $rowUser = $getUser->fetch_array(MYSQLI_ASSOC);

        $insertVisit = false;

        if ($is_pay != "0") {
            if ($is_pay == "pay") {
                $pay_value = $_POST['pay_value'];

                $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user,is_pay,pay_value) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user,'$is_pay',$pay_value)");
            } else if ($is_pay == "pay_later") {
                $pay_date = $_POST['pay_date'];

                $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user,is_pay,pay_date) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user,'$is_pay','$pay_date')");
            } else if ($is_pay == "not_pay") {
                $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user,is_pay) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user, '$is_pay')");
            }
        } else {
            $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user)");
        }

        if ($insertVisit) {
            $visitDate = date("Y-m-d H:i:s");
            if ($type_renvi == 'jatem3') {
                $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'jatem'");

                $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem3'");
            } else {
                if ($type_renvi == 'jatem2') {
                    $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem2'");
                } else if ($type_renvi == 'jatem1') {
                    $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem1'");
                } else if ($type_renvi == 'voucher') {
                    $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = '$type_renvi'");
                } else if ($type_renvi == 'passive') {
                    $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = '$type_renvi'");
                } else {
                    if ($source == 'renvisales') {
                        $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = '$type_renvi'");
                    } else {
                        $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact'");
                    }
                }
            }

            $id_bid = $rowBid['id_bid'];
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim laporan!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan laporan! " . mysqli_error($conn), "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
    } else if (isset($_POST['id_gudang'])) {
        $id_gudang = $_POST['id_gudang'] ? $_POST['id_gudang'] : 0;
        $distance_visit = $_POST['distance_visit'] ? str_replace(',', '.', $_POST['distance_visit']) : 0;
        $laporan_visit = $_POST['laporan_visit'] ? $_POST['laporan_visit'] : '';
        $id_user = $_POST['id_user'] ? $_POST['id_user'] : 0;

        $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_gudang, $distance_visit, '$laporan_visit', 'normal', $id_user)");

        if ($insertVisit) {
            $id_bid = $rowBid['id_bid'];
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim absen!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan absen! " . mysqli_error($conn), "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
    }
}
