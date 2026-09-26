<?php
$pageTitle = "Volunteer Assignments | ResQ Lanka";
$activePage = "assignments";
require_once __DIR__ . "/../../config/session.php";
requireRole("registered_user");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/sidebar.php"; ?>
    <main class="page-content-card">
        <h2>Volunteer Assignments</h2>
        <p>Available volunteer assignments will appear here when the volunteer module is connected to the database.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
