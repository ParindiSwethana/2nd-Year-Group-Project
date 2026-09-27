<?php
// Included in views/dashboard/user_dashboard.php; uses that page's dashboard styles

require_once __DIR__ . "/../../models/Notice.php";

$dashboardNotices = (new Notice())->getVisibleForDistrict($_SESSION["district"] ?? "", 5);
?>

<section class="dashboard-panel alerts-panel" style="margin-top:22px;">

    <div class="panel-heading">
        <div>
            <span class="section-label">
                NOTICES &amp; ALERTS
            </span>
            <h2>
                <i class="fa-solid fa-bullhorn"></i>
                Latest for <?= htmlspecialchars($_SESSION["district"] ?? "your district", ENT_QUOTES, "UTF-8") ?>
            </h2>
        </div>
    </div>

    <?php if (empty($dashboardNotices)): ?>
        <p>There are no active notices for your district.</p>
    <?php endif; ?>

    <?php foreach ($dashboardNotices as $notice): ?>
        <article class="alert-card <?= $notice["notice_type"] === "alert" ? "critical-alert" : "advisory-alert" ?>">

            <div class="alert-icon">
                <i class="fa-solid <?= $notice["notice_type"] === "alert" ? "fa-triangle-exclamation" : "fa-circle-info" ?>"></i>
            </div>

            <div class="alert-content">
                <div class="alert-top">
                    <span class="alert-level">
                        <?= $notice["notice_type"] === "alert" ? "ALERT" : "NOTICE" ?>
                    </span>
                    <span class="alert-time">
                        <?= htmlspecialchars(date("d M Y, h:i A", strtotime($notice["published_at"])), ENT_QUOTES, "UTF-8") ?>
                    </span>
                </div>

                <h3><?= htmlspecialchars($notice["title"], ENT_QUOTES, "UTF-8") ?></h3>

                <p><?= nl2br(htmlspecialchars($notice["message"], ENT_QUOTES, "UTF-8")) ?></p>
            </div>

        </article>
    <?php endforeach; ?>

</section>
