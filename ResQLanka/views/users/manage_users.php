<?php
$pageTitle = "Manage Volunteers | ResQ Lanka";
$activePage = "volunteers";
require_once __DIR__ . "/../../config/session.php";
requireRole("district_admin");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>
    <main class="page-content-card">
        <h2>Manage Volunteers</h2>
        <p>Registered volunteer records will appear here when user-management functionality is connected.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
