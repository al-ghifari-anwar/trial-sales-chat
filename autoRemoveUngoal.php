<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getContacts = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status IN ('data') ");

    while ($rowContact = $getContacts->fetch_array(MYSQLI_ASSOC)) {
        $id_contact = $rowContact['id_contact'];
        $visit = mysqli_query($conn, " SELECT COUNT(*) AS jml_visit FROM tb_visit WHERE id_contact = $id_contact ");

        if ($visit['jml_visit'] >= 8) {
            // 
        }
    }
}
