<?php

$pageTitle = "Publish Notice | ResQ Lanka";
$pageCSS = "../../css/admin_management.css";
$activePage = "notices";

require_once __DIR__ . "/../../config/session.php";
requireRole("district_admin");

require_once __DIR__ . "/../../config/csrf.php";

function escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$errors = $_SESSION["notice_errors"] ?? [];
$old = $_SESSION["notice_old"] ?? [];
unset($_SESSION["notice_errors"], $_SESSION["notice_old"]);

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/district_admin_sidebar.php";
?>

    <main class="admin-content">

        <div class="page-header-actions">
            <div class="page-heading">
                <h2>Publish a Notice or Alert</h2>
                <p>It will appear on the dashboard of registered users in <?= escape($_SESSION["district"] ?? "your district") ?> District</p>
            </div>
            <a href="manage_notices.php" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to notices
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="form-errors">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fa-solid fa-circle-exclamation"></i> <?= escape($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="../../controllers/NoticeController.php" class="form-grid" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
            <input type="hidden" name="action" value="create">

            <div class="field full">
                <label for="title">Title</label>
                <div class="input-box">
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" id="title" name="title" maxlength="150" placeholder="e.g. Flood warning for low-lying areas"
                           value="<?= escape($old["title"] ?? "") ?>" required>
                </div>
            </div>

            <div class="field full">
                <label for="message">Message</label>
                <div class="input-box">
                    <i class="fa-solid fa-message"></i>
                    <textarea id="message" name="message" maxlength="1000"
                              placeholder="Describe the situation and what residents should do" required><?= escape($old["message"] ?? "") ?></textarea>
                </div>
            </div>

            <div class="field">
                <label for="notice_type">Type</label>
                <div class="input-box">
                    <i class="fa-solid fa-flag"></i>
                    <select id="notice_type" name="notice_type" required>
                        <option value="notice" <?= ($old["notice_type"] ?? "notice") === "notice" ? "selected" : "" ?>>Notice (general information)</option>
                        <option value="alert" <?= ($old["notice_type"] ?? "") === "alert" ? "selected" : "" ?>>Alert (urgent warning)</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="expires_at">Expires At (optional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-clock"></i>
                    <input type="datetime-local" id="expires_at" name="expires_at" value="<?= escape($old["expires_at"] ?? "") ?>">
                </div>
            </div>

            <div class="form-actions">
                <a href="manage_notices.php" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Publish
                </button>
            </div>
        </form>

    </main>
</div>

<?php
include __DIR__ . "/../layouts/footer.php";
?>
