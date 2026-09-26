<?php
$pageTitle = "Assignment Details | ResQ Lanka";
$activePage = "assignments";
require_once __DIR__ . "/../../config/session.php";
requireRole("registered_user");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/sidebar.php"; ?>
    <main class="page-content-card">
        <h2>Assignment Details</h2>
        <p>Volunteer assignment details will appear here when the volunteer data layer is connected.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
