<?php
include "main.php";

function customer_order_page(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/customer-order-page.html";
    redirect_unwanted_user_to_login_page(CUSTOMER_ROLE_ID);

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $order_id = $_GET["order_id"];
        $order_handler = new OrderHandler();
        $data = [
            "order_data" => $order_handler->getHumanReadableData($order_id),
            "order_id" => $order_id,
        ];
        return $html_handler->getHtml($template_path, $data);
    }

    return $html_handler->getHtml($template_path);
}

echo customer_order_page();