<?php

require_once __DIR__ . "/../../config/session.php";
require_once __DIR__ . "/../../controllers/FuelController.php";

$pageTitle = "Fuel Station Tracker | ResQ Lanka";
$pageCSS = "../../css/fuel_tracker.css";
$activePage = "fuel";

$loggedIn = isLoggedIn();
$role = $_SESSION["role"] ?? null;

$fuelController = new FuelController();

$location = trim($_GET["location"] ?? "");
$selectedType = trim($_GET["type"] ?? "");

$stations = $fuelController->searchStations();
$stationTypes = $fuelController->getStationTypes();

function fuelEscape($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

function fuelStatusLabel(string $status): string
{
    if ($status === "available") {
        return "Available";
    }

    if ($status === "out_of_stock") {
        return "Out of Stock";
    }

    return "Unknown";
}

function fuelStatusClass(string $status): string
{
    if ($status === "available") {
        return "available";
    }

    if ($status === "out_of_stock") {
        return "out";
    }

    return "unknown";
}

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<div class="fuel-page">
    <section class="fuel-hero">
        <div class="fuel-hero-content">
            <div class="fuel-hero-icon">
                <i class="fa-solid fa-gas-pump"></i>
            </div>

            <h1>Fuel Station Tracker</h1>

            <p>
                Find fuel availability near you.
                Updated by the community in real time.
            </p>
        </div>
    </section>

    <main class="fuel-content">
        <section class="fuel-search-section">
            <form method="GET" action="search.php" class="fuel-search-form">
                <div class="fuel-search-field">
                    <i class="fa-solid fa-location-dot"></i>

                    <input
                        type="text"
                        name="location"
                        value="<?= fuelEscape($location) ?>"
                        placeholder="Enter city, district or station name"
                    >
                </div>

                <div class="fuel-type-field">
                    <i class="fa-solid fa-gas-pump"></i>

                    <select name="type">
                        <option value="">All Stations</option>

                        <?php foreach ($stationTypes as $type): ?>
                            <option
                                value="<?= fuelEscape($type) ?>"
                                <?= $selectedType === $type ? "selected" : "" ?>
                            >
                                <?= fuelEscape($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="fuel-search-button">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Search
                </button>
            </form>
        </section>

        <?php if (isset($_GET["updated"])): ?>
            <div class="fuel-message success-message">
                <i class="fa-solid fa-circle-check"></i>
                Your fuel availability vote has been recorded.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET["error"])): ?>
            <div class="fuel-message error-message">
                <i class="fa-solid fa-circle-exclamation"></i>
                Unable to update the fuel availability. Please try again.
            </div>
        <?php endif; ?>

        <section class="fuel-results-heading">
            <div>
                <h2>Nearby Fuel Stations</h2>

                <p>
                    <?= count($stations) ?>
                    station<?= count($stations) === 1 ? "" : "s" ?> found
                </p>
            </div>

            <div class="fuel-window-info">
                <i class="fa-regular fa-clock"></i>

                <span>
                    Votes reset at 12 AM and 12 PM
                </span>
            </div>
        </section>

        <?php if (empty($stations)): ?>
            <section class="fuel-empty-state">
                <i class="fa-solid fa-gas-pump"></i>
                <h3>No fuel stations found</h3>
                <p>Try searching using another location or station type.</p>
            </section>
        <?php else: ?>

            <section class="fuel-station-list">

                <?php foreach ($stations as $station): ?>

                    <?php
                    $status = $station["current_status"] ?? "unknown";
                    $statusClass = fuelStatusClass($status);
                    $availableVotes = (int) ($station["available_votes"] ?? 0);
                    $outVotes = (int) ($station["out_votes"] ?? 0);
                    ?>

                    <article
                        class="fuel-station-card"
                        id="station-<?= (int) $station["station_id"] ?>"
                    >
                        <div class="station-main">
                            <div class="station-icon">
                                <i class="fa-solid fa-gas-pump"></i>
                            </div>

                            <div class="station-information">
                                <div class="station-title-row">
                                    <div>
                                        <h3>
                                            <?= fuelEscape($station["station_name"]) ?>
                                        </h3>

                                        <span class="station-type">
                                            <?= fuelEscape($station["station_type"]) ?>
                                        </span>
                                    </div>

                                    <span class="fuel-status status-<?= $statusClass ?>">
                                        <span class="status-dot"></span>
                                        <?= fuelEscape(fuelStatusLabel($status)) ?>
                                    </span>
                                </div>

                                <div class="station-location">
                                    <i class="fa-solid fa-location-dot"></i>

                                    <span>
                                        <?= fuelEscape($station["address"]) ?>,
                                        <?= fuelEscape($station["city"]) ?>,
                                        <?= fuelEscape($station["district"]) ?>
                                    </span>
                                </div>

                                <div class="fuel-vote-summary">
                                    <div class="vote-summary-item available-summary">
                                        <span class="vote-summary-icon">
                                            <i class="fa-solid fa-check"></i>
                                        </span>

                                        <div>
                                            <strong><?= $availableVotes ?></strong>
                                            <span>Available</span>
                                        </div>
                                    </div>

                                    <div class="vote-summary-item unavailable-summary">
                                        <span class="vote-summary-icon">
                                            <i class="fa-solid fa-xmark"></i>
                                        </span>

                                        <div>
                                            <strong><?= $outVotes ?></strong>
                                            <span>Not Available</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="station-vote-area">
                            <p>Is fuel available now?</p>

                            <?php if ($loggedIn && $role === "registered_user"): ?>

                                <div class="vote-buttons">
                                    <form method="POST" action="../../controllers/FuelController.php?action=vote">
                                        <input
                                            type="hidden"
                                            name="station_id"
                                            value="<?= (int) $station["station_id"] ?>"
                                        >

                                        <input type="hidden" name="vote" value="available">

                                        <button
                                            type="submit"
                                            class="vote-button available-button"
                                        >
                                            <i class="fa-solid fa-check"></i>
                                            Available
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="../../controllers/FuelController.php?action=vote"
                                    >
                                        <input
                                            type="hidden"
                                            name="station_id"
                                            value="<?= (int) $station["station_id"] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="vote"
                                            value="out_of_stock"
                                        >

                                        <button
                                            type="submit"
                                            class="vote-button unavailable-button"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                            Not Available
                                        </button>
                                    </form>
                                </div>

                                <small class="vote-note">
                                    You can vote once and change your vote during the current 12-hour period.
                                </small>

                            <?php elseif (!$loggedIn): ?>

                                <a
                                    href="../auth/login.php"
                                    class="fuel-login-button"
                                >
                                    Log in to update availability
                                </a>

                            <?php else: ?>

                                <small class="vote-note">
                                    Fuel availability voting is available to registered users.
                                </small>

                            <?php endif; ?>
                        </div>
                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

        <?php
        include __DIR__ . "/../layouts/footer.php";
        ?>

    </main>
</div>
