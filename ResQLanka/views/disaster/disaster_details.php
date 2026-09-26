<?php
$pageTitle = "Disaster Details | ResQ Lanka";
$activePage = "disasters";
require_once __DIR__ . "/../../config/session.php";
requireRole("registered_user");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/sidebar.php"; ?>
    <main class="page-content-card">
        <h2>Disaster Details</h2>
        <p>Disaster details will appear here when the disaster data layer is connected.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
