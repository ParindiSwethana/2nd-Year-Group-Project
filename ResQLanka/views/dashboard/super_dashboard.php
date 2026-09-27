<?php
$pageTitle = "Super Administrator Dashboard | ResQ Lanka";
$pageCSS = "../../css/admin_management.css";
$activePage = "dashboard";
require_once __DIR__ . "/../../config/session.php";
requireRole("super_admin");
require_once __DIR__ . "/../../models/DistrictAdmin.php";

$districtAdmins = (new DistrictAdmin())->getAll();
$activeCount = count(array_filter($districtAdmins, fn($admin) => $admin["status"] === "active"));

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/super_admin_sidebar.php"; ?>
    <main class="admin-content">
        <div class="page-header-actions">
            <div class="page-heading">
                <h2>Super Administrator Dashboard</h2>
                <p>National-level administration of ResQ Lanka</p>
            </div>
            <a href="../users/manage_disctict_admins.php" class="btn-primary">
                <i class="fa-solid fa-user-shield"></i> Manage District Administrators
            </a>
        </div>

        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon"><i class="fa-solid fa-user-shield"></i></div>
                <div class="card-data">
                    <h2><?= count($districtAdmins) ?></h2>
                    <span>District Administrators</span>
                </div>
            </div>
            <div class="summary-card green">
                <div class="card-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="card-data">
                    <h2><?= $activeCount ?></h2>
                    <span>Active accounts</span>
                </div>
            </div>
            <div class="summary-card grey">
                <div class="card-icon"><i class="fa-solid fa-circle-pause"></i></div>
                <div class="card-data">
                    <h2><?= count($districtAdmins) - $activeCount ?></h2>
                    <span>Inactive accounts</span>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>
