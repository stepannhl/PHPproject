<?php

// Файл для хранения вспомогательных функций.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Перенаправить пользователя на его домашнюю страницу, если он авторизован.
 */
function redirect_logged_in_user_to_his_homepage(): void 
{
    $db = new DataBase();
    $user = new UserHandler();
    $html_handler = new HTMLHandler();

    if ($user->isLoggedIn()) {
        $user_role_id = $user->getRoleId();
        $html_handler->redirectToUsersHomepage($user_role_id);
    }
}

/**
 * Перенаправить пользователя на другую страницу, если пользователь является
 * нежелательным (то есть не должен иметь доступ к данной странице).
 */
function redirect_unwanted_user_to_login_page(int $wanted_role_id): void
{
    $db = new DataBase();
    $user = new UserHandler();
    $html_handler = new HTMLHandler();

    // Перенаправить пользователя нежелательного статуса на его домашнюю
    // страницу.
    if ($user->isLoggedIn()) {
        $user_role_id = $user->getRoleId();
        if ($user_role_id != $wanted_role_id) {
            $html_handler->redirectToUsersHomepage($user_role_id);
        }
    }

    // Перенаправить неавторизованного пользователя на страницу авторизации.
    if (!$user->isLoggedIn()) {
        $html_handler->redirectToLoginPage();
    }
}

/**
 * Получить информацию о заказах, которые оформлены на пользователя.
 */
function get_orders_of_user_data(): array 
{
    $db = new DataBase();

    $user = new UserHandler();
    $user_id = $user->getId();
    $orders_ids = $user->getOrdersIds($user_id);

    $orders_of_user_data = [];
    foreach ($orders_ids as $this_order_id) {
        $query = "SELECT id, time_of_delivery, date_of_delivery, order_status_id
                  FROM material_order
                  WHERE id = {$this_order_id['id']}";
        $order_data_query = $db->mysqli->query($query);
        $order_data = $order_data_query->fetch_assoc();
        $order_status_id = $order_data["order_status_id"];
        $order_status = $db->getPropertyByAttrId("id", $order_status_id, "name", "order_status");
        $order_data["order_status"] = $order_status;
        array_push($orders_of_user_data, $order_data);
    }

    return $orders_of_user_data;
}

/**
 * Отправить электронное письмо.
 */
function send_email(string $email, string $message): void
{
    $recipient = $email;
    $subject = "Non Metallic Materials - Notification";

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "php.semester.4314324@gmail.com";
    $mail->Password = "obztirslnekyaeep";
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;

    $mail->setFrom("php.semester.4314324@gmail.com");
    $mail->addAddress($recipient);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;

    $mail->send();
}