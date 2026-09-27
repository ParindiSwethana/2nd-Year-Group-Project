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

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$status = trim($_GET["status"] ?? "");
$sort = trim($_GET["sort"] ?? "name_asc");

$items = $model->search(
    $district,
    $search,
    $category,
    $status,
    $sort
);

$stats = $model->stats(
    $district !== "" ? $district : null
);

$categories = $model->categories(
    $district !== "" ? $district : null
);

$message = trim($_GET["message"] ?? "");

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
                <span class="eyebrow">RESOURCE MANAGEMENT</span>

                <h2>Manage Inventory</h2>

                <p>
                    <?php if ($role === "district_admin"): ?>
                        View and manage relief inventory in <?= invE($district) ?> District.
                    <?php else: ?>
                        Monitor and manage relief inventory across Sri Lanka.
                    <?php endif; ?>
                </p>
            </div>

            <div class="hero-actions">

                <a
                    class="secondary-btn"
                    href="stock_movements.php<?= $district !== "" && $role === "super_admin"
                        ? "?district=" . urlencode($district)
                        : "" ?>"
                >
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    Stock Movements
                </a>

                <a class="primary-btn" href="add_item.php">
                    <i class="fa-solid fa-plus"></i>
                    Add New Item
                </a>

            </div>

        </section>

        <?php if ($message !== ""): ?>

            <div class="notice <?= ($_GET["type"] ?? "") === "error" ? "error" : "" ?>">
                <?= invE($message) ?>
            </div>

        <?php endif; ?>

        <?php if ($role === "super_admin"): ?>

            <form class="district-strip" method="get">

                <label>District</label>

                <select name="district" onchange="this.form.submit()">

                    <option value="">All Districts</option>

                    <?php foreach ($districts as $d): ?>

                        <option
                            value="<?= invE($d) ?>"
                            <?= $district === $d ? "selected" : "" ?>
                        >
                            <?= invE($d) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </form>

        <?php endif; ?>

        <section class="inventory-stats">

            <article>

                <div class="stat-icon blue">
                    <i class="fa-solid fa-box"></i>
                </div>

                <strong>
                    <?= (int) $stats["total_items"] ?>
                </strong>

                <span>Total Items</span>

            </article>

            <article>

                <div class="stat-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <strong>
                    <?= invE(number_format((float) $stats["total_quantity"], 2)) ?>
                </strong>

                <span>Total Quantity</span>

            </article>

            <article>

                <div class="stat-icon orange">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <strong>
                    <?= (int) $stats["low_stock"] ?>
                </strong>

                <span>Low Stock Items</span>

            </article>

            <article>

                <div class="stat-icon purple">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>

                <strong>
                    <?= (int) $stats["expired"] ?>
                </strong>

                <span>Expired Items</span>

            </article>

        </section>

        <form class="inventory-filters" method="get">

            <?php if ($role === "super_admin" && $district !== ""): ?>

                <input
                    type="hidden"
                    name="district"
                    value="<?= invE($district) ?>"
                >

            <?php endif; ?>

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    name="search"
                    value="<?= invE($search) ?>"
                    placeholder="Search inventory..."
                >

            </div>

            <select name="category">

                <option value="">All categories</option>

                <?php foreach ($categories as $c): ?>

                    <option <?= $category === $c ? "selected" : "" ?>>
                        <?= invE($c) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <select name="status">

                <option value="">All status</option>

                <option
                    value="stock"
                    <?= $status === "stock" ? "selected" : "" ?>
                >
                    In stock
                </option>

                <option
                    value="low"
                    <?= $status === "low" ? "selected" : "" ?>
                >
                    Low stock
                </option>

                <option
                    value="expired"
                    <?= $status === "expired" ? "selected" : "" ?>
                >
                    Expired
                </option>

            </select>

            <select name="sort">

                <option
                    value="name_asc"
                    <?= $sort === "name_asc" ? "selected" : "" ?>
                >
                    Name A - Z
                </option>

                <option
                    value="name_desc"
                    <?= $sort === "name_desc" ? "selected" : "" ?>
                >
                    Name Z - A
                </option>

                <option
                    value="quantity_desc"
                    <?= $sort === "quantity_desc" ? "selected" : "" ?>
                >
                    Highest quantity
                </option>

                <option
                    value="quantity_asc"
                    <?= $sort === "quantity_asc" ? "selected" : "" ?>
                >
                    Lowest quantity
                </option>

                <option
                    value="expiry_asc"
                    <?= $sort === "expiry_asc" ? "selected" : "" ?>
                >
                    Expiry date
                </option>

                <option
                    value="updated_desc"
                    <?= $sort === "updated_desc" ? "selected" : "" ?>
                >
                    Recently updated
                </option>

            </select>

            <button>Apply</button>

        </form>

        <section class="inventory-table-card">

            <div class="table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>

                            <?php if ($role === "super_admin" && $district === ""): ?>
                                <th>District</th>
                            <?php endif; ?>

                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($items as $item): ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= invE($item["item_name"]) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="category-pill">
                                        <?= invE($item["category"]) ?>
                                    </span>
                                </td>

                                <?php if ($role === "super_admin" && $district === ""): ?>

                                    <td>
                                        <?= invE($item["district"]) ?>
                                    </td>

                                <?php endif; ?>

                                <td>
                                    <?= invE(number_format((float) $item["quantity"], 2)) ?>
                                </td>

                                <td>
                                    <?= invE($item["unit"]) ?>
                                </td>

                                <td>
                                    <?= $item["expiry_date"]
                                        ? invE(date("M d, Y", strtotime($item["expiry_date"])))
                                        : "—" ?>
                                </td>

                                <td>

                                    <span class="status-pill <?= strtolower(
                                        str_replace(" ", "-", $item["item_status"])
                                    ) ?>">
                                        <?= invE($item["item_status"]) ?>
                                    </span>

                                </td>

                                <td>

                                    <?= invE(
                                        date(
                                            "M d, Y",
                                            strtotime($item["updated_at"])
                                        )
                                    ) ?>

                                    <small>
                                        <?= invE(
                                            date(
                                                "g:i A",
                                                strtotime($item["updated_at"])
                                            )
                                        ) ?>
                                    </small>

                                </td>

                                <td>

                                    <div class="row-actions">

                                        <a href="edit_item.php?id=<?= (int) $item["item_id"] ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form
                                            method="post"
                                            action="../../controllers/InventoryController.php?action=delete"
                                            onsubmit="return confirm('Delete this inventory item?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="item_id"
                                                value="<?= (int) $item["item_id"] ?>"
                                            >

                                            <button>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        <?php if (!$items): ?>

                            <tr>
                                <td colspan="9" class="empty">
                                    No inventory items found.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <div class="inventory-note">

                <i class="fa-solid fa-circle-info"></i>

                Items past their expiry date are automatically shown as
                expired and should not be distributed.

            </div>

        </section>

        <nav class="inventory-links">

            <a href="stock_movements.php">
                Stock Movements
            </a>

            <a href="replacement_requests.php">
                Replenishment Requests
            </a>

            <a href="inventory_reports.php">
                Inventory Report
            </a>

        </nav>

    </main>

</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
