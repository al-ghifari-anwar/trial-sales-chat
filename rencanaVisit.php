<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['type'] == 'jatem') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT * FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact WHERE type_rencana = 'jatem' AND tb_contact.id_city = '$id_city'");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $renvisArray[] = $rowRenvis;
            }
        }
    }
}
