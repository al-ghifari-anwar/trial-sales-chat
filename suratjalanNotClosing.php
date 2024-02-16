<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['c'])) {
        $id_city = $_GET['c'];

        $resultSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact WHERE tb_contact.id_city = '$id_city' AND tb_surat_jalan.is_closing = 0 ORDER BY id_surat_jalan DESC");

        while ($row = $resultSuratJalan->fetch_array(MYSQLI_ASSOC)) {
            $suratJalanArray[] = $row;
        }

        if ($suratJalanArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
        }
    }
}
