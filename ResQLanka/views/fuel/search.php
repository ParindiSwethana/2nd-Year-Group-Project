<?php
$pageTitle = "Fuel Availability | ResQ Lanka";
require_once __DIR__ . "/../../config/session.php";
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php if (isLoggedIn() && ($_SESSION["role"] ?? "") === "registered_user") { include __DIR__ . "/../layouts/sidebar.php"; } ?>
    <main class="page-content-card" <?= !isLoggedIn() ? 'style="grid-column:1 / -1;"' : '' ?>>
        <h2>Fuel Availability</h2>
        <p>Fuel-station search and availability information will appear here as the module is integrated.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
