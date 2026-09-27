<?php

require_once __DIR__ . "/../config/database.php";

class InventoryMovement
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    public function all(string $district = "", int $limit = 200): array
    {
        $sql = "
            SELECT
                m.*,
                i.item_name,
                i.unit,
                i.district,
                CONCAT(u.first_name, ' ', u.last_name) performed_by_name
            FROM inventory_movements m
            JOIN inventory_items i
                ON i.item_id = m.item_id
            LEFT JOIN users u
                ON u.user_id = m.performed_by
        ";

        if ($district !== "") {
            $sql .= " WHERE i.district = :district";
        }

        $sql .= "
            ORDER BY
                m.created_at DESC,
                m.movement_id DESC
            LIMIT :lim
        ";

        $stmt = $this->conn->prepare($sql);

        if ($district !== "") {
            $stmt->bindValue(
                ":district",
                $district
            );
        }

        $stmt->bindValue(
            ":lim",
            $limit,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
