<?php
$pageTitle = "International Volunteering | ResQ Lanka";
$activePage = "international";
require_once __DIR__ . "/../../config/session.php";
requireRole("registered_user");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/sidebar.php"; ?>
    <main class="page-content-card">
        <h2>International Volunteering</h2>
        <p>International volunteering programmes and applications will appear here when they are available.</p>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
