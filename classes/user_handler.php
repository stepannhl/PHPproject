<?php

/**
 * Класс для работы с функциями, связанные с пользователем.
 */
class UserHandler
{
    private $_mysqli;

    public function __construct()
    {
        $db = new DataBase();
        $this->_mysqli = $db->mysqli;
    }

    /**
     * Проверка, что пользователь существует.
     */
    public function doesExist(string $users_email): bool
    {
        $query = "SELECT email FROM user";
        $used_emails_query = $this->_mysqli->query($query);

        while ($used_email = $used_emails_query->fetch_assoc()) {
            if ($used_email["email"] == $users_email) {
                return true;
            }
        }

        return false;
    }

    /**
     * Вернуть идентификатор авторизованного пользователя.
     */
    public function getId(): int
    {
        return $_COOKIE["user_id"];
    }

    /**
     * Получить ИДы заказов на пользователя.
     */
    public function getOrdersIds(int $user_id): array
    {
        $query = "SELECT id FROM material_order WHERE user_id={$user_id}";
        $orders_ids_query = $this->_mysqli->query($query);
        $orders_ids = [];
        while ($current_order_id = $orders_ids_query->fetch_assoc()) {
            array_push($orders_ids, $current_order_id);
        }
        return $orders_ids;
    }

    /**
     * Возвращает ИД роли у пользователя.
     */
    public function getRoleId(): int
    {
        $user_id = $this->getId();
        $query = "SELECT role_id FROM user WHERE id = {$user_id}";
        $role_id_query = $this->_mysqli->query($query);
        $role_id = $role_id_query->fetch_assoc()["role_id"];
        return $role_id;
    }

    /**
     * Получить статистику, где указано кол-во одобренных отзывов и общее кол-во отзывов.
     */
    public function getStatistics(): array
    {
        $order_handler = new OrderHandler();
        $user_id = $this->getId();
        $statistics = [
            "accepted_orders_amount" => count($order_handler->getAllUsersOrdersByTheirStatus($user_id, ORDER_SUCCESS_STATUS_ID)),
            "all_orders_amount" => count($order_handler->getAllUsersOrdersByTheirStatus($user_id))
        ];
        return $statistics;
    }

    /**
     * Зарегистрировать пользователя.
     */
    public function signup(): void
    {
        if ($_POST["password"] != $_POST["password-confirmation"]) {
            throw new Exception("Введенные пароли не совпадают.");
        }

        if ($this->doesExist($_POST["email"])) {
            throw new Exception("Пользователь уже существует!");
        }

        $query = "INSERT INTO user (name, email, password, phone_number, role_id)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->_mysqli->stmt_init();

        if (!$stmt->prepare($query)) {
            throw new Exception("Ошибка {$this->_mysqli->error}");
        }

        $customer_role_id = 1;

        $stmt->bind_param(
            "ssssi",
            $_POST["username"],
            $_POST["email"],
            $_POST["password"],
            $_POST["phone-number"],
            $customer_role_id
        );

        $stmt->execute();
    }

    /**
     * Авторизовать пользователя.
     */
    public function login(): void
    {
        $query = sprintf(
            "SELECT * FROM user
                          WHERE email = '%s'",
            $this->_mysqli->real_escape_string($_POST["email"])
        );
        $user_query = $this->_mysqli->query($query);
        $user = $user_query->fetch_assoc();

        if (is_null($user)) {
            throw new Exception("Введены некорректные данные.");
        }

        if ($this->_isAuthDataRight($user)) {
            $this->_addToCookie($user);
        } else {
            throw new Exception("Введены некорректные данные.");
        }
    }

    /**
     * Выйти из аккаунта.
     */
    public function logout(): void
    {
        $user_id_property = "user_id";
        if (isset($_COOKIE[$user_id_property])) {
            unset($_COOKIE[$user_id_property]);
            setcookie($user_id_property, "", time() - 1);
        }
    }

    /**
     * Проверить, авторизован ли пользователь. 
     */
    public function isLoggedIn(): bool
    {
        return isset($_COOKIE["user_id"]);
    }

    /**
     * Проверить, правильно ли переданы данные для авторизации. 
     */
    private function _isAuthDataRight(array $user): bool
    {
        return !is_null($user)
            && $user["password"] == $_POST["password"];
    }

    /**
     * Добавить пользователя в куки-сессию на один час. 
     */
    private function _addToCookie(array $user): void
    {
        setcookie("user_id", $user["id"], time() + 3600);
    }
}
