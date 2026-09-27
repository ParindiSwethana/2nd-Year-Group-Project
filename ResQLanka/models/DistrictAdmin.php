<?php

require_once __DIR__ . "/../config/Database.php";

class DistrictAdmin
{
    private $conn;
    private $table = "users";
    private $role = "district_admin";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAll($district = "")
    {
        $query = "
            SELECT user_id, first_name, last_name, email, phone, nic, district, status
            FROM {$this->table}
            WHERE role = :role
        ";
        $params = ["role" => $this->role];

        if ($district !== "") {
            $query .= " AND district = :district";
            $params["district"] = $district;
        }

        $query .= " ORDER BY district, first_name, last_name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "
            SELECT user_id, first_name, last_name, date_of_birth, gender, email, phone,
                   address, district, nic, emergency_contact_name, emergency_contact_phone, status
            FROM {$this->table}
            WHERE user_id = :id AND role = :role
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id" => $id, "role" => $this->role]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // $excludeId lets an account keep its own email/NIC when it is edited
    public function emailExists($email, $excludeId = 0)
    {
        $query = "
            SELECT user_id
            FROM {$this->table}
            WHERE (email = :email OR username = :username) AND user_id <> :exclude_id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["email" => $email, "username" => $email, "exclude_id" => $excludeId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function nicExists($nic, $excludeId = 0)
    {
        $query = "
            SELECT user_id
            FROM {$this->table}
            WHERE nic = :nic AND user_id <> :exclude_id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["nic" => $nic, "exclude_id" => $excludeId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function create($data)
    {
        // Same columns as User::register(); tier and points are unused for administrators
        $query = "
            INSERT INTO {$this->table}
            (
                first_name, last_name, date_of_birth, gender, username, email, password,
                phone, address, district, occupation, nic,
                emergency_contact_name, emergency_contact_phone,
                role, tier, points, status
            )
            VALUES
            (
                :first_name, :last_name, :date_of_birth, :gender, :username, :email, :password,
                :phone, :address, :district, 'District Administrator', :nic,
                :emergency_contact_name, :emergency_contact_phone,
                'district_admin', 'Bronze', 0, 'active'
            )
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "date_of_birth" => $data["date_of_birth"],
            "gender" => $data["gender"],
            "username" => $data["email"],
            "email" => $data["email"],
            "password" => $data["password"],
            "phone" => $data["phone"],
            "address" => $data["address"],
            "district" => $data["district"],
            "nic" => $data["nic"],
            "emergency_contact_name" => $data["emergency_contact_name"],
            "emergency_contact_phone" => $data["emergency_contact_phone"]
        ]);
    }

    public function update($id, $data)
    {
        $query = "
            UPDATE {$this->table}
            SET first_name = :first_name,
                last_name = :last_name,
                date_of_birth = :date_of_birth,
                gender = :gender,
                username = :username,
                email = :email,
                phone = :phone,
                address = :address,
                district = :district,
                nic = :nic,
                emergency_contact_name = :emergency_contact_name,
                emergency_contact_phone = :emergency_contact_phone,
                status = :status
            WHERE user_id = :id AND role = :role
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "date_of_birth" => $data["date_of_birth"],
            "gender" => $data["gender"],
            "username" => $data["email"],
            "email" => $data["email"],
            "phone" => $data["phone"],
            "address" => $data["address"],
            "district" => $data["district"],
            "nic" => $data["nic"],
            "emergency_contact_name" => $data["emergency_contact_name"],
            "emergency_contact_phone" => $data["emergency_contact_phone"],
            "status" => $data["status"],
            "id" => $id,
            "role" => $this->role
        ]);
    }

    public function updatePassword($id, $passwordHash)
    {
        $query = "
            UPDATE {$this->table}
            SET password = :password
            WHERE user_id = :id AND role = :role
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute(["password" => $passwordHash, "id" => $id, "role" => $this->role]);
    }

    // Throws PDOException (code 23000) if other records, e.g. notices, still reference this account
    public function delete($id)
    {
        $query = "
            DELETE FROM {$this->table}
            WHERE user_id = :id AND role = :role
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id" => $id, "role" => $this->role]);

        return $stmt->rowCount() > 0;
    }
}
