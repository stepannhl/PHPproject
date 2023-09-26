<?php
include_once "main.php";

function customer_get_statistics(): string
{
    redirect_unwanted_user_to_login_page(CUSTOMER_ROLE_ID);
    $user_handler = new UserHandler();
    $statistics = $user_handler->getStatistics();
    return json_encode($statistics);
}

echo customer_get_statistics();
