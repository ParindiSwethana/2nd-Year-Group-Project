<?php
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../models/FuelStation.php";

function fuelRedirect(string $path, string $message = ""): void
{
    $suffix = $message !== "" ? "?message=" . urlencode($message) : "";
    header("Location: " . $path . $suffix);
    exit();
}

$action = $_GET["action"] ?? $_POST["action"] ?? "";
$model = new FuelStation();

if ($action === "vote") {
    requireRole("registered_user");
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        fuelRedirect("../views/fuel/search.php");
    }

    $stationId = (int) ($_POST["station_id"] ?? 0);
    $vote = $_POST["vote"] ?? "";
    if ($stationId < 1 || !in_array($vote, ["available", "out_of_stock"], true)) {
        fuelRedirect("../views/fuel/search.php", "Invalid vote.");
    }

    $model->vote($stationId, (int) $_SESSION["user_id"], $vote);
    fuelRedirect("../views/fuel/search.php", "Thank you. Fuel availability has been updated.");
}

if (in_array($action, ["save", "delete"], true)) {
    requireRole(["district_admin", "super_admin"]);
    $role = $_SESSION["role"];
    $adminDistrict = trim((string) ($_SESSION["district"] ?? ""));

    if ($action === "delete") {
        $stationId = (int) ($_POST["station_id"] ?? 0);
        $model->deactivate($stationId, $role === "district_admin" ? $adminDistrict : null);
        fuelRedirect("../views/fuel/manage_stations.php", "Fuel station removed.");
    }

    $stationId = (int) ($_POST["station_id"] ?? 0);
    $district = trim($_POST["district"] ?? "");
    if ($role === "district_admin") {
        $district = $adminDistrict;
    }

    $data = [
        "station_name" => trim($_POST["station_name"] ?? ""),
        "station_type" => trim($_POST["station_type"] ?? ""),
        "address" => trim($_POST["address"] ?? ""),
        "city" => trim($_POST["city"] ?? ""),
        "district" => $district,
        "latitude" => ($_POST["latitude"] ?? "") !== "" ? (float) $_POST["latitude"] : null,
        "longitude" => ($_POST["longitude"] ?? "") !== "" ? (float) $_POST["longitude"] : null,
        "fuel_status" => in_array($_POST["fuel_status"] ?? "", ["available", "out_of_stock", "unknown"], true) ? $_POST["fuel_status"] : "unknown"
    ];

    if ($data["station_name"] === "" || $data["station_type"] === "" || $data["address"] === "" || $data["district"] === "") {
        fuelRedirect("../views/fuel/manage_stations.php", "Please complete all required station details.");
    }

    if ($stationId > 0 && $role === "district_admin") {
        $existing = $model->find($stationId);
        if (!$existing || $existing["district"] !== $adminDistrict) {
            fuelRedirect("../views/fuel/manage_stations.php", "You can only manage stations in your district.");
        }
    }

    $model->save($data, $stationId > 0 ? $stationId : null);
    fuelRedirect("../views/fuel/manage_stations.php", $stationId > 0 ? "Fuel station updated." : "Fuel station added.");
}

fuelRedirect("../views/fuel/search.php");
