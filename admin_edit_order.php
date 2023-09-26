<?php
include "main.php";

function admin_edit_order(): string
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/admin-edit-order.html";
    redirect_unwanted_user_to_login_page(ADMIN_ROLE_ID);

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $db = new DataBase();
        $data = [
            "order_data" => $db->getObjectById($_GET["order-id"], "material_order"),
            "users_data" => $db->getAllObjects("user"),
            "materials_data" => $db->getAllObjects("material"),
        ];
        return $html_handler->getHtml($template_path, $data);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $order = new Order(
            $_POST["material-id"],
            $_POST["volume"],
            $_POST["date-of-delivery"],
            $_POST["time-of-delivery"],
            $_POST["can-truck-go-through"],
            $_POST["is-there-credit"],
            $_POST["address"],
            user_id: $_POST["user-id"],
        );
        $order->updateInDB($_GET["order-id"]);
        $html_handler->redirectToAdminSuccess();
    }

    return $html_handler->getHtml($template_path);
}

echo admin_edit_order();
