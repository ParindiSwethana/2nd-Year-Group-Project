<?php

$pageTitle = "Notices & Alerts | ResQ Lanka";
$pageCSS = "../../css/admin_management.css";
$activePage = "notices";

require_once __DIR__ . "/../../config/session.php";
requireRole("district_admin");

require_once __DIR__ . "/../../config/csrf.php";
require_once __DIR__ . "/../../models/Notice.php";
require_once __DIR__ . "/../../models/DistrictAdmin.php";

function escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

// District comes from the database in case the Super Administrator changed it after login
$account = (new DistrictAdmin())->getById($_SESSION["user_id"]);

if (!$account || $account["status"] !== "active") {
    header("Location: ../../controllers/AuthController.php?action=logout");
    exit();
}

$district = $account["district"];
$_SESSION["district"] = $district;

$notices = (new Notice())->getByDistrict($district);
$now = Notice::now()->format("Y-m-d H:i:s");
$activeCount = count(array_filter($notices, fn($notice) => $notice["expires_at"] === null || $notice["expires_at"] > $now));

$successMessage = $_SESSION["notice_success"] ?? "";
$errors = $_SESSION["notice_errors"] ?? [];
unset($_SESSION["notice_success"], $_SESSION["notice_errors"]);

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
                <h2>Notices &amp; Alerts</h2>
                <p>Notices for <?= escape($district) ?> District appear on the dashboard of registered users in the district</p>
            </div>
            <a href="create_notice.php" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Publish Notice
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
                <div class="card-icon"><i class="fa-solid fa-bullhorn"></i></div>
                <div class="card-data">
                    <h2><?= count($notices) ?></h2>
                    <span>Notices published</span>
                </div>
            </div>
            <div class="summary-card green">
                <div class="card-icon"><i class="fa-solid fa-eye"></i></div>
                <div class="card-data">
                    <h2><?= $activeCount ?></h2>
                    <span>Currently shown to users</span>
                </div>
            </div>
            <div class="summary-card grey">
                <div class="card-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="card-data">
                    <h2><?= count($notices) - $activeCount ?></h2>
                    <span>Expired</span>
                </div>
            </div>
        </div>

        <?php if (empty($notices)): ?>
            <p class="empty-text">No notices have been published for this district yet.</p>
        <?php else: ?>
            <div class="notice-list">
                <?php foreach ($notices as $notice):
                    $isExpired = $notice["expires_at"] !== null && $notice["expires_at"] <= $now; ?>
                    <article class="notice-item <?= $notice["notice_type"] === "alert" ? "alert" : "" ?> <?= $isExpired ? "expired" : "" ?>">
                        <div class="notice-top">
                            <div>
                                <span class="status-badge <?= escape($notice["notice_type"]) ?>">
                                    <?= $notice["notice_type"] === "alert" ? "Alert" : "Notice" ?>
                                </span>
                                <?php if ($isExpired): ?>
                                    <span class="status-badge inactive">Expired</span>
                                <?php endif; ?>
                                <h3><?= escape($notice["title"]) ?></h3>
                            </div>
                            <form method="POST" action="../../controllers/NoticeController.php" class="inline-form"
                                  onsubmit="return confirm('Remove this notice? Registered users will no longer see it.');">
                                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="notice_id" value="<?= (int) $notice["notice_id"] ?>">
                                <button type="submit" class="btn-outline-red">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                        <p><?= nl2br(escape($notice["message"])) ?></p>
                        <p class="notice-meta">
                            Published <?= escape(date("d M Y, h:i A", strtotime($notice["published_at"]))) ?>
                            by <?= escape($notice["published_by_name"]) ?>
                            <?php if ($notice["expires_at"] !== null): ?>
                                &middot; Expires <?= escape(date("d M Y, h:i A", strtotime($notice["expires_at"]))) ?>
                            <?php endif; ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php
include __DIR__ . "/../layouts/footer.php";
?>
