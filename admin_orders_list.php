<?php
include "main.php";

function admin_orders_list(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/admin-orders-list.html";
    redirect_unwanted_user_to_login_page(ADMIN_ROLE_ID);
    $order_handler = new OrderHandler();
    $pending_orders_data = $order_handler->getPendingData();
    $data = ["pending_orders_data" => $pending_orders_data];
    return $html_handler->getHtml($template_path, $data);
}

echo admin_orders_list();