<?php
include "main.php";

function signup(): string
{
    $user = new UserHandler();
    $html_handler = new HTMLHandler();

    $template_path = "templates/signup.html";

    redirect_logged_in_user_to_his_homepage();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $user->signup();
            $data = ["success_message" => "Регистрация успешно пройдена."];
        } catch (Exception $exception) {
            $data = ["error_message" => $exception->getMessage()];
        }
        return $html_handler->getHtml($template_path, $data);
    }

    return $html_handler->getHtml($template_path);
}

echo signup();
