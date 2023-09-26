<?php
include "main.php";

function customer_make_order(): string 
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/customer-make-order.html";
    redirect_unwanted_user_to_login_page(CUSTOMER_ROLE_ID);
    $db = new DataBase();
    $materials_data = $db->getAllObjects("material");
    $data = ["materials_data" => $materials_data];
    return $html_handler->getHtml($template_path, $data);
}

echo customer_make_order();