<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['u'])) {
        $id_user = $_GET['u'];
        $getBiddedStore = mysqli_query($conn, "SELECT * FROM tb_bid WHERE id_user = '$id_user' AND is_active = '1'");

        while ($rowBiddedStore = $getBiddedStore->fetch_array(MYSQLI_ASSOC)) {
            $biddedStoreArray[] = $rowBiddedStore;
        }
    }
}
