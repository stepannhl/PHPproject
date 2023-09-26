<?php

/**
 * Класс для работы с заказом.
 */
class Order
{
    private $_material_id;
    private $_volume;
    private $_date_of_delivery;
    private $_time_of_delivery;
    private $_can_truck_go_through;
    private $_is_there_credit;
    private $_address;
    private $_price;
    private $_are_properties_human_readable;
    private $_user_id;

    public function __construct(
        int $material_id,
        int $volume,
        string $date_of_delivery,
        string $time_of_delivery,
        bool|null $can_truck_go_through,
        bool|null $is_there_credit,
        string $address,
        int $price = null,
        bool $are_properties_human_readable = false,
        int $user_id = null,
    ) {
        $this->_material_id = $material_id;
        $this->_volume = $volume;
        $this->_date_of_delivery = $date_of_delivery;
        $this->_time_of_delivery = $time_of_delivery;
        $this->_address = $address;
        $this->_price = $price;
        $this->_are_properties_human_readable = $are_properties_human_readable;
        $this->_user_id = $user_id;
        
        if (is_null($can_truck_go_through)) {
            $this->_can_truck_go_through = 0;
        } else {
            $this->_can_truck_go_through = $can_truck_go_through;
        }

        if (is_null($is_there_credit)) {
            $this->_is_there_credit = 0;
        } else {
            $this->_is_there_credit = $is_there_credit;
        }
    }

    /**
     * Получить данные о заказе в удобном для чтения конечному пользователю 
     * виде.
     */
    public function getHumanReadableData(): array
    {
        $db = new DataBase();

        $human_readable_data = [
            "volume" => $this->_volume,
            "date-of-delivery" => $this->_date_of_delivery,
            "address" => $this->_address,
            "type-of-material" => $db->getObjectById($this->_material_id, "material")["name"],
        ];

        if ($this->_are_properties_human_readable) {
            $human_readable_data["time-of-delivery"] = $this->_time_of_delivery;
        } else {
            $human_readable_data["time-of-delivery"] =
                $this->_getHumanReadableTimeOfDelivery($this->_time_of_delivery);
        }

        if (is_null($this->_price)) {
            $human_readable_data["price"] = $this->getTotalPrice();
        } else {
            $human_readable_data["price"] = $this->_price;
        }

        if ($this->_can_truck_go_through) {
            $human_readable_data["can-truck-go-through"] = "Да";
        } else {
            $human_readable_data["can-truck-go-through"] = "Нет";
        }

        if ($this->_is_there_credit) {
            $human_readable_data["is-there-credit"] = "Да";
        } else {
            $human_readable_data["is-there-credit"] = "Нет";
        }

        return $human_readable_data;
    }

    /**
     * Сохранить заказ в таблицу material_order базы данных.
     */
    public function saveToDB(): void
    {
        $db = new DataBase();

        $query = "INSERT INTO material_order (
                      material_id, 
                      volume, 
                      time_of_delivery, 
                      date_of_delivery, 
                      will_truck_pass, 
                      is_credit_payment, 
                      price, 
                      address,
                      user_id,
                      order_status_id
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->mysqli->stmt_init();

        if (!$stmt->prepare($query)) {
            throw new Exception($db->mysqli->error);
        }

        $user_id = null;
        if (is_null($this->_user_id)) {
            $user = new UserHandler();
            $user_id = $user->getId();
        } else {
            $user_id = $this->_user_id;
        }

        $order_pending_status_id = ORDER_PENDING_STATUS_ID;

        $stmt->bind_param(
            "iissiidsii",
            $this->_material_id,
            $this->_volume,
            $this->_getHumanReadableTimeOfDelivery(),
            $this->_date_of_delivery,
            $this->_can_truck_go_through,
            $this->_is_there_credit,
            $this->getTotalPrice(),
            $this->_address,
            $user_id,
            $order_pending_status_id
        );

        $stmt->execute();
    }


    /**
     * Обновить заказ в таблице material_order базы данных.
     */
    public function updateInDB(int $order_id): void
    {
        $query = "UPDATE material_order
                  SET
                      material_id = {$this->_material_id},
                      volume = {$this->_volume},
                      time_of_delivery = '{$this->_getHumanReadableTimeOfDelivery()}',
                      date_of_delivery = '{$this->_date_of_delivery}',
                      will_truck_pass = {$this->_can_truck_go_through},
                      is_credit_payment = {$this->_is_there_credit},
                      price = {$this->getTotalPrice()},
                      address = '{$this->_address}',
                      user_id = {$this->_user_id}
                  WHERE id = {$order_id}";
        print_r($query);
        $db = new DataBase();
        $db->mysqli->query($query);
    }

    /**
     * Получить удобно читаемое для конечного пользователя значение времени 
     * доставки.
     */
    private function _getHumanReadableTimeOfDelivery(): string
    {
        $time_of_delivery_translation = [
            "morning-time" => "8:00-15:00",
            "evening-time" => "15:00-23:00",
        ];
        return $time_of_delivery_translation[$this->_time_of_delivery];
    }

    /**
     * Получить цену за заказ.
     */
    private function getTotalPrice(): float
    {
        return $this->_volume
            * $this->_getTypeOfMaterialPrice()
            * $this->_getTimeOfDeliveryMargin()
            * $this->_getCanTruckGoThroughMargin()
            * $this->_getIsThereCreditMargin();
    }

    /**
     * Получить итоговую цену заказа.
     */
    private function _getTypeOfMaterialPrice(): int
    {
        $material_id_price = [
            SAND_MATERIAL_ID => 200,
            RUBBLE_MATERIAL_ID => 300,
            CEMENT_MATERIAL_ID => 400,
            SOIL_MATERIAL_ID => 100
        ];

        return $material_id_price[$this->_material_id];
    }

    /**
     * Получить наценку за время доставки.
     */
    private function _getTimeOfDeliveryMargin(): float
    {
        $time_of_delivery_margin = [
            "morning-time" => 1,
            "evening-time" => 1.5
        ];

        return $time_of_delivery_margin[$this->_time_of_delivery];
    }

    /**
     * Получить наценку за то, может ли грузовик проехать на место доставки.
     */
    private function _getCanTruckGoThroughMargin(): int
    {
        if ($this->_can_truck_go_through) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * Получить наценку за оплату в кредит.
     */
    private function _getIsThereCreditMargin(): float
    {
        if ($this->_is_there_credit) {
            return 1.2;
        } else {
            return 1;
        }
    }
}
