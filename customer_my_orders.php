<?php
include "main.php";

function customer_my_orders(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/customer-my-orders.html";
    redirect_unwanted_user_to_login_page(CUSTOMER_ROLE_ID);
    $orders_of_user_data = get_orders_of_user_data();
    $data = ["orders_of_user_data" => $orders_of_user_data];
    return $html_handler->getHtml($template_path, $data);
}

echo customer_my_orders();