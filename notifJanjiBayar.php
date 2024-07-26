<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE is_pay = 'pay_later'");
    while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
        $visitArray[] = $rowVisit;
    }

    foreach ($visitArray as $visit) {
        $pay_date = $visit['pay_date'];
        $id_contact = $visit['id_contact'];
        $id_user = $visit['id_user'];
    }
}
