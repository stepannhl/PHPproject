<?php
include "main.php";

function admin_orders_management(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/admin-orders-management.html";
    redirect_unwanted_user_to_login_page(ADMIN_ROLE_ID);
    $db = new DataBase();
    $orders_data = $db->getAllObjects("material_order");
    $data = ["orders_data" => $orders_data];
    return $html_handler->getHtml($template_path, $data);
}

echo admin_orders_management();