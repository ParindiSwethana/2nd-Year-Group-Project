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

function itemForm(
    string $title,
    array $item,
    string $role,
    string $district,
    array $districts
): void {
    ?>

    <section class="form-card">

        <div class="form-heading">

            <div>

                <span class="eyebrow">
                    INVENTORY ITEM
                </span>

                <h2>
                    <?= invE($title) ?>
                </h2>

                <p>
                    Enter the relief item details below.
                </p>

            </div>

            <a
                class="secondary-btn"
                href="inventory_list.php"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </a>

        </div>

        <form
            class="inventory-form"
            method="post"
            action="../../controllers/InventoryController.php?action=save"
        >

            <input
                type="hidden"
                name="item_id"
                value="<?= (int) ($item["item_id"] ?? 0) ?>"
            >

            <label>
                Item Name

                <input
                    name="item_name"
                    required
                    value="<?= invE($item["item_name"] ?? "") ?>"
                >
            </label>

            <label>
                Category

                <select name="category" required>

                    <?php
                    $categories = [
                        "Food & Water",
                        "Medical Supplies",
                        "Shelter Supplies",
                        "Tools & Equipment",
                        "Clothing",
                        "Hygiene",
                        "Other"
                    ];
                    ?>

                    <?php foreach ($categories as $c): ?>

                        <option
                            value="<?= invE($c) ?>"
                            <?= ($item["category"] ?? "") === $c
                                ? "selected"
                                : "" ?>
                        >
                            <?= invE($c) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </label>

            <?php if ($role === "super_admin"): ?>

                <label>
                    District

                    <select name="district" required>

                        <option value="">
                            Select district
                        </option>

                        <?php foreach ($districts as $d): ?>

                            <option
                                value="<?= invE($d) ?>"
                                <?= ($item["district"] ?? $district) === $d
                                    ? "selected"
                                    : "" ?>
                            >
                                <?= invE($d) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>

            <?php else: ?>

                <label>
                    District

                    <input
                        value="<?= invE($district) ?>"
                        disabled
                    >
                </label>

            <?php endif; ?>

            <div class="form-grid">

                <label>
                    Quantity

                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="quantity"
                        required
                        value="<?= invE($item["quantity"] ?? 0) ?>"
                    >
                </label>

                <label>
                    Unit

                    <input
                        name="unit"
                        required
                        placeholder="Bottles, Packs, Kits..."
                        value="<?= invE($item["unit"] ?? "") ?>"
                    >
                </label>

            </div>

            <div class="form-grid">

                <label>
                    Low Stock Threshold

                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="low_stock_threshold"
                        required
                        value="<?= invE(
                            $item["low_stock_threshold"] ?? 10
                        ) ?>"
                    >
                </label>

                <label>
                    Expiry Date

                    <input
                        type="date"
                        name="expiry_date"
                        value="<?= invE($item["expiry_date"] ?? "") ?>"
                    >
                </label>

            </div>

            <div class="form-actions">

                <button
                    class="primary-btn"
                    type="submit"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Item
                </button>

                <a
                    class="secondary-btn"
                    href="inventory_list.php"
                >
                    Cancel
                </a>

            </div>

        </form>

    </section>

    <?php
}

$id = (int) ($_GET["id"] ?? 0);

$item = $model->find($id);

if (
    !$item
    || (
        $role === "district_admin"
        && $item["district"] !== $district
    )
) {
    header("Location: inventory_list.php");
    exit();
}

?>

<div class="app-layout">

    <?php if ($role === "district_admin"): ?>
        <?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>
    <?php else: ?>
        <?php include __DIR__ . "/../layouts/super_admin_sidebar.php"; ?>
    <?php endif; ?>

    <main class="inventory-page">

        <?php
        itemForm(
            "Edit Inventory Item",
            $item,
            $role,
            $district,
            $districts
        );
        ?>

    </main>

</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
