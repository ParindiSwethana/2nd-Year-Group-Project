<?php
$pageTitle = "District Administrator Dashboard | ResQ Lanka";
$activePage = "dashboard";
require_once __DIR__ . "/../../config/session.php";
requireRole("district_admin");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>
    <main class="page-content-card">
        <h2>District Administrator Dashboard</h2>
        <p>Use the navigation menu to manage disasters, volunteers and district inventory.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
