<?php

/**
 * Класс для взаимодействия с базой данных.
 */
class DataBase
{
    /**
     * MySQLi 
     */
    public $mysqli;

    public function __construct()
    {
        $this->mysqli = $this->getMySQLi();
    }

    /**
     * Получить ИД последнего вставленного в таблицу БД значения.
     */
    public function getLastInsertedId(string $table_name): int
    {
        $query = "SELECT MAX(id) FROM {$table_name}";
        $last_inserted_id_query = $this->mysqli->query($query);
        $last_inserted_id = array_values($last_inserted_id_query->fetch_assoc())[0];
        return $last_inserted_id;
    }


    /**
     * Получить значение атрибута объекта в таблице по идентификатору из БД.
     */
    public function getPropertyByAttrId(
        string $attr,
        int $attr_id,
        string $property,
        string $table_name
    ): string {
        $query = "SELECT {$property} 
                  FROM {$table_name} 
                  WHERE {$attr} = {$attr_id}";
        $property_query = $this->mysqli->query($query);
        $resulted_property = $property_query->fetch_assoc()[$property];
        return $resulted_property;
    }

    /**
     * Получить все атрибуты объекта в таблице по идентификатору из БД.
     */
    public function getObjectById(int $id, string $table_name): array
    {
        $query = "SELECT * FROM {$table_name} WHERE id = {$id}";
        $object_query = $this->mysqli->query($query);
        $object = $object_query->fetch_assoc();
        return $object;
    }

    /**
     * Получить все объекты таблицы из БД.
     */
    public function getAllObjects(
        string $table_name,
        string $property_name = null
    ): array {
        if (is_null($property_name)) {
            $query = "SELECT * FROM {$table_name}";
        } else {
            $query = "SELECT {$property_name} FROM {$table_name}";
        }
        $objects_query = $this->mysqli->query($query);
        $objects = [];
        while ($this_object = $objects_query->fetch_assoc()) {
            array_push($objects, $this_object);
        }
        return $objects;
    }

    /**
     * Удалить запись из таблицы в БД.
     */
    public function deleteRecordByAttr(
        string $table_name,
        string $attr,
        string $attr_value
    ) {
        $query = "DELETE FROM {$table_name}
                  WHERE {$attr} = {$attr_value}";
        $this->mysqli->query($query);
    }

    /**
     * Удалить все записи из таблицы в БД.
     */
    public function deleteAllRecords(string $table_name)
    {
        $query = "DELETE FROM {$table_name}";
        $this->mysqli->query($query);
    }

    /**
     * Получить MySQLi для соединения с базой данной MySQL. 
     */
    private function getMySQLi(): mysqli
    {
        $host = "localhost";
        $dbname = "non_metallic_materials";
        $username = "root";
        $password = "";

        $mysqli = new mysqli($host, $username, $password, $dbname);

        if ($mysqli->connect_errno) {
            die("Ошибка с соединением {$mysqli->connect_error}");
        }

        // Обеспечивает работу с кириллицей.
        $mysqli->set_charset("utf8");

        return $mysqli;
    }
}
