<?php
include "main.php";

function admin_successful_check(): string 
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/admin-successful-check.html";
    redirect_unwanted_user_to_login_page(ADMIN_ROLE_ID);

    if ($_SERVER["REQUEST_METHOD"] = "POST") {
        $order_handler = new OrderHandler();
        $order_handler->check(
            $_POST["order-id"],
            $_POST["user-email"],
            $_POST["order-status"],
            $_POST["admin-comment"]
        );
    }

    return $html_handler->getHtml($template_path);
}

echo admin_successful_check();