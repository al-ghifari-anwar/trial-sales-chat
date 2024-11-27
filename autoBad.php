<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getInvoice = mysqli_query($conn, "SELECT id_contact, MAX(id_invoice) AS id_invoice, termin_payment, MAX(date_invoice) AS date_invoice FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE status_invoice = 'waiting' GROUP BY tb_surat_jalan.id_contact");

    while ($rowInvoice = $getInvoice->fetch_array(MYSQLI_ASSOC)) {
        $invoiceArray[] = $rowInvoice;
    }

    foreach ($invoiceArray as $invoice) {
        $id_invoice = $invoice['id_invoice'];
        // Calculate sisa hari jatuh tempo
        $jatuhTempo = date('d M Y', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
        $date1 = new DateTime(date("Y-m-d"));
        $date2 = new DateTime($jatuhTempo);
        $days  = $date2->diff($date1)->format('%a');
        $operan = "";
        if ($date1 < $date2) {
            $operan = "-";
        }
        $days = $operan . $days;

        if ($days >= 45) {
            $response = ["response" => 200, "status" => "ok", "message" => "Store is bad", "id_invoice" => $id_invoice];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "ok", "message" => "Store is not bad"];
            echo json_encode($response);
        }
    }
}
