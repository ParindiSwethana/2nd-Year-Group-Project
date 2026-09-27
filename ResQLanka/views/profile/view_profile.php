<?php
$pageTitle = "My Profile | ResQ Lanka";
require_once __DIR__ . "/../../config/session.php";
requireLogin();
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php if (($_SESSION["role"] ?? "") === "registered_user") { include __DIR__ . "/../layouts/sidebar.php"; } elseif (($_SESSION["role"] ?? "") === "district_admin") { include __DIR__ . "/../layouts/district_admin_sidebar.php"; } elseif (($_SESSION["role"] ?? "") === "super_admin") { include __DIR__ . "/../layouts/super_admin_sidebar.php"; } ?>
    <main class="page-content-card">
        <h2><?= htmlspecialchars($_SESSION["name"] ?? "User", ENT_QUOTES, "UTF-8") ?></h2>
        <p>Email/Username: <?= htmlspecialchars($_SESSION["username"] ?? "", ENT_QUOTES, "UTF-8") ?></p>
        <?php if (!empty($_SESSION["district"])): ?><p>District: <?= htmlspecialchars($_SESSION["district"], ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <?php if (($_SESSION["role"] ?? "") === "registered_user"): ?><p>Tier: <?= htmlspecialchars($_SESSION["tier"] ?? "Bronze", ENT_QUOTES, "UTF-8") ?> · Points: <?= (int) ($_SESSION["points"] ?? 0) ?></p><?php endif; ?>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
