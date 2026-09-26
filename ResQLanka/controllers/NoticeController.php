<?php

require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../config/csrf.php";
require_once __DIR__ . "/../models/Notice.php";
require_once __DIR__ . "/../models/DistrictAdmin.php";

$managePage = "../views/notices/manage_notices.php";
$createPage = "../views/notices/create_notice.php";

if (!isLoggedIn() || $_SESSION["role"] !== "district_admin") {
    header("Location: ../views/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . $managePage);
    exit();
}

verifyCsrfToken();

// Read the district from the database, in case the Super Administrator changed it after login
$account = (new DistrictAdmin())->getById($_SESSION["user_id"]);

if (!$account || $account["status"] !== "active") {
    header("Location: AuthController.php?action=logout");
    exit();
}

$district = $account["district"];
$_SESSION["district"] = $district;

$action = $_POST["action"] ?? "";
$noticeModel = new Notice();

if ($action === "delete") {
    $noticeId = (int) ($_POST["notice_id"] ?? 0);

    if ($noticeId > 0 && $noticeModel->deleteInDistrict($noticeId, $district)) {
        $_SESSION["notice_success"] = "Notice removed.";
    } else {
        $_SESSION["notice_errors"] = ["Notice not found in your district."];
    }

    header("Location: " . $managePage);
    exit();
}

if ($action !== "create") {
    header("Location: " . $managePage);
    exit();
}

$title = trim($_POST["title"] ?? "");
$message = trim($_POST["message"] ?? "");
$noticeType = $_POST["notice_type"] ?? "";
$expiresInput = trim($_POST["expires_at"] ?? "");

$errors = [];

if (strlen($title) < 5 || strlen($title) > 150) {
    $errors[] = "Title must be between 5 and 150 characters.";
}

if (strlen($message) < 10 || strlen($message) > 1000) {
    $errors[] = "Message must be between 10 and 1000 characters.";
}

if (!in_array($noticeType, ["notice", "alert"], true)) {
    $errors[] = "Please select whether this is a notice or an alert.";
}

$expiresAt = null;

if ($expiresInput !== "") {
    $expiry = DateTime::createFromFormat("Y-m-d\TH:i", $expiresInput, new DateTimeZone("Asia/Colombo"));

    if (!$expiry) {
        $errors[] = "Enter a valid expiry date and time, or leave it empty.";
    } elseif ($expiry <= Notice::now()) {
        $errors[] = "Expiry date and time must be in the future.";
    } else {
        $expiresAt = $expiry->format("Y-m-d H:i:s");
    }
}

if (!empty($errors)) {
    $_SESSION["notice_errors"] = $errors;
    $_SESSION["notice_old"] = [
        "title" => $title,
        "message" => $message,
        "notice_type" => $noticeType,
        "expires_at" => $expiresInput
    ];
    header("Location: " . $createPage);
    exit();
}

try {
    $noticeModel->create([
        "title" => $title,
        "message" => $message,
        "notice_type" => $noticeType,
        "district" => $district,
        "published_by" => (int) $_SESSION["user_id"],
        "expires_at" => $expiresAt
    ]);

    $_SESSION["notice_success"] = ucfirst($noticeType) . " published to registered users in " . $district . ".";
} catch (PDOException $exception) {
    $_SESSION["notice_errors"] = ["A database error occurred while publishing the notice."];
    header("Location: " . $createPage);
    exit();
}

header("Location: " . $managePage);
exit();
