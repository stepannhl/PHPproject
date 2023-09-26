<?php
include "main.php";

function customer_successful_order(): string 
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/customer-successful-order.html";
    redirect_unwanted_user_to_login_page(CUSTOMER_ROLE_ID);
    return $html_handler->getHtml($template_path);
}

echo customer_successful_order();