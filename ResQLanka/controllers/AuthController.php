<?php
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../models/User.php";

if (($_GET["action"] ?? "") === "logout") {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    session_destroy();
    header("Location: ../views/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/auth/login.php");
    exit();
}

$identifier = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($identifier === "" || $password === "") {
    header("Location: ../views/auth/login.php?error=1");
    exit();
}

$user = new User();
$loggedUser = $user->login($identifier);

if (!$loggedUser || !password_verify($password, $loggedUser["password"])) {
    header("Location: ../views/auth/login.php?error=1");
    exit();
}

if (($loggedUser["status"] ?? "active") !== "active") {
    header("Location: ../views/auth/login.php?status=inactive");
    exit();
}

session_regenerate_id(true);
$_SESSION["user_id"] = $loggedUser["user_id"];
$_SESSION["username"] = $loggedUser["username"];
$_SESSION["role"] = $loggedUser["role"];
$_SESSION["name"] = trim(($loggedUser["first_name"] ?? "") . " " . ($loggedUser["last_name"] ?? ""));
$_SESSION["district"] = $loggedUser["district"] ?? null;
$_SESSION["tier"] = $loggedUser["tier"] ?? "Bronze";
$_SESSION["points"] = (int) ($loggedUser["points"] ?? 0);

$destinations = [
    "registered_user" => "../views/dashboard/user_dashboard.php",
    "district_admin" => "../views/dashboard/district_dashboard.php",
    "super_admin" => "../views/dashboard/super_dashboard.php"
];

header("Location: " . ($destinations[$loggedUser["role"]] ?? "../views/auth/login.php"));
exit();
?>
