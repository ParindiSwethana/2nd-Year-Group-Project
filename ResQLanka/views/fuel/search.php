<?php
$pageTitle = "Fuel Station Tracker | ResQ Lanka";
$pageCSS = "../../css/fuel_tracker.css";
$activePage = "fuel";

require_once __DIR__ . "/../../config/session.php";
require_once __DIR__ . "/../../models/FuelStation.php";

function fuelEscape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$model = new FuelStation();
$location = trim($_GET["location"] ?? "");
$type = trim($_GET["type"] ?? "");
$stations = $model->search($location, $type, null, 50);
$types = $model->getTypes();
$message = trim($_GET["message"] ?? "");
$loggedIn = isLoggedIn();
$role = $_SESSION["role"] ?? null;

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>

<div class="app-layout">
    <?php
    if ($role === "registered_user") {
        include __DIR__ . "/../layouts/sidebar.php";
    } elseif ($role === "district_admin") {
        include __DIR__ . "/../layouts/district_admin_sidebar.php";
    }
    ?>

    <main class="fuel-page <?= !$loggedIn || $role === 'super_admin' ? 'fuel-page-full' : '' ?>">
        <section class="fuel-main-card">
            <div class="fuel-content-column">
                <div class="fuel-heading">
                    <h2>Fuel Station Tracker</h2>
                    <p>Find nearby fuel stations and check real-time fuel availability</p>
                </div>

                <?php if ($message !== ""): ?>
                    <div class="fuel-message"><?= fuelEscape($message) ?></div>
                <?php endif; ?>

                <form class="fuel-search-box" method="GET" action="search.php">
                    <div class="fuel-search-fields">
                        <label>
                            <span>Search Location</span>
                            <input type="text" name="location" value="<?= fuelEscape($location) ?>" placeholder="Enter city, town or area">
                        </label>

                        <label>
                            <span>Station Type</span>
                            <select name="type">
                                <option value="">All station types</option>
                                <?php foreach ($types as $stationType): ?>
                                    <option value="<?= fuelEscape($stationType) ?>" <?= $type === $stationType ? "selected" : "" ?>><?= fuelEscape($stationType) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <button type="submit" class="fuel-search-button">Search</button>
                    </div>

                    <div class="fuel-search-summary">
                        <?= count($stations) ?> station<?= count($stations) === 1 ? "" : "s" ?> found<?= $location !== "" ? " for " . fuelEscape($location) : "" ?>
                    </div>
                </form>

                <section class="station-list-box">
                    <div class="station-list-title">Fuel Stations (<?= count($stations) ?>)</div>

                    <?php if (!$stations): ?>
                        <div class="no-stations">No fuel stations matched your search.</div>
                    <?php endif; ?>

                    <?php foreach ($stations as $station): ?>
                        <?php
                        $status = $station["current_status"];
                        $statusLabel = $status === "available" ? "Available" : ($status === "out_of_stock" ? "Out of Stock" : "Unknown");
                        $updatedText = $station["status_updated_at"] ? date("g:i A", strtotime($station["status_updated_at"])) : "NOT UPDATED";
                        ?>
                        <article class="station-card">
                            <div class="station-logo"><i class="fa-solid fa-gas-pump"></i></div>

                            <div class="station-details">
                                <h3><?= fuelEscape($station["station_name"]) ?></h3>
                                <p><i class="fa-solid fa-location-dot"></i> <?= fuelEscape($station["address"]) ?><?= $station["city"] ? ", " . fuelEscape($station["city"]) : "" ?></p>
                            </div>

                            <div class="station-status-block">
                                <span>FUEL STATUS</span>
                                <strong class="fuel-status status-<?= fuelEscape($status) ?>"><?= fuelEscape($statusLabel) ?></strong>
                                <div class="station-vote-counts">
                                    <span class="available-count"><?= (int) ($station["available_votes"] ?? 0) ?> Available</span>
                                    <span class="out-count"><?= (int) ($station["out_votes"] ?? 0) ?> Not Available</span>
                                </div>
                            </div>

                            <div class="station-updated">
                                <span>UPDATED AT</span>
                                <strong><?= fuelEscape($updatedText) ?></strong>
                            </div>

                            <div class="station-votes">
                                <form method="POST" action="../../controllers/FuelController.php?action=vote">
                                    <input type="hidden" name="station_id" value="<?= (int) $station["station_id"] ?>">
                                    <button type="submit" name="vote" value="available" class="vote-button vote-available">VOTE ON<br>AVAILABLE</button>
                                    <button type="submit" name="vote" value="out_of_stock" class="vote-button vote-out">VOTE ON<br>NOT AVAILABLE</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>
            </div>

            <aside class="fuel-side-column">
                <section class="fuel-guide side-fuel-box">
                    <h3>Fuel Availability Guide</h3>
                    <div class="guide-row"><span class="guide-dot guide-green"></span><div><strong>Available</strong><small>Fuel is readily available</small></div></div>
                    <div class="guide-row"><span class="guide-dot guide-red"></span><div><strong>Out of Stock</strong><small>Currently not available</small></div></div>
                    <div class="guide-row"><span class="guide-dot guide-blue"></span><div><strong>Unknown</strong><small>Status not yet updated</small></div></div>
                </section>

                <div class="fuel-side-spacer"></div>

                <section class="shortage-box side-fuel-box">
                    <h3>Inform About Shortage</h3>
                    <p>Help keep the information accurate by reporting fuel shortages in your area.</p>
                </section>

                <section class="fuel-help-box side-fuel-box">
                    <h3>Need Help?</h3>
                    <p>For any urgent fuel supply issues, contact the emergency hotline.</p>
                    <a href="tel:117">Call Emergency Hotline</a>
                </section>
            </aside>
        </section>

        <?php include __DIR__ . "/../layouts/footer.php"; ?>
    </main>
</div>
