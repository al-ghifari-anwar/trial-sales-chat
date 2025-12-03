<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_user = $_GET['id_user'];

    $dateFrom = date('Y-m-01');
    $dateTo = date('Y-m-t');

    $getUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user = '$id_user'");
    $user = $getUser->fetch_array(MYSQLI_ASSOC);

    if ($user == null) {
        $response = ["response" => 200, "status" => "failed", "message" => "User tidak ditemukan!"];
        echo json_encode($response);
    } else {
        $getDateGroupVisit = mysqli_query($conn, " SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' AND DATE(tb_visit.date_visit) >= '$dateFrom' AND DATE(tb_visit.date_visit) <= '$dateTo' GROUP BY DATE(tb_visit.date_visit) ");

        $total = 0;

        while ($rowDateGroupVisit = $getDateGroupVisit->fetch_array(MYSQLI_ASSOC)) {
            $dateGroup = date('Y-m-d', strtotime($rowDateGroupVisit['date_visit']));

            $getTotal = mysqli_query($conn, " SELECT COUNT(*) AS total_visit FROM tb_visit JOIN tb_contact ON tb_visit.id_contact = tb_contact.id_contact WHERE tb_visit.id_user = '$id_user' AND DATE(tb_visit.date_visit) = '$dateGroup' AND tb_visit.is_deleted = 0 AND is_approved = 1 ");
            $total += $getTotal['total_visit'];
        }

        $resultArray = [
            'user' => $user['full_name'],
            'target_visit' => 200 . "",
            'total_visit' => $total . "",
        ];

        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data user!", "results" => $resultArray];
        echo json_encode($response);
    }
} else {
    $response = ["response" => 200, "status" => "failed", "message" => "Not found!"];
    echo json_encode($response);
}
