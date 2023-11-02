<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['u'])) {
        if ($_GET['visit'] == 0) {
            $id_user = $_GET['u'];
            $getBiddedStore = mysqli_query($conn, "SELECT * FROM tb_bid JOIN tb_contact ON tb_contact.id_contact = tb_bid.id_contact WHERE id_user = '$id_user' AND is_active = '1' AND tb_bid.id_contact NOT IN (SELECT id_contact FROM tb_visit WHERE id_user = '$id_user' GROUP BY id_contact)");

            while ($rowBiddedStore = $getBiddedStore->fetch_array(MYSQLI_ASSOC)) {
                $biddedStoreArray[] = $rowBiddedStore;
            }

            if ($biddedStoreArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $biddedStoreArray));
            }
        } else if ($_GET['visit'] == 1) {
            $id_user = $_GET['u'];
            // $getBiddedStore = mysqli_query($conn, "SELECT * FROM tb_bid JOIN tb_contact ON tb_contact.id_contact = tb_bid.id_contact WHERE id_user = '$id_user' AND is_active = '1' AND tb_bid.id_contact IN (SELECT id_contact FROM tb_visit WHERE id_user = '$id_user' GROUP BY id_contact)");
            $getBiddedStore = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' GROUP BY tb_visit.id_contact");

            while ($rowBiddedStore = $getBiddedStore->fetch_array(MYSQLI_ASSOC)) {
                $biddedStoreArray[] = $rowBiddedStore;
            }

            if ($biddedStoreArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $biddedStoreArray));
            }
        }
    }
}
