<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");



if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $id_city = $_GET['id_city'];

    $cluster1 = mysqli_query($conn, " SELECT COUNT(*) AS jml FROM tb_contact WHERE id_city = '$id_city' AND cluster = 1 ")->fetch_array(MYSQLI_ASSOC);
    $cluster2 = mysqli_query($conn, " SELECT COUNT(*) AS jml FROM tb_contact WHERE id_city = '$id_city' AND cluster = 2 ")->fetch_array(MYSQLI_ASSOC);
    $cluster3 = mysqli_query($conn, " SELECT COUNT(*) AS jml FROM tb_contact WHERE id_city = '$id_city' AND cluster = 3 ")->fetch_array(MYSQLI_ASSOC);

    $result = [
        'cluster_1' => $cluster1['jml'],
        'cluster_2' => $cluster2['jml'],
        'cluster_3' => $cluster3['jml'],
    ];

    echo json_encode(array("status" => "ok", "results" => $result));
    die;
}
