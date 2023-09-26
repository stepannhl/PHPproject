<?php
include "main.php";

function logout(): void 
{
    $user = new UserHandler();
    $html_handler = new HTMLHandler();
    $user->logout();
    $html_handler->redirectToLoginPage();
}

echo logout();