<?php

require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../models/FuelStation.php";

class FuelController
{
    private FuelStation $fuelStation;

    public function __construct()
    {
        $this->fuelStation = new FuelStation();
    }

    public function searchStations(): array
    {
        $location = trim($_GET["location"] ?? "");
        $type = trim($_GET["type"] ?? "");

        return $this->fuelStation->search(
            $location,
            $type
        );
    }

    public function getStationTypes(): array
    {
        return $this->fuelStation->getTypes();
    }

    public function submitVote(): void
    {
        requireRole("registered_user");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../views/fuel/search.php");
            exit();
        }

        $stationId = (int) ($_POST["station_id"] ?? 0);
        $vote = trim($_POST["vote"] ?? "");
        $userId = (int) ($_SESSION["user_id"] ?? 0);

        if (
            $stationId <= 0 ||
            $userId <= 0 ||
            !in_array($vote, ["available", "out_of_stock"], true)
        ) {
            header("Location: ../views/fuel/search.php?error=invalid_vote");
            exit();
        }

        $station = $this->fuelStation->find($stationId);

        if (!$station) {
            header("Location: ../views/fuel/search.php?error=station_not_found");
            exit();
        }

        try {
            $this->fuelStation->vote(
                $stationId,
                $userId,
                $vote
            );

            header(
                "Location: ../views/fuel/search.php?updated=1#station-" .
                $stationId
            );
            exit();
        } catch (Throwable $e) {
            header("Location: ../views/fuel/search.php?error=vote_failed");
            exit();
        }
    }

    public function getManageableStations(): array
    {
        requireRole([
            "district_admin",
            "super_admin"
        ]);

        $location = trim($_GET["location"] ?? "");
        $type = trim($_GET["type"] ?? "");

        if ($_SESSION["role"] === "district_admin") {
            $district = $_SESSION["district"] ?? "";

            return $this->fuelStation->search(
                $location,
                $type,
                $district,
                100
            );
        }

        $district = trim($_GET["district"] ?? "");

        return $this->fuelStation->search(
            $location,
            $type,
            $district !== "" ? $district : null,
            100
        );
    }

    public function saveStation(): void
    {
        requireRole([
            "district_admin",
            "super_admin"
        ]);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../views/fuel/manage_stations.php");
            exit();
        }

        $stationId = isset($_POST["station_id"])
            ? (int) $_POST["station_id"]
            : null;

        $stationName = trim($_POST["station_name"] ?? "");
        $stationType = trim($_POST["station_type"] ?? "");
        $address = trim($_POST["address"] ?? "");
        $city = trim($_POST["city"] ?? "");
        $district = trim($_POST["district"] ?? "");
        $latitude = trim($_POST["latitude"] ?? "");
        $longitude = trim($_POST["longitude"] ?? "");
        $fuelStatus = trim($_POST["fuel_status"] ?? "unknown");

        if ($_SESSION["role"] === "district_admin") {
            $district = $_SESSION["district"] ?? "";

            if ($stationId) {
                $existingStation = $this->fuelStation->find($stationId);

                if (
                    !$existingStation ||
                    $existingStation["district"] !== $district
                ) {
                    header(
                        "Location: ../views/fuel/manage_stations.php?error=unauthorized"
                    );
                    exit();
                }
            }
        }

        if (
            $stationName === "" ||
            $stationType === "" ||
            $address === "" ||
            $city === "" ||
            $district === ""
        ) {
            header(
                "Location: ../views/fuel/manage_stations.php?error=missing_fields"
            );
            exit();
        }

        if (
            !in_array(
                $fuelStatus,
                ["available", "out_of_stock", "unknown"],
                true
            )
        ) {
            $fuelStatus = "unknown";
        }

        $data = [
            "station_name" => $stationName,
            "station_type" => $stationType,
            "address" => $address,
            "city" => $city,
            "district" => $district,
            "latitude" => $latitude !== "" ? $latitude : null,
            "longitude" => $longitude !== "" ? $longitude : null,
            "fuel_status" => $fuelStatus
        ];

        try {
            $this->fuelStation->save(
                $data,
                $stationId ?: null
            );

            header(
                "Location: ../views/fuel/manage_stations.php?saved=1"
            );
            exit();
        } catch (Throwable $e) {
            header(
                "Location: ../views/fuel/manage_stations.php?error=save_failed"
            );
            exit();
        }
    }

    public function deleteStation(): void
    {
        requireRole([
            "district_admin",
            "super_admin"
        ]);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../views/fuel/manage_stations.php");
            exit();
        }

        $stationId = (int) ($_POST["station_id"] ?? 0);

        if ($stationId <= 0) {
            header(
                "Location: ../views/fuel/manage_stations.php?error=invalid_station"
            );
            exit();
        }

        $district = null;

        if ($_SESSION["role"] === "district_admin") {
            $district = $_SESSION["district"] ?? "";
        }

        try {
            $deleted = $this->fuelStation->deactivate(
                $stationId,
                $district
            );

            if (!$deleted) {
                header(
                    "Location: ../views/fuel/manage_stations.php?error=unauthorized"
                );
                exit();
            }

            header(
                "Location: ../views/fuel/manage_stations.php?deleted=1"
            );
            exit();
        } catch (Throwable $e) {
            header(
                "Location: ../views/fuel/manage_stations.php?error=delete_failed"
            );
            exit();
        }
    }
}

$controller = new FuelController();
$action = $_GET["action"] ?? "";

if ($action === "vote") {
    $controller->submitVote();
}

if ($action === "save") {
    $controller->saveStation();
}

if ($action === "delete") {
    $controller->deleteStation();
}
