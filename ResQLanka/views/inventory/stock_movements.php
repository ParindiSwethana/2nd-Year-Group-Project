<?php

$pageTitle = "Manage Inventory | ResQ Lanka";
$pageCSS = "../../css/inventory.css";
$activePage = "inventory";

require_once __DIR__ . "/../../config/session.php";

requireRole(["district_admin", "super_admin"]);

require_once __DIR__ . "/../../models/InventoryItem.php";
require_once __DIR__ . "/../../models/InventoryMovement.php";

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

$movementModel = new InventoryMovement();

$movements = $movementModel->all(
    $district,
    200
);

$message = trim($_GET["message"] ?? "");

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
                    STOCK CONTROL
                </span>

                <h2>
                    Stock Movements
                </h2>

                <p>
                    Record received supplies, distributions and stock adjustments.
                </p>

            </div>

            <a
                class="secondary-btn"
                href="inventory_list.php"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Inventory
            </a>

        </section>

        <?php if ($message !== ""): ?>

            <div class="notice <?= ($_GET["type"] ?? "") === "error"
                ? "error"
                : "" ?>"
            >
                <?= invE($message) ?>
            </div>

        <?php endif; ?>

        <section class="movement-grid">

            <form
                class="form-card compact"
                method="post"
                action="../../controllers/InventoryController.php?action=movement"
            >

                <h3>
                    Record Movement
                </h3>

                <label>
                    Inventory Item

                    <select
                        name="item_id"
                        required
                    >

                        <option value="">
                            Select item
                        </option>

                        <?php foreach ($items as $i): ?>

                            <option value="<?= (int) $i["item_id"] ?>">

                                <?= invE(
                                    $i["item_name"]
                                    . " — "
                                    . $i["quantity"]
                                    . " "
                                    . $i["unit"]
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>

                <label>
                    Movement Type

                    <select
                        name="movement_type"
                        required
                    >

                        <option value="stock_in">
                            Stock In / Donation
                        </option>

                        <option value="distribution">
                            Distribution / Stock Out
                        </option>

                        <option value="adjustment">
                            Set Exact Quantity
                        </option>

                    </select>

                </label>

                <label>
                    Quantity

                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="quantity"
                        required
                    >

                </label>

                <label>
                    Notes

                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Optional reference or destination"
                    ></textarea>

                </label>

                <button class="primary-btn">
                    Save Movement
                </button>

            </form>

            <section class="history-card">

                <h3>
                    Movement History
                </h3>

                <div class="table-wrap">

                    <table>

                        <thead>

                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Before</th>
                                <th>After</th>
                                <th>By</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($movements as $m): ?>

                                <tr>

                                    <td>
                                        <?= invE(
                                            date(
                                                "M d, Y g:i A",
                                                strtotime($m["created_at"])
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= invE($m["item_name"]) ?>
                                    </td>

                                    <td>
                                        <?= invE(
                                            ucwords(
                                                str_replace(
                                                    "_",
                                                    " ",
                                                    $m["movement_type"]
                                                )
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= invE(
                                            $m["quantity"]
                                            . " "
                                            . $m["unit"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= invE($m["quantity_before"]) ?>
                                    </td>

                                    <td>
                                        <?= invE($m["quantity_after"]) ?>
                                    </td>

                                    <td>
                                        <?= invE(
                                            $m["performed_by_name"]
                                            ?? "System"
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            <?php if (!$movements): ?>

                                <tr>
                                    <td
                                        colspan="7"
                                        class="empty"
                                    >
                                        No stock movements yet.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </section>

    </main>

</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
