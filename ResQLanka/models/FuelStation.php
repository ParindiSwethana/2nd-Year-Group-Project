<?php
require_once __DIR__ . "/../config/database.php";

class FuelStation
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    private function periodStart(): string
    {
        return date("Y-m-d ") . ((int) date("H") < 12 ? "00:00:00" : "12:00:00");
    }

    private function addVoteSummary(array $station): array
    {
        $stmt = $this->conn->prepare(
            "SELECT
                COALESCE(SUM(vote = 'available'), 0) AS available_votes,
                COALESCE(SUM(vote = 'out_of_stock'), 0) AS out_votes,
                MAX(voted_at) AS latest_vote_at
             FROM fuel_votes
             WHERE station_id = :station_id
             AND voted_at >= :period_start"
        );
        $stmt->execute([
            "station_id" => $station["station_id"],
            "period_start" => $this->periodStart()
        ]);
        $counts = $stmt->fetch();
        $available = (int) $counts["available_votes"];
        $out = (int) $counts["out_votes"];
        $status = "unknown";

        if ($available > $out) {
            $status = "available";
        } elseif ($out > $available) {
            $status = "out_of_stock";
        } elseif ($available > 0) {
            $latest = $this->conn->prepare(
                "SELECT vote FROM fuel_votes
                 WHERE station_id = :station_id
                 AND voted_at >= :period_start
                 ORDER BY voted_at DESC
                 LIMIT 1"
            );
            $latest->execute([
                "station_id" => $station["station_id"],
                "period_start" => $this->periodStart()
            ]);
            $status = $latest->fetchColumn() ?: "unknown";
        }

        $station["available_votes"] = $available;
        $station["out_votes"] = $out;
        $station["current_status"] = $status;
        $station["status_updated_at"] = $counts["latest_vote_at"];
        return $station;
    }

    public function search(string $location = "", string $type = "", ?string $district = null, int $limit = 20): array
    {
        $sql = "SELECT fs.* FROM fuel_stations fs WHERE fs.is_active = 1";
        $params = [];

        if ($location !== "") {
            $sql .= " AND (fs.station_name LIKE :location OR fs.address LIKE :location2 OR fs.city LIKE :location3 OR fs.district LIKE :location4)";
            $term = "%" . $location . "%";
            $params = ["location" => $term, "location2" => $term, "location3" => $term, "location4" => $term];
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
        $stations = $stmt->fetchAll();

        foreach ($stations as &$station) {
            $station = $this->addVoteSummary($station);
        }
        unset($station);
        return $stations;
    }

    public function find(int $stationId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM fuel_stations WHERE station_id = :id AND is_active = 1 LIMIT 1");
        $stmt->execute(["id" => $stationId]);
        $station = $stmt->fetch();
        return $station ? $this->addVoteSummary($station) : null;
    }

    public function getTypes(): array
    {
        $stmt = $this->conn->query("SELECT DISTINCT station_type FROM fuel_stations WHERE is_active = 1 AND station_type <> '' ORDER BY station_type");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function vote(int $stationId, ?int $userId, string $voterToken, string $vote): void
    {
        if (!in_array($vote, ["available", "out_of_stock"], true)) {
            throw new InvalidArgumentException("Invalid fuel vote.");
        }

        $periodStart = $this->periodStart();
        $this->conn->beginTransaction();

        try {
            if ($userId !== null) {
                $find = $this->conn->prepare("SELECT vote, voted_at FROM fuel_votes WHERE station_id = :station_id AND user_id = :user_id LIMIT 1 FOR UPDATE");
                $find->execute(["station_id" => $stationId, "user_id" => $userId]);
            } else {
                $find = $this->conn->prepare("SELECT vote, voted_at FROM fuel_votes WHERE station_id = :station_id AND voter_token = :voter_token LIMIT 1 FOR UPDATE");
                $find->execute(["station_id" => $stationId, "voter_token" => $voterToken]);
            }

            $existing = $find->fetch();

            if ($existing && $existing["voted_at"] >= $periodStart && $existing["vote"] === $vote) {
                $this->conn->commit();
                return;
            }

            if ($existing) {
                if ($userId !== null) {
                    $stmt = $this->conn->prepare("UPDATE fuel_votes SET vote = :vote, voted_at = NOW() WHERE station_id = :station_id AND user_id = :user_id");
                    $stmt->execute(["vote" => $vote, "station_id" => $stationId, "user_id" => $userId]);
                } else {
                    $stmt = $this->conn->prepare("UPDATE fuel_votes SET vote = :vote, voted_at = NOW() WHERE station_id = :station_id AND voter_token = :voter_token");
                    $stmt->execute(["vote" => $vote, "station_id" => $stationId, "voter_token" => $voterToken]);
                }
            } else {
                $stmt = $this->conn->prepare("INSERT INTO fuel_votes (station_id, user_id, voter_token, vote, voted_at) VALUES (:station_id, :user_id, :voter_token, :vote, NOW())");
                $stmt->execute([
                    "station_id" => $stationId,
                    "user_id" => $userId,
                    "voter_token" => $userId === null ? $voterToken : null,
                    "vote" => $vote
                ]);
            }

            $summary = $this->addVoteSummary(["station_id" => $stationId]);
            $update = $this->conn->prepare("UPDATE fuel_stations SET fuel_status = :status, status_updated_at = NOW(), updated_at = NOW() WHERE station_id = :station_id");
            $update->execute(["status" => $summary["current_status"], "station_id" => $stationId]);
            $this->conn->commit();
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
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
