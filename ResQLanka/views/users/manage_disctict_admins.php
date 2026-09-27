<?php

$pageTitle = "District Administrators | ResQ Lanka";
$pageCSS = "../../css/admin_management.css";
$activePage = "district_admins";

require_once __DIR__ . "/../../config/session.php";
requireRole("super_admin");

require_once __DIR__ . "/../../config/csrf.php";
require_once __DIR__ . "/../../models/DistrictAdmin.php";

function escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$districts = require __DIR__ . "/../../config/districts.php";
$selectedDistrict = $_GET["district"] ?? "";

if (!in_array($selectedDistrict, $districts, true)) {
    $selectedDistrict = "";
}

$districtAdminModel = new DistrictAdmin();
$allAdmins = $districtAdminModel->getAll();
$admins = $selectedDistrict === "" ? $allAdmins : $districtAdminModel->getAll($selectedDistrict);
$activeCount = count(array_filter($allAdmins, fn($admin) => $admin["status"] === "active"));

$successMessage = $_SESSION["district_admin_success"] ?? "";
$errors = $_SESSION["district_admin_errors"] ?? [];
unset($_SESSION["district_admin_success"], $_SESSION["district_admin_errors"]);

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/super_admin_sidebar.php";
?>

    <main class="admin-content">

        <div class="page-header-actions">
            <div class="page-heading">
                <h2>District Administrators</h2>
                <p>Create, view, edit and delete District Administrator accounts</p>
            </div>
            <a href="create_district_admin.php" class="btn-primary">
                <i class="fa-solid fa-user-plus"></i> Add District Administrator
            </a>
        </div>

        <?php if ($successMessage !== ""): ?>
            <div class="form-success"><?= escape($successMessage) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="form-errors">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fa-solid fa-circle-exclamation"></i> <?= escape($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon"><i class="fa-solid fa-user-shield"></i></div>
                <div class="card-data">
                    <h2><?= count($allAdmins) ?></h2>
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
                    <h2><?= count($allAdmins) - $activeCount ?></h2>
                    <span>Inactive accounts</span>
                </div>
            </div>
        </div>

        <form method="GET" class="filter-bar">
            <span><?= count($admins) ?> account(s) shown</span>
            <label>
                District
                <select name="district" onchange="this.form.submit()">
                    <option value="">All districts</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= escape($district) ?>" <?= $district === $selectedDistrict ? "selected" : "" ?>>
                            <?= escape($district) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </form>

        <?php if (empty($admins)): ?>
            <p class="empty-text">No District Administrator accounts found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>NIC</th>
                            <th>District</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td data-label="Name"><?= escape($admin["first_name"] . " " . $admin["last_name"]) ?></td>
                            <td data-label="Email"><?= escape($admin["email"]) ?></td>
                            <td data-label="Contact"><?= escape($admin["phone"]) ?></td>
                            <td data-label="NIC"><?= escape($admin["nic"]) ?></td>
                            <td data-label="District"><?= escape($admin["district"]) ?></td>
                            <td data-label="Status">
                                <span class="status-badge <?= $admin["status"] === "active" ? "active" : "inactive" ?>">
                                    <?= $admin["status"] === "active" ? "Active" : "Inactive" ?>
                                </span>
                            </td>
                            <td class="action-cell">
                                <div class="action-buttons">
                                    <a href="create_district_admin.php?id=<?= (int) $admin["user_id"] ?>" class="btn-outline-blue">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <form method="POST" action="../../controllers/DistrictAdminController.php" class="inline-form"
                                          onsubmit="return confirm('Delete this District Administrator account? This cannot be undone.');">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= (int) $admin["user_id"] ?>">
                                        <button type="submit" class="btn-outline-red">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php
include __DIR__ . "/../layouts/footer.php";
?>
