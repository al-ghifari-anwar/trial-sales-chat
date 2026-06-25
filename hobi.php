<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $getHobis = mysqli_query($conn, "SELECT * FROM tb_hobi WHERE name_hobi LIKE '%$search%'");

    $hobis = array();
    while ($rowHobi = $getHobis->fetch_array(MYSQLI_ASSOC)) {
        $ids = explode('/', trim($rowHobi['path_hobi'], '/'));

        $breadcrumb = array();

        foreach ($ids as $id) {

            $q = mysqli_query($conn, "SELECT name_hobi FROM tb_hobi WHERE id_hobi = '" . intval($id) . "'");

            $d = mysqli_fetch_assoc($q);

            if ($d) {
                $breadcrumb[] = $d['name_hobi'];
            }
        }

        $label = implode(' → ', $breadcrumb);

        $rowHobi['label'] = $label;
        $hobis[] = $rowHobi;
    }

    if ($hobis == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $hobis));
    }
}
