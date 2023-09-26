<?php
include "main.php";

function customer_orders_statistics(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/customer-orders-statistics.html";
    redirect_unwanted_user_to_login_page(CUSTOMER_ROLE_ID);
    return $html_handler->getHtml($template_path);
}

echo customer_orders_statistics();
