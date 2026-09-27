<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION["user_id"], $_SESSION["role"]);
}

function dashboardPathForRole(?string $role = null): string
{
    $role = $role ?? ($_SESSION["role"] ?? null);

    if ($role === "super_admin") {
        return "../dashboard/super_dashboard.php";
    }

    if ($role === "district_admin") {
        return "../dashboard/district_dashboard.php";
    }

    return "../dashboard/user_dashboard.php";
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function requireRole(array|string $roles): void
{
    requireLogin();
    $roles = (array) $roles;

    if (!in_array($_SESSION["role"], $roles, true)) {
        header("Location: " . dashboardPathForRole());
        exit();
    }
}
?>
