<?php
$pageTitle = "Manage Inventory | ResQ Lanka";
$activePage = "inventory";
require_once __DIR__ . "/../../config/session.php";
requireRole("district_admin");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>
    <main class="page-content-card">
        <h2>Manage Inventory</h2>
        <p>District inventory records will appear here when the inventory data layer is connected.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
