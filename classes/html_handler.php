<?php

/**
 * Класс для взаимодействия PHP-HTML.
 */
class HTMLHandler
{
    /**
     * Получить HTML-файл с используемыми в PHP переменными.
     */
    public function getHtml(string $template_path, array $data = null): string
    {
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }

        ob_start();
        require($template_path);
        $html = ob_get_clean();

        return $html;
    }

    /**
     * Перенаправляет пользователя на его домашнюю страницу в зависимости от
     * его статуса (клиент или администратор).
     */
    public function redirectToUsersHomepage(int $user_role_id): void 
    {
        if ($user_role_id == CUSTOMER_ROLE_ID) {
            header("Location: customer_my_orders.php");
            exit;
        }

        if ($user_role_id == ADMIN_ROLE_ID) {
            header("Location: admin_orders_list.php");
            exit;
        }
    }

    /**
     * Перенаправляет пользователя на страницу авторизации.
     */
    public function redirectToLoginPage(): void 
    {
        header("Location: login.php");
        exit;
    }

    /**
     * Перенаправляет пользователя на страницу управления заказами у админа.
     */
    public function redirectToAdminSuccess(): void
    {
        header("Location: admin_success.php");
        exit;
    }

    /**
     * Принудительно обновляет страницу.
     */
    public function forcedRefresh():void
    {
        header('Location: '.$_SERVER['PHP_SELF']);
        exit(); 
    }
}
