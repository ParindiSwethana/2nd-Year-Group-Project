<?php

require_once __DIR__ . "/../config/Database.php";

class User
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function login($identifier)
    {
        $query = "SELECT * FROM users
          WHERE username = :username
          OR email = :email
          LIMIT 1";

        $stmt = $this->conn->prepare($query);
        
        $stmt->execute([
            "username" => $identifier,
            "email" => $identifier
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function usernameExists($username)
    {
        $query = "
            SELECT user_id
            FROM {$this->table}
            WHERE username = :username
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            "username" => $username
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function emailExists($email)
    {
        $query = "
            SELECT user_id
            FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            "email" => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function nicExists($nic)
    {
        $query = "
            SELECT user_id
            FROM {$this->table}
            WHERE nic = :nic
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            "nic" => $nic
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function register($data)
    {
        $query = "
            INSERT INTO {$this->table}
            (
                first_name,
                last_name,
                date_of_birth,
                gender,
                username,
                email,
                password,
                phone,
                address,
                district,
                occupation,
                nic,
                emergency_contact_name,
                emergency_contact_phone,
                role,
                tier,
                points,
                status
            )
            VALUES
            (
                :first_name,
                :last_name,
                :date_of_birth,
                :gender,
                :username,
                :email,
                :password,
                :phone,
                :address,
                :district,
                :occupation,
                :nic,
                :emergency_contact_name,
                :emergency_contact_phone,
                'registered_user',
                'Bronze',
                0,
                'active'
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
            "district" => $data["district"],
            "occupation" => $data["occupation"],
            "nic" => $data["nic"],
            "emergency_contact_name" =>
                $data["emergency_contact_name"],
            "emergency_contact_phone" =>
                $data["emergency_contact_phone"]
        ]);
    }
}
