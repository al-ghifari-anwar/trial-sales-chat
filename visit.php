<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['u'])) {
        if (isset($_GET['s'])) {
            $id_user = $_GET['u'];
            $id_contact = $_GET['s'];

            $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE id_user = '$id_user' AND tb_visit.id_contact = '$id_contact' AND is_deleted = 0 ORDER BY date_visit DESC");

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
            $id_user = $_GET['u'];
            $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' AND is_deleted = 0 ORDER BY date_visit DESC");

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
    if (isset($_GET['a']) && isset($_GET['s'])) {
        $id_contact = $_GET['s'];
        $getUser = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_user ON tb_user.id_user = tb_visit.id_user WHERE id_contact = '$id_contact' AND is_deleted = 0 GROUP BY tb_visit.id_user");

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
    $id_contact = $_POST['id_contact'] ? $_POST['id_contact'] : 0;
    $distance_visit = $_POST['distance_visit'] ? $_POST['distance_visit'] : 0;
    $laporan_visit = $_POST['laporan_visit'] ? $_POST['laporan_visit'] : '';
    $id_user = $_POST['id_user'] ? $_POST['id_user'] : 0;

    // $cekBid = mysqli_query($conn, "SELECT * FROM tb_bid WHERE id_user = '$id_user' AND id_contact = '$id_contact' AND is_active = 1");
    // $rowBid = $cekBid->fetch_array(MYSQLI_ASSOC);

    // if ($rowBid != null) {
    $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,id_user) VALUES($id_contact, '$distance_visit', '$laporan_visit', $id_user)");

    if ($insertVisit) {
        $id_bid = $rowBid['id_bid'];
        // $insertAction = mysqli_query($conn, "INSERT INTO tb_action_bid(id_bid, field_action_bid) VALUES($id_bid, 'Visit to store')");

        // if ($insertAction) {
        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim laporan!"];
        echo json_encode($response);
        // } else {
        //     $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan record!", "detail" => mysqli_error($conn)];
        //     echo json_encode($response);
        // }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan laporan! " . mysqli_error($conn), "detail" => mysqli_error($conn)];
        echo json_encode($response);
    }
    // } else {
    //     $response = ["response" => 200, "status" => "failed", "message" => "Tidak bisa mengirim laporan karena belum melakukan bidding!", "detail" => mysqli_error($conn)];
    //     echo json_encode($response);
    // }
}
