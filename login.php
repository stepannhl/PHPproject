<?php
include "main.php";

function login(): string 
{
    $user = new UserHandler();
    $html_handler = new HTMLHandler();

    $template_path = "templates/login.html";

    redirect_logged_in_user_to_his_homepage();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $user->login();
            $html_handler->forcedRefresh();
        } catch (Exception $exception) {
            $data = ["error_message" => $exception->getMessage()];
            return $html_handler->getHtml($template_path, $data);
        }
    }

    return $html_handler->getHtml($template_path);
}

echo login();