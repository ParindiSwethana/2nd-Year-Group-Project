<?php

$pageTitle = "Active Disasters | ResQ Lanka";

$pageCSS = "../../css/active_disasters.css";
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
| Disaster Data
|--------------------------------------------------------------------------
*/

$disasters = [
    [
        "severity" => "CRITICAL",
        "severity_class" => "critical",
        "title" => "Flooding - Kelani River Basin",
        "location" => "Kelaniya, Gampaha",
        "description" => "Heavy rainfall has caused severe flooding in low-lying areas along the Kelani River. several communities are affected.",
        "reported" => "Reported 2 hours ago",
        "volunteers" => "120 volunteers requested",
        "image" => "../../images/flood.jpeg" 
    ],
    [
        "severity" => "MODERATE",
        "severity_class" => "moderate",
        "title" => "Landslide Risk Warning",
        "location" => "Kegalle, Ratnapura",
        "description" => "Due to continuous rainfall, there is a high risk of landslides in hilly areas and along main roads.",
        "reported" => "Reported 5 hours ago",
        "volunteers" => "80 volunteers requested",
        "image" => "../../images/landslide.jpeg" 
    ],
    [
        "severity" => "MINOR",
        "severity_class" => "minor",
        "title" => "Strong Winds Advisory",
        "location" => "Puttalam, Kurunegala",
        "description" => "Strong winds expected over the next 24 hours. Fishermen and coastal communities advised to be cautious.",
        "reported" => "Reported 8 hours ago",
        "volunteers" => "45 volunteers requested",
        "image" => "../../images/strong_wind.jpg" 
    ]
];

?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/sidebar.php";
?>

    <!-- =====================================================
         PAGE CONTENT
    ====================================================== -->
    <main class="assignments-content">

        <!-- PAGE HEADING -->
        <section class="page-heading">
            <h2>Active Disasters</h2>
            <p>Stay updated on emergencies happening across Sri Lanka</p>
        </section>

        <!-- =================================================
             STATISTICS
        ================================================== -->
        <section class="assignment-stats">
            <!-- CRITICAL -->
            <article class="assignment-stat stat-box-critical">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-information stat-info-critical">
                    <strong>3</strong>
                    <span>Critical</span>
                    <small>High priority</small>
                </div>
            </article>

            <!-- MODERATE -->
            <article class="assignment-stat stat-box-moderate">
                <div class="stat-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div class="stat-information stat-info-moderate">
                    <strong>4</strong>
                    <span>Moderate</span>
                    <small>Monitor closely</small>
                </div>
            </article>

            <!-- MONITOR -->
            <article class="assignment-stat stat-box-monitor">
                <div class="stat-icon"><i class="fa-regular fa-circle-check"></i></div>
                <div class="stat-information stat-info-monitor">
                    <strong>7</strong>
                    <span>Monitor</span>
                    <small>Under control</small>
                </div>
            </article>

            <!-- VOLUNTEERS -->
            <article class="assignment-stat stat-box-volunteers">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-information stat-info-volunteers">
                    <strong>356</strong>
                    <span>Volunteers</span>
                    <small>Deployed</small>
                </div>
            </article>
        </section>

        <!-- =================================================
             FILTER BAR
        ================================================== -->
        <section class="filter-bar disaster-filter-bar">
            <div class="filter-controls-left">
                <div class="search-disaster">
                    <input type="text" placeholder="Search disasters">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <select class="custom-select">
                    <option>All Severity</option>
                </select>
                <select class="custom-select">
                    <option>All Districts</option>
                </select>
            </div>
            <div class="filter-controls-right">
                <span class="sort-text">Sort: <strong>Newest</strong></span>
            </div>
        </section>

        <!-- =================================================
             DISASTERS AREA
        ================================================== -->
        <div class="assignment-layout">

            <!-- =================================================
                 LEFT - DISASTER LIST
            ================================================== -->
            <section class="assignment-list">
                <?php foreach ($disasters as $index =>$disaster): ?>
                    <article class="assignment-card disaster-card border-<?= escape($disaster["severity_class"]) ?>">
                        
                        <!-- IMAGE -->
                        <img src="<?= escape($disaster["image"]) ?>" alt="Disaster Area" class="disaster-img">

                        <!-- INFO -->
                        <div class="disaster-info-main">
                            <div class="disaster-top-row">
                                <span class="status-badge badge-<?= escape($disaster["severity_class"]) ?>">
                                    <?= escape($disaster["severity"]) ?>
                                </span>
                                <a 
                                    href="disaster_details.php?id=<?= $index + 1 ?>"
                                    class="view-details btn-<?= escape($disaster["severity_class"]) ?>"
                                >
                                    View Details <i class="fa-solid fa-caret-down"></i>
                                </a>
                            </div>
                            
                            <h3 class="disaster-title"><?= escape($disaster["title"]) ?></h3>
                            
                            <div class="disaster-location">
                                <i class="fa-solid fa-location-dot"></i> <?= escape($disaster["location"]) ?>
                            </div>
                            
                            <p class="disaster-desc"><?= escape($disaster["description"]) ?></p>
                            
                            <div class="disaster-meta-footer">
                                <span><i class="fa-regular fa-clock"></i> <?= escape($disaster["reported"]) ?></span>
                                <span><i class="fa-solid fa-users"></i> <?= escape($disaster["volunteers"]) ?></span>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </section>

            <!-- =================================================
                 RIGHT SIDE
            ================================================== -->
            <aside class="right-panel">
                
                <!-- URGENT HELP -->
                <section class="side-box urgent-help-box">
                    <div class="urgent-header">
                        <div class="icon-ring">
                            <i class="fa-solid fa-life-ring"></i>
                        </div>
                        <h3>Need Help Urgently?</h3>
                    </div>
                    <p>If you are in danger or need immediate assistence, contact emergency services.</p>
                    <button type="button" class="emergency-btn">
                        <i class="fa-solid fa-phone"></i> Call Emergency Hotline
                    </button>
                </section>

                <!-- SAFETY TIPS -->
                <section class="side-box safety-tips-box">
                    <div class="tips-header">
                        <h3>Safety Tips</h3>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <ul class="tips-list">
                        <li>Stay away from flood water <i class="fa-solid fa-caret-right"></i></li>
                        <li>Monitor official updates <i class="fa-solid fa-caret-right"></i></li>
                        <li>Keeps emergency kit ready <i class="fa-solid fa-caret-right"></i></li>
                        <li>Follow evacuation orders <i class="fa-solid fa-caret-right"></i></li>
                        <li>Help vulnerable people <i class="fa-solid fa-caret-right"></i></li>
                    </ul>
                    <div class="stay-safe-alert">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <p>Stay informed. Stay prepareed.<br>Stay safe</p>
                    </div>
                </section>

            </aside>
        </div>
        <?php
            include __DIR__ . "/../layouts/footer.php";
        ?>

    </main>
</div>
