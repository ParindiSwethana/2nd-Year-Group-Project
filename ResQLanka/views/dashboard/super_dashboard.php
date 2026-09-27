<?php
$pageTitle = "Super Administrator Dashboard | ResQ Lanka";
require_once __DIR__ . "/../../config/session.php";
requireRole("super_admin");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <main class="page-content-card" style="grid-column:1 / -1;">
        <h2>Super Administrator Dashboard</h2>
        <p>National-level administration features can be accessed from this dashboard as they are implemented.</p>
        <p><a class="logout-button" href="../../controllers/AuthController.php?action=logout">Log Out</a></p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
