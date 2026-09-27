<?php

require_once __DIR__ . "/../config/Database.php";

class Notice
{
    private $conn;
    private $table = "notices";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Sri Lanka time, independent of the PHP/MySQL server time zone
    public static function now()
    {
        return new DateTime("now", new DateTimeZone("Asia/Colombo"));
    }

    public function create($data)
    {
        $query = "
            INSERT INTO {$this->table}
                (title, message, notice_type, scope, district, published_by, published_at, expires_at)
            VALUES
                (:title, :message, :notice_type, 'district', :district, :published_by, :published_at, :expires_at)
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            "title" => $data["title"],
            "message" => $data["message"],
            "notice_type" => $data["notice_type"],
            "district" => $data["district"],
            "published_by" => $data["published_by"],
            "published_at" => self::now()->format("Y-m-d H:i:s"),
            "expires_at" => $data["expires_at"]
        ]);
    }

    public function getByDistrict($district)
    {
        $query = "
            SELECT n.notice_id, n.title, n.message, n.notice_type, n.published_at, n.expires_at,
                   CONCAT(u.first_name, ' ', u.last_name) AS published_by_name
            FROM {$this->table} n
            JOIN users u ON u.user_id = n.published_by
            WHERE n.scope = 'district' AND n.district = :district
            ORDER BY n.published_at DESC, n.notice_id DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["district" => $district]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // National notices plus the district's notices that have not expired; alerts first
    public function getVisibleForDistrict($district, $limit = 5)
    {
        $query = "
            SELECT notice_id, title, message, notice_type, scope, district, published_at
            FROM {$this->table}
            WHERE (scope = 'national' OR (scope = 'district' AND district = :district))
              AND (expires_at IS NULL OR expires_at > :now)
            ORDER BY (notice_type = 'alert') DESC, published_at DESC, notice_id DESC
            LIMIT :limit_rows
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":district", (string) $district, PDO::PARAM_STR);
        $stmt->bindValue(":now", self::now()->format("Y-m-d H:i:s"), PDO::PARAM_STR);
        $stmt->bindValue(":limit_rows", (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteInDistrict($noticeId, $district)
    {
        $query = "
            DELETE FROM {$this->table}
            WHERE notice_id = :id AND scope = 'district' AND district = :district
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id" => $noticeId, "district" => $district]);

        return $stmt->rowCount() > 0;
    }
}
