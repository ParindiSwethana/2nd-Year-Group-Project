<?php

$pageTitle = "Manage Inventory | ResQ Lanka";
$pageCSS = "../../css/inventory.css";
$activePage = "inventory";

require_once __DIR__ . "/../../config/session.php";

requireRole(["district_admin", "super_admin"]);

require_once __DIR__ . "/../../models/InventoryItem.php";

function invE($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, "UTF-8");
}

$role = $_SESSION["role"];

$district = $role === "district_admin"
    ? trim((string) ($_SESSION["district"] ?? ""))
    : trim((string) ($_GET["district"] ?? ""));

$districts = require __DIR__ . "/../../config/districts.php";

$model = new InventoryItem();

$items = $model->search($district);

$stats = $model->stats(
    $district !== ""
        ? $district
        : null
);

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<div class="app-layout">

    <?php if ($role === "district_admin"): ?>
        <?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>
    <?php else: ?>
        <?php include __DIR__ . "/../layouts/super_admin_sidebar.php"; ?>
    <?php endif; ?>

    <main class="inventory-page">

        <section class="inventory-hero">

            <div>

                <span class="eyebrow">
                    INVENTORY REPORT
                </span>

                <h2>
                    Inventory Overview
                </h2>

                <p>
                    Current stock position<?= $district !== ""
                        ? " for " . invE($district) . " District"
                        : " across Sri Lanka" ?>.
                </p>

            </div>

            <div class="hero-actions">

                <button
                    class="secondary-btn"
                    onclick="window.print()"
                >
                    <i class="fa-solid fa-print"></i>
                    Print Report
                </button>

                <a
                    class="secondary-btn"
                    href="inventory_list.php"
                >
                    Back
                </a>

            </div>

        </section>

        <section class="inventory-stats">

            <article>

                <div class="stat-icon blue">
                    <i class="fa-solid fa-box"></i>
                </div>

                <strong>
                    <?= (int) $stats["total_items"] ?>
                </strong>

                <span>
                    Items
                </span>

            </article>

            <article>

                <div class="stat-icon green">
                    <i class="fa-solid fa-cubes"></i>
                </div>

                <strong>
                    <?= invE(
                        number_format(
                            (float) $stats["total_quantity"],
                            2
                        )
                    ) ?>
                </strong>

                <span>
                    Total Quantity
                </span>

            </article>

            <article>

                <div class="stat-icon orange">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <strong>
                    <?= (int) $stats["low_stock"] ?>
                </strong>

                <span>
                    Low Stock
                </span>

            </article>

            <article>

                <div class="stat-icon purple">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>

                <strong>
                    <?= (int) $stats["expired"] ?>
                </strong>

                <span>
                    Expired
                </span>

            </article>

        </section>

        <section class="inventory-table-card">

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>District</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Expiry</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($items as $i): ?>

                            <tr>

                                <td>
                                    <?= invE($i["item_name"]) ?>
                                </td>

                                <td>
                                    <?= invE($i["category"]) ?>
                                </td>

                                <td>
                                    <?= invE($i["district"]) ?>
                                </td>

                                <td>
                                    <?= invE($i["quantity"]) ?>
                                </td>

                                <td>
                                    <?= invE($i["unit"]) ?>
                                </td>

                                <td>
                                    <?= invE(
                                        $i["expiry_date"]
                                        ?: "—"
                                    ) ?>
                                </td>

                                <td>
                                    <?= invE($i["item_status"]) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
