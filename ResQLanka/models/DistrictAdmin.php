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
            SELECT user_id, first_name, last_name, username, email, phone, nic, district, status
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
            SELECT user_id, first_name, last_name, username, date_of_birth, gender, email, phone,
                   address, office_contact, district, nic, status
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

    public function usernameExists($username, $excludeId = 0)
    {
        $query = "
            SELECT user_id
            FROM {$this->table}
            WHERE username = :username AND user_id <> :exclude_id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["username" => $username, "exclude_id" => $excludeId]);

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
        // Same columns as User::register(); emergency contact, tier and points are unused for administrators
        $query = "
            INSERT INTO {$this->table}
            (
                first_name, last_name, date_of_birth, gender, username, email, password,
                phone, address, office_contact, district, occupation, nic,
                emergency_contact_name, emergency_contact_phone,
                role, tier, points, status
            )
            VALUES
            (
                :first_name, :last_name, :date_of_birth, :gender, :username, :email, :password,
                :phone, :address, :office_contact, :district, 'District Administrator', :nic,
                '', '',
                'district_admin', 'Bronze', 0, 'active'
            )
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "date_of_birth" => $data["date_of_birth"],
            "gender" => $data["gender"],
            "username" => $data["username"],
            "email" => $data["email"],
            "password" => $data["password"],
            "phone" => $data["phone"],
            "address" => $data["address"],
            "office_contact" => $data["office_contact"],
            "district" => $data["district"],
            "nic" => $data["nic"]
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
                office_contact = :office_contact,
                district = :district,
                nic = :nic,
                status = :status
            WHERE user_id = :id AND role = :role
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "date_of_birth" => $data["date_of_birth"],
            "gender" => $data["gender"],
            "username" => $data["username"],
            "email" => $data["email"],
            "phone" => $data["phone"],
            "address" => $data["address"],
            "office_contact" => $data["office_contact"],
            "district" => $data["district"],
            "nic" => $data["nic"],
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
