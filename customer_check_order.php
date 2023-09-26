<?php
include "main.php";

function customer_check_order(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/customer-check-order.html";
    redirect_unwanted_user_to_login_page(CUSTOMER_ROLE_ID);

    $order = new Order(
        $_GET["material-id"],
        $_GET["volume"],
        $_GET["date-of-delivery"],
        $_GET["time-of-delivery"],
        $_GET["can-truck-go-through"],
        $_GET["is-there-credit"],
        $_GET["address"],
    );

    $data = [
        "order" => $order->getHumanReadableData()
    ];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["submit-btn"])) {
            try {
                $order->saveToDB();
                header("Location: customer_successful_order.php");
                exit;
            } catch (Exception $exception) {
                $data = ["error_message" => $exception->getMessage()];
                return $html_handler->getHtml($template_path, $data);
            }
        }
    }

    return $html_handler->getHtml($template_path, $data);
}

echo customer_check_order();