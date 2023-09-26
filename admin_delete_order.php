<?php
include "main.php";

function admin_delete_order(): void
{
    $db = new DataBase();
    if (
        $_SERVER["REQUEST_METHOD"] == "GET"
        && isset($_GET["order-id"])
    ) {
        $db->deleteRecordByAttr("material_order", "id", $_GET["order-id"]);
    } else {
        $db->deleteAllRecords("material_order");
    }
    $html_handler = new HTMLHandler();
    $html_handler->redirectToAdminSuccess();
}

admin_delete_order();
