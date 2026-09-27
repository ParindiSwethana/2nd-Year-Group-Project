<?php
require_once __DIR__ . "/../config/database.php";

class FuelStation
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    private function getCurrentPeriodStart(): string
    {
        $hour = (int) date("H");

        if ($hour < 12) {
            return date("Y-m-d 00:00:00");
        }

        return date("Y-m-d 12:00:00");
    }

    private function getVoteSummary(int $stationId): array
    {
        $periodStart = $this->getCurrentPeriodStart();

        $stmt = $this->conn->prepare(
            "SELECT
                COALESCE(SUM(vote = 'available'), 0) AS available_votes,
                COALESCE(SUM(vote = 'out_of_stock'), 0) AS out_votes
             FROM fuel_votes
             WHERE station_id = :station_id
             AND voted_at >= :period_start"
        );

        $stmt->execute([
            "station_id" => $stationId,
            "period_start" => $periodStart
        ]);

        $counts = $stmt->fetch();

        $availableVotes = (int) $counts["available_votes"];
        $outVotes = (int) $counts["out_votes"];

        $status = "unknown";

        if ($availableVotes > $outVotes) {
            $status = "available";
        } elseif ($outVotes > $availableVotes) {
            $status = "out_of_stock";
        } elseif ($availableVotes > 0) {
            $latestStmt = $this->conn->prepare(
                "SELECT vote
                 FROM fuel_votes
                 WHERE station_id = :station_id
                 AND voted_at >= :period_start
                 ORDER BY voted_at DESC
                 LIMIT 1"
            );

            $latestStmt->execute([
                "station_id" => $stationId,
                "period_start" => $periodStart
            ]);

            $latestVote = $latestStmt->fetchColumn();

            if ($latestVote) {
                $status = $latestVote;
            }
        }

        return [
            "available_votes" => $availableVotes,
            "out_votes" => $outVotes,
            "current_status" => $status
        ];
    }

    public function search(
        string $location = "",
        string $type = "",
        ?string $district = null,
        int $limit = 20
    ): array {
        $sql = "SELECT fs.*
                FROM fuel_stations fs
                WHERE fs.is_active = 1";

        $params = [];

        if ($location !== "") {
            $sql .= " AND (
                fs.station_name LIKE :location
                OR fs.address LIKE :location2
                OR fs.city LIKE :location3
                OR fs.district LIKE :location4
            )";

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

        $stations = $stmt->fetchAll();

        foreach ($stations as &$station) {
            $summary = $this->getVoteSummary((int) $station["station_id"]);

            $station["available_votes"] = $summary["available_votes"];
            $station["out_votes"] = $summary["out_votes"];
            $station["current_status"] = $summary["current_status"];
        }

        unset($station);

        return $stations;
    }

    public function find(int $stationId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT fs.*
             FROM fuel_stations fs
             WHERE fs.station_id = :id
             AND fs.is_active = 1
             LIMIT 1"
        );

        $stmt->execute([
            "id" => $stationId
        ]);

        $station = $stmt->fetch();

        if (!$station) {
            return null;
        }

        $summary = $this->getVoteSummary($stationId);

        $station["available_votes"] = $summary["available_votes"];
        $station["out_votes"] = $summary["out_votes"];
        $station["current_status"] = $summary["current_status"];

        return $station;
    }

    public function getTypes(): array
    {
        $stmt = $this->conn->query(
            "SELECT DISTINCT station_type
             FROM fuel_stations
             WHERE is_active = 1
             AND station_type <> ''
             ORDER BY station_type"
        );

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function vote(int $stationId, int $userId, string $vote): void
    {
        if (!in_array($vote, ["available", "out_of_stock"], true)) {
            throw new InvalidArgumentException("Invalid fuel vote.");
        }

        $periodStart = $this->getCurrentPeriodStart();

        $this->conn->beginTransaction();

        try {
            $existingStmt = $this->conn->prepare(
                "SELECT vote, voted_at
                 FROM fuel_votes
                 WHERE station_id = :station_id
                 AND user_id = :user_id
                 LIMIT 1
                 FOR UPDATE"
            );

            $existingStmt->execute([
                "station_id" => $stationId,
                "user_id" => $userId
            ]);

            $existingVote = $existingStmt->fetch();

            if (
                $existingVote &&
                $existingVote["voted_at"] >= $periodStart &&
                $existingVote["vote"] === $vote
            ) {
                $this->conn->commit();
                return;
            }

            if ($existingVote) {
                $stmt = $this->conn->prepare(
                    "UPDATE fuel_votes
                     SET vote = :vote,
                         voted_at = NOW()
                     WHERE station_id = :station_id
                     AND user_id = :user_id"
                );
            } else {
                $stmt = $this->conn->prepare(
                    "INSERT INTO fuel_votes (
                        station_id,
                        user_id,
                        vote,
                        voted_at
                     )
                     VALUES (
                        :station_id,
                        :user_id,
                        :vote,
                        NOW()
                     )"
                );
            }

            $stmt->execute([
                "station_id" => $stationId,
                "user_id" => $userId,
                "vote" => $vote
            ]);

            $summary = $this->getVoteSummary($stationId);

            $update = $this->conn->prepare(
                "UPDATE fuel_stations
                 SET fuel_status = :status,
                     status_updated_at = NOW(),
                     updated_at = NOW()
                 WHERE station_id = :station_id"
            );

            $update->execute([
                "status" => $summary["current_status"],
                "station_id" => $stationId
            ]);

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
            $sql = "UPDATE fuel_stations
                    SET station_name = :station_name,
                        station_type = :station_type,
                        address = :address,
                        city = :city,
                        district = :district,
                        latitude = :latitude,
                        longitude = :longitude,
                        fuel_status = :fuel_status,
                        status_updated_at = NOW(),
                        updated_at = NOW()
                    WHERE station_id = :station_id";

            $data["station_id"] = $stationId;

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);

            return $stationId;
        }

        $sql = "INSERT INTO fuel_stations (
                    station_name,
                    station_type,
                    address,
                    city,
                    district,
                    latitude,
                    longitude,
                    fuel_status,
                    status_updated_at,
                    is_active,
                    created_at,
                    updated_at
                )
                VALUES (
                    :station_name,
                    :station_type,
                    :address,
                    :city,
                    :district,
                    :latitude,
                    :longitude,
                    :fuel_status,
                    NOW(),
                    1,
                    NOW(),
                    NOW()
                )";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);

        return (int) $this->conn->lastInsertId();
    }

    public function deactivate(int $stationId, ?string $district = null): bool
    {
        $sql = "UPDATE fuel_stations
                SET is_active = 0,
                    updated_at = NOW()
                WHERE station_id = :id";

        $params = [
            "id" => $stationId
        ];

        if ($district !== null) {
            $sql .= " AND district = :district";
            $params["district"] = $district;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }
}
