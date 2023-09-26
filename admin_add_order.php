<?php
include "main.php";

function admin_add_order(): string 
{
    $html_handler = new HTMLHandler();
    $template_path = "templates/admin-add-order.html";
    redirect_unwanted_user_to_login_page(ADMIN_ROLE_ID);
    $db = new DataBase();
    $users_data = $db->getAllObjects("user");
    $materials_data = $db->getAllObjects("material");
    $data = [
        "users_data" => $users_data,
        "materials_data" => $materials_data
    ];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $order = new Order(
            material_id: $_POST["material-id"],
            volume: $_POST["volume"],
            date_of_delivery: $_POST["date-of-delivery"],
            time_of_delivery: $_POST["time-of-delivery"],
            can_truck_go_through: $_POST["can-truck-go-through"],
            is_there_credit: $_POST["is-there-credit"],
            address: $_POST["address"],
            user_id: $_POST["user-id"]
        );
        $order->saveToDB();
        $html_handler->redirectToAdminSuccess();
    }

    return $html_handler->getHtml($template_path, $data);
}

echo admin_add_order();