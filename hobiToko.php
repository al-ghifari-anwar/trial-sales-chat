<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // 
    $id_contact = $_GET['id_contact'];

    $getHobi = mysqli_query($conn, "SELECT * FROM tb_hobi_toko JOIN tb_hobi ON tb_hobi.id_hobi = tb_hobi_toko.id_hobi WHERE id_contact = '$id_contact' ORDER BY tb_hobi.name_hobi ASC");

    $hobis = array();
    while ($rowHobi = $getHobi->fetch_array(MYSQLI_ASSOC)) {
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
        die;
    } else {
        echo json_encode(array("status" => "ok", "results" => $hobis));
        die;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_contact = $_POST['id_contact'];
    $id_hobis = explode(',', $_POST['id_hobis']);
    $id_hobis_search = $_POST['id_hobis'];
    $id_user = $_POST['id_user'];

    $deletekHobi = mysqli_query($conn, "DELETE FROM tb_hobi_toko WHERE id_contact = '$id_contact' ");

    if ($deletekHobi) {
        foreach ($id_hobis as $id_hobi) {
            $save = mysqli_query($conn, "INSERT INTO tb_hobi_toko(id_contact,id_hobi,id_user) VALUES($id_contact,$id_hobi,$id_user)");

            if (!$save) {
                // $response = ["response" => 200, "status" => "failed", "message" => "Gagal harap coba lagi"];
                // echo json_encode($response);
                continue;
            } else {
                // $response = ["response" => 200, "status" => "success", "message" => "Berhasil menambahkan dat hobi toko"];
                // echo json_encode($response);
                continue;
            }
        }
    }

    $response = ["response" => 200, "status" => "success", "message" => "Berhasil menambahkan hobi toko"];
    echo json_encode($response);
}
