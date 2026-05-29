<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_user = $_GET['id_user'];

    $user = mysqli_query($conn, " SELECT * FROM tb_user WHERE id_user = $id_user ")->fetch_array(MYSQLI_ASSOC);

    $id_city = $user['id_city'];

    // Target Calculation
    $date1 = new DateTime(date('Y-m-01'));
    $date2 = new DateTime(date('Y-m-d'));
    $date2->modify('+1 day');
    $days  = $date2->diff($date1)->format('%a');

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($date1, $interval, $date2);

    $dates = [];

    $targetVisit = $days * 10;

    // Visit Count
    $dateFrom = date('Y-m-01');
    $dateTo = date('Y-m-d');

    $getDateGroupVisit = mysqli_query($conn, " SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' AND DATE(tb_visit.date_visit) >= '$dateFrom' AND DATE(tb_visit.date_visit) <= '$dateTo' GROUP BY DATE(tb_visit.date_visit) ");

    $totalVisit = 0;
    $totalConfirmed = 0;
    $totalDayoffGlobal = 0;
    $totalDayoffUser = 0;
    $totalDayoff = 0;

    $detailVisits = array();
    $detailConfirmed = array();
    // while ($rowDateGroupVisit = $getDateGroupVisit->fetch_array(MYSQLI_ASSOC)) {
    foreach ($period as $date) {
        // $dateGroup = date('Y-m-d', strtotime($rowDateGroupVisit['date_visit']));
        $dateGroup = $date->format('Y-m-d');

        $getDayoffGlobal = mysqli_query($conn, "SELECT COALESCE(SUM(jml_day_off), 0) AS day_off_global FROM tb_day_off WHERE id_user = 0 AND date_day_off = '$dateGroup'")->fetch_array(MYSQLI_ASSOC);

        $getDayoffUser = mysqli_query($conn, "SELECT COALESCE(SUM(jml_day_off), 0) AS day_off_user FROM tb_day_off WHERE id_user = '$id_user' AND date_day_off = '$dateGroup'")->fetch_array(MYSQLI_ASSOC);

        $getTotal = mysqli_query($conn, " SELECT COUNT(*) AS total_visit FROM tb_visit JOIN tb_contact ON tb_visit.id_contact = tb_contact.id_contact WHERE tb_visit.id_user = '$id_user' AND DATE(tb_visit.date_visit) = '$dateGroup' AND tb_visit.is_deleted = 0 AND is_approved = 1 GROUP BY tb_visit.id_contact ");
        $arrayTotal = array();
        while ($rowTotal = $getTotal->fetch_array(MYSQLI_ASSOC)) {
            $arrayTotal[] = $rowTotal;
        }

        $checkYes = mysqli_query($conn, " SELECT COUNT(*) AS total_confirmed FROM tb_jadwal_visit WHERE id_city = $id_city AND date_jadwal_visit = '$dateGroup' AND is_yes = 1 ")->fetch_array(MYSQLI_ASSOC);

        $detailVisits[] = ['tgl' => $dateGroup, 'total' => count($arrayTotal)];
        $detailConfirmed[] = ['tgl' => $dateGroup, 'total' => $checkYes['total_confirmed']];

        $totalVisit += count($arrayTotal);
        $totalConfirmed += $checkYes['total_confirmed'];
        $totalDayoffGlobal += $getDayoffGlobal['day_off_global'];
        $totalDayoffUser += ($getDayoffUser > 0) ? 10 - $getDayoffUser['day_off_user'] : 0;
        $totalDayoff += $getDayoffGlobal['day_off_global'] + $getDayoffUser['day_off_user'];
    }
    // }

    $resultArray = [
        'user' => $user['full_name'],
        'target_visit' => $targetVisit - $totalDayoff . "",
        'total_dayoff' => $totalDayoff . "",
        'total_visit' => $totalVisit . "",
        'total_confirmed' => $totalConfirmed . "",
        'details' => [
            'visits' => $detailVisits,
            'confirmed' => $detailConfirmed,
        ]
    ];

    $response = ["response" => 200, "status" => "ok", "message" => "Success!", "results" => $resultArray];
    echo json_encode($response);
}
