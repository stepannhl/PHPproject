<?php
include "main.php";

function admin_order_page(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/admin-order-page.html";
    redirect_unwanted_user_to_login_page(ADMIN_ROLE_ID);

    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $db = new DataBase();
        $order_id = $_GET["order_id"];
        $order_handler = new OrderHandler();
        $order_data = $order_handler->getHumanReadableData($order_id);
        $user_id = $db->getPropertyByAttrId("id", $order_id, "user_id", "material_order");
        $user_data = $db->getObjectById($user_id, "user");
        $data = [
            "order_id" => $order_id,
            "order_data" => $order_data,
            "user_data" => $user_data,
        ];
        return $html_handler->getHtml($template_path, $data);
    }

    return $html_handler->getHtml($template_path);
}

echo admin_order_page();