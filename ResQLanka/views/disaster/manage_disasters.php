<?php

$pageTitle = "Manage Disasters | ResQ Lanka";

$pageCSS = "../../css/manage_disasters.css";
$activePage = "disasters";

require_once __DIR__ . "/../../config/session.php";

$fullName = $_SESSION["name"] ?? "John";
$tier = $_SESSION["tier"] ?? "Bronze";
$points = $_SESSION["points"] ?? 0;
$firstName = explode(" ", trim($fullName))[0];

function escape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

/*
|--------------------------------------------------------------------------
| Manage Disasters Data
|--------------------------------------------------------------------------
*/

$ongoingDisasters = [
    [
        "title" => "Flooding in Kelaniya",
        "desc" => "Heavy rainfall causing waterlogging",
        "location" => "Kelaniya",
        "date" => "10th August 2026",
        "time" => "5.30PM",
        "priority" => "High",
        "priority_class" => "high",
        "icon" => "fa-house-flood-water",
        "icon_class" => "blue"
    ],
    [
        "title" => "Landslide in Mirigama",
        "desc" => "Heavy rains increase landslides",
        "location" => "Mirigama",
        "date" => "07th August 2026",
        "time" => "12.30AM",
        "priority" => "Medium",
        "priority_class" => "medium",
        "icon" => "fa-hill-rockslide",
        "icon_class" => "orange"
    ],
    [
        "title" => "Road Blockage in Ja-Ela",
        "desc" => "Fallen tree blocking main road",
        "location" => "Ja-Ela",
        "date" => "03rd August 2026",
        "time" => "1.00PM",
        "priority" => "High",
        "priority_class" => "high",
        "icon" => "fa-tree",
        "icon_class" => "green-alt"
    ],
    [
        "title" => "Strong Winds in Negombo",
        "desc" => "Strong winds affecting coastal area",
        "location" => "Negombo",
        "date" => "1st August 2026",
        "time" => "6.30PM",
        "priority" => "Low",
        "priority_class" => "low",
        "icon" => "fa-wind",
        "icon_class" => "purple"
    ]
];

$completedDisasters = [
    [
        "title" => "Flooding in Gampaha Town",
        "desc" => "Floodwaters receded after drainage",
        "location" => "Gampaha",
        "date" => "05th July 2026",
        "time" => "7.00AM",
        "duration" => "3 days"
    ],
    [
        "title" => "Thunderstorm in Wattala",
        "desc" => "Thunderstorm and heavy rain",
        "location" => "Wattala",
        "date" => "03rd July 2026",
        "time" => "10.30AM",
        "duration" => "1 day"
    ],
    [
        "title" => "Flooding in Divulapitiya",
        "desc" => "Drain overflow due to heavy rain",
        "location" => "Divulapitiya",
        "date" => "01st July 2026",
        "time" => "2.00PM",
        "duration" => "2 days"
    ]
];

?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/admin_sidebar.php";
?>


    <!-- =====================================================
         PAGE CONTENT
    ====================================================== -->
    <main class="assignments-content">

        <!-- PAGE HEADING & ACTION BUTTON -->
        <div class="page-header-actions">
            <div class="page-heading">
                <h2>Manage Disasters</h2>
                <p>View and manage the disasters reported in the district</p>
            </div>
            <button class="btn-primary">
                <i class="fa-solid fa-plus"></i> Create Disaster Assignment Request
            </button>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="summary-cards">
            <!-- Active Disasters -->
            <div class="summary-card active-card">
                <div class="card-icon blue-icon"><i class="fa-solid fa-bell"></i></div>
                <div class="card-data">
                    <h2>4</h2>
                    <div class="card-text">
                        <strong>Active Disasters</strong>
                        <span>Ongoing incidents</span>
                    </div>
                </div>
            </div>

            <!-- Completed Disasters -->
            <div class="summary-card completed-card">
                <div class="card-icon green-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="card-data">
                    <h2>3</h2>
                    <div class="card-text">
                        <strong>Completed Disasters</strong>
                        <span>All time</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABS BAR -->
        <div class="tabs-container">
            <div class="tabs">
                <a href="#" class="tab active">Ongoing</a>
                <a href="#" class="tab">Completed</a>
                <a href="#" class="tab">All</a>
            </div>
            <div class="tab-filter">
                <span>Gampaha District Only <i class="fa-solid fa-lock"></i></span>
            </div>
        </div>

        <!-- ONGOING DISASTERS TABLE -->
        <div class="table-section">
            <h3 class="table-title">Ongoing Disasters (<?= count($ongoingDisasters) ?>)</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Disaster Title</th>
                            <th>Location</th>
                            <th>Reported Date</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ongoingDisasters as $disaster): ?>
                        <tr>
                            <td>
                                <div class="title-cell">
                                    <div class="title-icon <?= escape($disaster['icon_class']) ?>">
                                        <i class="fa-solid <?= escape($disaster['icon']) ?>"></i>
                                    </div>
                                    <div class="title-text">
                                        <strong><?= escape($disaster['title']) ?></strong>
                                        <span><?= escape($disaster['desc']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="location-cell">
                                    <i class="fa-solid fa-location-dot"></i> <?= escape($disaster['location']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <div>
                                        <span><?= escape($disaster['date']) ?></span>
                                        <span class="time-sub"><?= escape($disaster['time']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="priority-badge <?= escape($disaster['priority_class']) ?>">
                                    <?= escape($disaster['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-indicator">
                                    <i class="fa-solid fa-circle"></i> Ongoing
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-outline-blue">View Details</button>
                                    <button class="btn-outline-green">
                                        <i class="fa-regular fa-circle-check"></i> Mark as Completed
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- COMPLETED DISASTERS TABLE -->
        <div class="table-section">
            <h3 class="table-title">Completed / Finished Disasters (<?= count($completedDisasters) ?>)</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Disaster Title</th>
                            <th>Location</th>
                            <th>Completed Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completedDisasters as $disaster): ?>
                        <tr>
                            <td>
                                <div class="title-cell">
                                    <div class="title-icon green-icon">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <div class="title-text">
                                        <strong><?= escape($disaster['title']) ?></strong>
                                        <span><?= escape($disaster['desc']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="location-cell">
                                    <i class="fa-solid fa-location-dot"></i> <?= escape($disaster['location']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <div>
                                        <span><?= escape($disaster['date']) ?></span>
                                        <span class="time-sub"><?= escape($disaster['time']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="duration-text"><?= escape($disaster['duration']) ?></span>
                            </td>
                            <td>
                                <span class="status-badge-completed">
                                    <i class="fa-regular fa-circle-check"></i> Completed
                                </span>
                            </td>
                            <td>
                                <button class="btn-outline-blue">View Details</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

         <?php
            include __DIR__ . "/../layouts/footer.php";
        ?>

    </main>
</div>
