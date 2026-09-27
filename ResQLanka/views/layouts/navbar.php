<?php
$loggedIn = isset($_SESSION["user_id"], $_SESSION["role"]);
$role = $_SESSION["role"] ?? null;
$brandHref = "../auth/login.php";

if ($role === "registered_user") {
    $brandHref = "../dashboard/user_dashboard.php";
} elseif ($role === "district_admin") {
    $brandHref = "../dashboard/district_dashboard.php";
} elseif ($role === "super_admin") {
    $brandHref = "../dashboard/super_dashboard.php";
}
?>

<header class="top-header">
    <div class="header-inner">
        <a href="<?= htmlspecialchars($brandHref, ENT_QUOTES, "UTF-8") ?>" class="brand">
            <img src="../../images/logo.png" alt="ResQ Lanka Logo">

            <div class="brand-text">
                <h1>Res<span>Q</span> Lanka</h1>
                <p>Disaster &amp; Crisis Management System</p>
            </div>
        </a>

        <div class="header-actions">
            <a href="../fuel/search.php" class="quick-action fuel-action">
                <i class="fa-solid fa-gas-pump"></i>

                <div>
                    <strong>CHECK FUEL</strong>
                    <span>AVAILABILITY</span>
                </div>
            </a>

            <a href="../disaster/user_reports.php" class="quick-action disaster-action">
                <i class="fa-solid fa-triangle-exclamation"></i>

                <div>
                    <strong>INFORM ABOUT</strong>
                    <span>DISASTER!</span>
                </div>
            </a>

            <?php if ($loggedIn): ?>
                <a href="../profile/view_profile.php" class="profile-button" title="View Profile">
                    <i class="fa-solid fa-user"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
