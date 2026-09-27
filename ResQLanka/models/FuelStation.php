<?php
require_once __DIR__ . "/../config/database.php";

class FuelStation
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    public function search(string $location = "", string $type = "", ?string $district = null, int $limit = 20): array
    {
        $sql = "SELECT fs.*,
                       CASE
                           WHEN fs.status_updated_at IS NULL OR fs.status_updated_at < (NOW() - INTERVAL 10 HOUR) THEN 'unknown'
                           ELSE fs.fuel_status
                       END AS current_status
                FROM fuel_stations fs
                WHERE fs.is_active = 1";
        $params = [];

        if ($location !== "") {
            $sql .= " AND (fs.station_name LIKE :location OR fs.address LIKE :location2 OR fs.city LIKE :location3 OR fs.district LIKE :location4)";
            $term = "%" . $location . "%";
            $params["location"] = $term;
            $params["location2"] = $term;
            $params["location3"] = $term;
            $params["location4"] = $term;
        }

        if ($type !== "") {
            $sql .= " AND fs.station_type = :type";
            $params["type"] = $type;
        }

        if ($district !== null && $district !== "") {
            $sql .= " AND fs.district = :district";
            $params["district"] = $district;
        }

        $sql .= " ORDER BY fs.updated_at DESC LIMIT " . max(1, min($limit, 100));
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $stationId): ?array
    {
        $stmt = $this->conn->prepare("SELECT fs.*, CASE WHEN fs.status_updated_at IS NULL OR fs.status_updated_at < (NOW() - INTERVAL 10 HOUR) THEN 'unknown' ELSE fs.fuel_status END AS current_status FROM fuel_stations fs WHERE fs.station_id = :id AND fs.is_active = 1 LIMIT 1");
        $stmt->execute(["id" => $stationId]);
        $station = $stmt->fetch();
        return $station ?: null;
    }

    public function getTypes(): array
    {
        $stmt = $this->conn->query("SELECT DISTINCT station_type FROM fuel_stations WHERE is_active = 1 AND station_type <> '' ORDER BY station_type");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function vote(int $stationId, int $userId, string $vote): void
    {
        if (!in_array($vote, ["available", "out_of_stock"], true)) {
            throw new InvalidArgumentException("Invalid fuel vote.");
        }

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("INSERT INTO fuel_votes (station_id, user_id, vote, voted_at) VALUES (:station_id, :user_id, :vote, NOW()) ON DUPLICATE KEY UPDATE vote = VALUES(vote), voted_at = NOW()");
            $stmt->execute([
                "station_id" => $stationId,
                "user_id" => $userId,
                "vote" => $vote
            ]);

            $countStmt = $this->conn->prepare("SELECT SUM(vote = 'available') AS available_votes, SUM(vote = 'out_of_stock') AS out_votes FROM fuel_votes WHERE station_id = :station_id AND voted_at >= (NOW() - INTERVAL 6 HOUR)");
            $countStmt->execute(["station_id" => $stationId]);
            $counts = $countStmt->fetch();

            $status = ((int) $counts["available_votes"] >= (int) $counts["out_votes"]) ? "available" : "out_of_stock";
            $update = $this->conn->prepare("UPDATE fuel_stations SET fuel_status = :status, status_updated_at = NOW(), updated_at = NOW() WHERE station_id = :station_id");
            $update->execute(["status" => $status, "station_id" => $stationId]);
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function save(array $data, ?int $stationId = null): int
    {
        if ($stationId) {
            $sql = "UPDATE fuel_stations SET station_name=:station_name, station_type=:station_type, address=:address, city=:city, district=:district, latitude=:latitude, longitude=:longitude, fuel_status=:fuel_status, status_updated_at=NOW(), updated_at=NOW() WHERE station_id=:station_id";
            $data["station_id"] = $stationId;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
            return $stationId;
        }

        $sql = "INSERT INTO fuel_stations (station_name, station_type, address, city, district, latitude, longitude, fuel_status, status_updated_at, is_active, created_at, updated_at) VALUES (:station_name,:station_type,:address,:city,:district,:latitude,:longitude,:fuel_status,NOW(),1,NOW(),NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return (int) $this->conn->lastInsertId();
    }

    public function deactivate(int $stationId, ?string $district = null): bool
    {
        $sql = "UPDATE fuel_stations SET is_active = 0, updated_at = NOW() WHERE station_id = :id";
        $params = ["id" => $stationId];
        if ($district !== null) {
            $sql .= " AND district = :district";
            $params["district"] = $district;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
}
