<?php

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

// With ?id= this page edits an existing account; without it, it creates a new one
$id = (int) ($_GET["id"] ?? 0);
$isEdit = $id > 0;

$values = [
    "full_name" => "", "date_of_birth" => "", "gender" => "", "email" => "", "phone" => "",
    "address" => "", "office_contact" => "", "district" => "", "nic" => "", "status" => "active"
];

if ($isEdit) {
    $admin = (new DistrictAdmin())->getById($id);

    if (!$admin) {
        $_SESSION["district_admin_errors"] = ["District Administrator account not found."];
        header("Location: manage_disctict_admins.php");
        exit();
    }

    $values = array_merge($values, array_intersect_key($admin, $values));
    $values["full_name"] = trim($admin["first_name"] . " " . $admin["last_name"]);
}

$errors = $_SESSION["district_admin_errors"] ?? [];
$values = array_merge($values, $_SESSION["district_admin_old"] ?? []);
unset($_SESSION["district_admin_errors"], $_SESSION["district_admin_old"]);

$pageTitle = ($isEdit ? "Edit" : "Add") . " District Administrator | ResQ Lanka";

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
                <h2><?= $isEdit ? "Edit District Administrator" : "Add District Administrator" ?></h2>
                <p><?= $isEdit
                    ? "Update the details, assigned district or status of this account"
                    : "The new administrator signs in with the username or email address and the password entered here" ?></p>
            </div>
            <a href="manage_disctict_admins.php" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to list
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="form-errors">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fa-solid fa-circle-exclamation"></i> <?= escape($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="../../controllers/DistrictAdminController.php" class="form-grid" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
            <input type="hidden" name="action" value="<?= $isEdit ? "update" : "create" ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="user_id" value="<?= $id ?>">
            <?php endif; ?>

            <div class="field">
                <label for="full_name">Full Name</label>
                <div class="input-box">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="full_name" name="full_name" maxlength="100" value="<?= escape($values["full_name"]) ?>" required>
                </div>
            </div>

            <div class="field">
                <label for="date_of_birth">Date of Birth</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?= escape($values["date_of_birth"]) ?>" required>
                </div>
            </div>

            <div class="field">
                <label for="gender">Gender</label>
                <div class="input-box">
                    <i class="fa-solid fa-venus-mars"></i>
                    <select id="gender" name="gender" required>
                        <option value="">Select gender</option>
                        <?php foreach (["Male", "Female"] as $gender): ?>
                            <option value="<?= $gender ?>" <?= $values["gender"] === $gender ? "selected" : "" ?>><?= $gender ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="nic">NIC Number</label>
                <div class="input-box">
                    <i class="fa-solid fa-id-card"></i>
                    <input type="text" id="nic" name="nic" maxlength="12" value="<?= escape($values["nic"]) ?>" required>
                </div>
            </div>

            <div class="field">
                <label for="email">Email Address</label>
                <div class="input-box">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" maxlength="100" value="<?= escape($values["email"]) ?>" required>
                </div>
            </div>

            <div class="field">
                <label for="phone">Contact Number</label>
                <div class="input-box">
                    <i class="fa-solid fa-phone"></i>
                    <input type="tel" id="phone" name="phone" maxlength="20" value="<?= escape($values["phone"]) ?>" required>
                </div>
            </div>

            <div class="field">
                <label for="district">Assigned District</label>
                <div class="input-box">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <select id="district" name="district" required>
                        <option value="">Select district</option>
                        <?php foreach ($districts as $district): ?>
                            <option value="<?= escape($district) ?>" <?= $values["district"] === $district ? "selected" : "" ?>><?= escape($district) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="username">Username (set from the district)</label>
                <div class="input-box">
                    <i class="fa-solid fa-at"></i>
                    <input type="text" id="username" value="<?= escape($values["district"] !== "" ? $values["district"] . " Office" : "") ?>" placeholder="Select a district first" readonly>
                </div>
            </div>

            <div class="field">
                <label for="address">Office Address</label>
                <div class="input-box">
                    <i class="fa-solid fa-building"></i>
                    <input type="text" id="address" name="address" maxlength="255" value="<?= escape($values["address"]) ?>" required>
                </div>
            </div>

            <div class="field">
                <label for="office_contact">Office Contact Number</label>
                <div class="input-box">
                    <i class="fa-solid fa-phone-volume"></i>
                    <input type="tel" id="office_contact" name="office_contact" maxlength="20" value="<?= escape($values["office_contact"]) ?>" required>
                </div>
            </div>

            <?php if ($isEdit): ?>
                <div class="field full">
                    <label for="status">Account Status</label>
                    <div class="input-box">
                        <i class="fa-solid fa-toggle-on"></i>
                        <select id="status" name="status">
                            <option value="active" <?= $values["status"] === "active" ? "selected" : "" ?>>Active</option>
                            <option value="inactive" <?= $values["status"] !== "active" ? "selected" : "" ?>>Inactive (cannot sign in)</option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <div class="field">
                <label for="password"><?= $isEdit ? "New Password (leave empty to keep the current one)" : "Password" ?></label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" <?= $isEdit ? "" : "required" ?>>
                    <i class="fa-regular fa-eye toggle-password" data-target="password"></i>
                </div>
            </div>

            <div class="field">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="8" autocomplete="new-password" <?= $isEdit ? "" : "required" ?>>
                    <i class="fa-regular fa-eye toggle-password" data-target="confirm_password"></i>
                </div>
            </div>

            <div class="form-actions">
                <a href="manage_disctict_admins.php" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? "Save Changes" : "Create Account" ?>
                </button>
            </div>
        </form>

    </main>
</div>

<script src="../../js/district_admin_form.js"></script>

<?php
include __DIR__ . "/../layouts/footer.php";
?>
