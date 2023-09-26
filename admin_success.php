<?php
include "main.php";

function admin_successful_order(): string 
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/admin-success.html";
    redirect_unwanted_user_to_login_page(ADMIN_ROLE_ID);
    return $html_handler->getHtml($template_path);
}

echo admin_successful_order();