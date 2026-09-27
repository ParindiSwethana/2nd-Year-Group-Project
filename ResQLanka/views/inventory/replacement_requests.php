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

$db = (new Database())->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $act = $_POST["request_action"] ?? "";

    if ($act === "create") {

        $itemId = (int) ($_POST["item_id"] ?? 0);

        $item = $model->find($itemId);

        if (
            $item
            && (
                $role === "super_admin"
                || $item["district"] === $district
            )
        ) {

            $st = $db->prepare(
                "
                INSERT INTO inventory_replacement_requests (
                    item_id,
                    district,
                    requested_quantity,
                    reason,
                    requested_by
                )
                VALUES (?, ?, ?, ?, ?)
                "
            );

            $st->execute([
                $itemId,
                $item["district"],
                (float) $_POST["requested_quantity"],
                trim($_POST["reason"]),
                (int) $_SESSION["user_id"]
            ]);
        }
    }

    if (
        $role === "super_admin"
        && $act === "review"
    ) {

        $status = $_POST["status"] ?? "";

        if (
            in_array(
                $status,
                [
                    "Approved",
                    "Rejected",
                    "Fulfilled"
                ],
                true
            )
        ) {

            $st = $db->prepare(
                "
                UPDATE inventory_replacement_requests
                SET
                    status = ?,
                    reviewed_by = ?,
                    reviewed_at = NOW()
                WHERE request_id = ?
                "
            );

            $st->execute([
                $status,
                (int) $_SESSION["user_id"],
                (int) $_POST["request_id"]
            ]);
        }
    }

    header("Location: replacement_requests.php");
    exit();
}

$items = $model->search($district);

$sql = "
    SELECT
        r.*,
        i.item_name,
        i.unit
    FROM inventory_replacement_requests r
    JOIN inventory_items i
        ON i.item_id = r.item_id
";

if ($district !== "") {
    $sql .= " WHERE r.district = :district";
}

$sql .= " ORDER BY r.created_at DESC";

$st = $db->prepare($sql);

$st->execute(
    $district !== ""
        ? ["district" => $district]
        : []
);

$requests = $st->fetchAll();

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
                    RESTOCKING
                </span>

                <h2>
                    Replenishment Requests
                </h2>

                <p>
                    Request and track replacement stock for relief supplies.
                </p>

            </div>

            <a
                class="secondary-btn"
                href="inventory_list.php"
            >
                Back to Inventory
            </a>

        </section>

        <section class="movement-grid">

            <form
                class="form-card compact"
                method="post"
            >

                <input
                    type="hidden"
                    name="request_action"
                    value="create"
                >

                <h3>
                    New Request
                </h3>

                <label>
                    Item

                    <select
                        name="item_id"
                        required
                    >

                        <option value="">
                            Select item
                        </option>

                        <?php foreach ($items as $i): ?>

                            <option value="<?= (int) $i["item_id"] ?>">
                                <?= invE($i["item_name"]) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>

                <label>
                    Requested Quantity

                    <input
                        type="number"
                        min="0.01"
                        step="0.01"
                        name="requested_quantity"
                        required
                    >

                </label>

                <label>
                    Reason

                    <textarea
                        name="reason"
                        rows="4"
                        required
                    ></textarea>

                </label>

                <button class="primary-btn">
                    Submit Request
                </button>

            </form>

            <section class="history-card">

                <h3>
                    Requests
                </h3>

                <div class="table-wrap">

                    <table>

                        <thead>

                            <tr>
                                <th>Item</th>
                                <th>District</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Status</th>

                                <?php if ($role === "super_admin"): ?>
                                    <th>Action</th>
                                <?php endif; ?>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($requests as $r): ?>

                                <tr>

                                    <td>
                                        <?= invE($r["item_name"]) ?>
                                    </td>

                                    <td>
                                        <?= invE($r["district"]) ?>
                                    </td>

                                    <td>
                                        <?= invE(
                                            $r["requested_quantity"]
                                            . " "
                                            . $r["unit"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= invE($r["reason"]) ?>
                                    </td>

                                    <td>

                                        <span class="status-pill">
                                            <?= invE($r["status"]) ?>
                                        </span>

                                    </td>

                                    <?php if ($role === "super_admin"): ?>

                                        <td>

                                            <form
                                                method="post"
                                                class="review-form"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="request_action"
                                                    value="review"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="request_id"
                                                    value="<?= (int) $r["request_id"] ?>"
                                                >

                                                <select name="status">

                                                    <option>
                                                        Approved
                                                    </option>

                                                    <option>
                                                        Rejected
                                                    </option>

                                                    <option>
                                                        Fulfilled
                                                    </option>

                                                </select>

                                                <button>
                                                    Update
                                                </button>

                                            </form>

                                        </td>

                                    <?php endif; ?>

                                </tr>

                            <?php endforeach; ?>

                            <?php if (!$requests): ?>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="empty"
                                    >
                                        No replenishment requests yet.
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
