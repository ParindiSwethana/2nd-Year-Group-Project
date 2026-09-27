<?php
$pageTitle = "Manage Fuel Stations | ResQ Lanka";
$pageCSS = "../../css/fuel_manage.css";
$activePage = "fuel";

require_once __DIR__ . "/../../config/session.php";
requireRole(["district_admin", "super_admin"]);
require_once __DIR__ . "/../../models/FuelStation.php";

function manageFuelEscape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$model = new FuelStation();
$role = $_SESSION["role"];
$district = $role === "district_admin" ? trim((string) ($_SESSION["district"] ?? "")) : trim($_GET["district"] ?? "");
$stations = $model->search("", "", $district !== "" ? $district : null, 100);
$message = trim($_GET["message"] ?? "");
$editId = (int) ($_GET["edit"] ?? 0);
$editStation = $editId > 0 ? $model->find($editId) : null;

if ($role === "district_admin" && $editStation && $editStation["district"] !== $district) {
    $editStation = null;
}

$districts = ["Ampara","Anuradhapura","Badulla","Batticaloa","Colombo","Galle","Gampaha","Hambantota","Jaffna","Kalutara","Kandy","Kegalle","Kilinochchi","Kurunegala","Mannar","Matale","Matara","Monaragala","Mullaitivu","Nuwara Eliya","Polonnaruwa","Puttalam","Ratnapura","Trincomalee","Vavuniya"];

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>

<div class="app-layout">
    <?php
    if ($role === "district_admin") {
        include __DIR__ . "/../layouts/district_admin_sidebar.php";
    } else {
        include __DIR__ . "/../layouts/super_admin_sidebar.php";
    }
    ?>

    <main class="fuel-admin-page">
        <section class="fuel-admin-header">
            <div>
                <h2>Manage Fuel Stations</h2>
                <p><?= $role === "district_admin" ? "Manage fuel stations in " . manageFuelEscape($district) . " District" : "Manage fuel stations across Sri Lanka" ?></p>
            </div>
            <a href="search.php" class="tracker-link"><i class="fa-solid fa-gas-pump"></i> Open Fuel Tracker</a>
        </section>

        <?php if ($message !== ""): ?>
            <div class="fuel-admin-message"><?= manageFuelEscape($message) ?></div>
        <?php endif; ?>

        <?php if ($role === "super_admin"): ?>
            <form class="district-filter" method="GET">
                <label for="district">District</label>
                <select name="district" id="district" onchange="this.form.submit()">
                    <option value="">All Districts</option>
                    <?php foreach ($districts as $districtName): ?>
                        <option value="<?= manageFuelEscape($districtName) ?>" <?= $district === $districtName ? "selected" : "" ?>><?= manageFuelEscape($districtName) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <section class="fuel-admin-grid">
            <form class="station-form" method="POST" action="../../controllers/FuelController.php?action=save">
                <h3><?= $editStation ? "Edit Fuel Station" : "Add Fuel Station" ?></h3>
                <input type="hidden" name="station_id" value="<?= $editStation ? (int) $editStation["station_id"] : 0 ?>">

                <label>Station Name<input type="text" name="station_name" required value="<?= manageFuelEscape($editStation["station_name"] ?? "") ?>"></label>
                <label>Station Type<input type="text" name="station_type" required placeholder="CEYPETCO, LANKA IOC, SINOPEC" value="<?= manageFuelEscape($editStation["station_type"] ?? "") ?>"></label>
                <label>Address<input type="text" name="address" required value="<?= manageFuelEscape($editStation["address"] ?? "") ?>"></label>
                <label>City<input type="text" name="city" value="<?= manageFuelEscape($editStation["city"] ?? "") ?>"></label>

                <?php if ($role === "super_admin"): ?>
                    <label>District
                        <select name="district" required>
                            <option value="">Select district</option>
                            <?php foreach ($districts as $districtName): ?>
                                <option value="<?= manageFuelEscape($districtName) ?>" <?= ($editStation["district"] ?? "") === $districtName ? "selected" : "" ?>><?= manageFuelEscape($districtName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php else: ?>
                    <label>District<input type="text" value="<?= manageFuelEscape($district) ?>" disabled></label>
                <?php endif; ?>

                <label>Fuel Status
                    <select name="fuel_status">
                        <option value="unknown" <?= ($editStation["fuel_status"] ?? "") === "unknown" ? "selected" : "" ?>>Unknown</option>
                        <option value="available" <?= ($editStation["fuel_status"] ?? "") === "available" ? "selected" : "" ?>>Available</option>
                        <option value="out_of_stock" <?= ($editStation["fuel_status"] ?? "") === "out_of_stock" ? "selected" : "" ?>>Out of Stock</option>
                    </select>
                </label>

                <div class="coordinate-row">
                    <label>Latitude<input type="number" step="0.0000001" name="latitude" value="<?= manageFuelEscape($editStation["latitude"] ?? "") ?>"></label>
                    <label>Longitude<input type="number" step="0.0000001" name="longitude" value="<?= manageFuelEscape($editStation["longitude"] ?? "") ?>"></label>
                </div>

                <div class="station-form-actions">
                    <button type="submit"><?= $editStation ? "Update Station" : "Add Station" ?></button>
                    <?php if ($editStation): ?><a href="manage_stations.php<?= $district !== "" && $role === "super_admin" ? "?district=" . urlencode($district) : "" ?>">Cancel</a><?php endif; ?>
                </div>
            </form>

            <section class="managed-stations">
                <div class="managed-stations-title"><h3>Fuel Stations</h3><span><?= count($stations) ?> stations</span></div>

                <?php foreach ($stations as $station): ?>
                    <article class="managed-station-card">
                        <div>
                            <h4><?= manageFuelEscape($station["station_name"]) ?></h4>
                            <p><?= manageFuelEscape($station["address"]) ?> · <?= manageFuelEscape($station["district"]) ?></p>
                            <span class="manage-status manage-<?= manageFuelEscape($station["current_status"]) ?>"><?= manageFuelEscape(str_replace("_", " ", ucfirst($station["current_status"]))) ?></span>
                        </div>
                        <div class="managed-actions">
                            <a href="manage_stations.php?edit=<?= (int) $station["station_id"] ?><?= $role === "super_admin" && $district !== "" ? "&district=" . urlencode($district) : "" ?>">Edit</a>
                            <form method="POST" action="../../controllers/FuelController.php?action=delete" onsubmit="return confirm('Remove this fuel station?');">
                                <input type="hidden" name="station_id" value="<?= (int) $station["station_id"] ?>">
                                <button type="submit">Remove</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (!$stations): ?><p class="empty-managed">No fuel stations found for this district.</p><?php endif; ?>
            </section>
        </section>

        <?php include __DIR__ . "/../layouts/footer.php"; ?>
    </main>
</div>
