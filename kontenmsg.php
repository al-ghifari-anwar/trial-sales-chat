<?php
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_kontenmsg'])) {
        $id_kontenmsg = $_GET['id_kontenmsg'];
        $kontenmsg = mysqli_query($conn, "SELECT * FROM tb_kontenmsg WHERE id_kontenmsg = '$id_kontenmsg'")->fetch_array(MYSQLI_ASSOC);

        if ($kontenmsg == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $kontenmsg));
        }
    } else {
        $kontenmsgs = array();

        $getKontenMsg = mysqli_query($conn, "SELECT * FROM tb_kontenmsg ORDER BY created_at DESC");

        while ($rowKontenMsg = $getKontenMsg->fetch_array(MYSQLI_ASSOC)) {
            $rowKontenMsg['link_thumbnail'] = 'https://dev-order.topmortarindonesia.com/assets/img/kontenmsg_img/' . $getKontenMsg['thumbnail_kontenmsg'];

            $kontenmsgs[] = $rowKontenMsg;
        }

        if ($kontenmsgs == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $kontenmsgs));
        }
    }
}
