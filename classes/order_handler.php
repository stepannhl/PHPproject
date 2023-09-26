<?php

/**
 * Класс для взаимодействия с функциями, связанные с заказом.
 */
class OrderHandler
{
    /**
     * Проверить заказ в админ-панели.
     */
    public function check(
        int $order_id,
        string $user_email,
        string $order_status,
        string $admin_comment
    ): void {
        $this->_parseOrderStatus($order_id, $order_status);
        send_email($user_email, $admin_comment);
    }

    /**
     * Получить все отзывы у данного пользователя по их статусу.
     */
    public function getAllUsersOrdersByTheirStatus(
        string $user_id,
        string $order_status_id = null
    ): array {
        $db = new DataBase();
        if (is_null($order_status_id)) {
            $query = "SELECT *
                      FROM material_order
                      WHERE user_id = {$user_id}";
        } else {
            $query = "SELECT *
                    FROM material_order
                    WHERE user_id = {$user_id}
                            AND order_status_id = {$order_status_id}";
        }
        $property_query = $db->mysqli->query($query);
        $resulted_objects = $property_query->fetch_all();
        return $resulted_objects;
    }

    /**
     * Получить все непроверенные заказы (то есть заказы со статусом 
     * "pending").
     */
    public function getPendingData(): array
    {
        $db = new DataBase();
        $pending_status_id = ORDER_PENDING_STATUS_ID;
        // * Реализация процедуры.
        $db->mysqli->query("DROP PROCEDURE IF EXISTS `Select Pending Data`");
        $db->mysqli->query("CREATE PROCEDURE `Select Pending Data`()
                            BEGIN
                                SELECT id, time_of_delivery, date_of_delivery
                                FROM material_order
                                WHERE order_status_id = {$pending_status_id};
                            END");
        $query = "CALL `Select Pending Data`();";
        $data_query = $db->mysqli->query($query);

        $data = [];
        while ($this_data = $data_query->fetch_assoc()) {
            array_push($data, $this_data);
        }
        return $data;
    }

    /**
     * Получить удобные для чтения данные о заказе по его идентификатору.
     */
    public function getHumanReadableData(int $order_id): array
    {
        $db = new DataBase();
        $data_as_array = $db->getObjectById($order_id, "material_order");
        $order_as_class = new Order(
            $data_as_array["material_id"],
            $data_as_array["volume"],
            $data_as_array["date_of_delivery"],
            $data_as_array["time_of_delivery"],
            $data_as_array["will_truck_pass"],
            $data_as_array["is_credit_payment"],
            $data_as_array["address"],
            $data_as_array["price"],
            true
        );
        $data_as_class = $order_as_class->getHumanReadableData();
        return $data_as_class;
    }

    /**
     * Изменить запись в таблице базы данных в зависимости от того, был принят
     * заказ или отклонён.
     */
    private function _parseOrderStatus(
        int $order_id,
        string $order_status
    ): void {
        $order_accepted = "Принять";
        $order_rejected = "Отклонить";

        $order_status_id = null;
        if ($order_status == $order_accepted) {
            $order_status_id = ORDER_SUCCESS_STATUS_ID;
        } elseif ($order_status = $order_rejected) {
            $order_status_id = ORDER_FAILURE_STATUS_ID;
        }

        $query = "UPDATE material_order
                  SET order_status_id = {$order_status_id}
                  WHERE id = {$order_id}";
        $db = new DataBase();
        $db->mysqli->query($query);
    }
}
