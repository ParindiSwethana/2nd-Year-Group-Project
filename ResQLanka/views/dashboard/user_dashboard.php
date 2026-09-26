<?php

$pageTitle = "User Dashboard | ResQ Lanka";

$pageCSS = "../../css/user_dashboard.css";

$activePage = "dashboard";

require_once __DIR__ . "/../../config/session.php";

$fullName = $_SESSION["name"] ?? "John";
$tier = $_SESSION["tier"] ?? "Bronze";
$points = $_SESSION["points"] ?? 0;

$firstName = explode(
    " ",
    trim($fullName)
)[0];

function escapeValue($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/sidebar.php";
?>

<main class="dashboard-content">


    <section class="welcome-card">

        <div class="welcome-content">

            <div class="welcome-title">

                <span class="welcome-label">
                    VOLUNTEER DASHBOARD
                </span>

                <h2>
                    Hi, <?= escapeValue($firstName) ?>!
                    <span class="wave">👋</span>
                </h2>

                <p>
                    Here's what's happening across
                    ResQ Lanka today.
                </p>

            </div>

            <div class="tier-summary">

                <div class="tier-icon">
                    <i class="fa-solid fa-medal"></i>
                </div>

                <div>

                    <span>
                        Current Volunteer Tier
                    </span>

                    <strong>
                        <?= escapeValue($tier) ?>
                    </strong>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         STATISTICS
    ========================== -->

    <section class="stats-grid">

        <article class="stat-card stat-blue">

            <div class="stat-top">

                <div class="stat-icon">
                    <i class="fa-regular fa-clipboard"></i>
                </div>

                <span class="stat-trend">
                    Active
                </span>

            </div>

            <div class="stat-number">
                2
            </div>

            <h3>
                Ongoing Assignments
            </h3>

            <a href="../disaster/volunteer_assignments.php">

                View assignments

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </article>


        <article class="stat-card stat-orange">

            <div class="stat-top">

                <div class="stat-icon">
                    <i class="fa-regular fa-clock"></i>
                </div>

                <span class="stat-trend">
                    Total
                </span>

            </div>

            <div class="stat-number">
                18
            </div>

            <h3>
                Volunteer Hours
            </h3>

            <a href="../profile/view_profile.php">

                View activity

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </article>


        <article class="stat-card stat-purple">

            <div class="stat-top">

                <div class="stat-icon">
                    <i class="fa-solid fa-star"></i>
                </div>

                <span class="stat-trend">
                    Earned
                </span>

            </div>

            <div class="stat-number">
                <?= escapeValue($points) ?>
            </div>

            <h3>
                Assignment Points
            </h3>

            <a href="../profile/view_profile.php">

                View points

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </article>


        <article class="stat-card stat-green">

            <div class="stat-top">

                <div class="stat-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>

                <span class="stat-trend">
                    Ranking
                </span>

            </div>

            <div class="stat-tier">
                <?= escapeValue($tier) ?>
            </div>

            <h3>
                Volunteer Tier
            </h3>

            <a href="../profile/view_profile.php">

                View ranking

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </article>

    </section>


    <!-- =========================
         MAIN DASHBOARD GRID
    ========================== -->

    <div class="dashboard-grid">


        <!-- =========================
             ONGOING ASSIGNMENTS
        ========================== -->

        <section class="dashboard-panel assignments-panel">

            <div class="panel-heading">

                <div>

                    <span class="section-label">
                        VOLUNTEERING
                    </span>

                    <h2>

                        <i class="fa-regular fa-file-lines"></i>

                        Ongoing Assignments

                    </h2>

                </div>

                <a
                    href="../disaster/volunteer_assignments.php"
                    class="view-all"
                >

                    View all

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <article class="assignment-item blue-assignment">

                <div class="assignment-number">
                    1
                </div>

                <div class="assignment-icon">
                    <i class="fa-solid fa-tree"></i>
                </div>

                <div class="assignment-content">

                    <div class="assignment-title-row">

                        <h3>
                            Beach Clean-Up in Mount Lavinia
                        </h3>

                        <span class="active-badge">
                            ACTIVE
                        </span>

                    </div>

                    <div class="assignment-meta">

                        <span>
                            <i class="fa-regular fa-calendar"></i>
                            25 Aug 2026
                        </span>

                        <span>
                            <i class="fa-regular fa-clock"></i>
                            7:00 AM – 12:00 PM
                        </span>

                        <span>
                            <i class="fa-solid fa-users"></i>
                            100 Volunteers
                        </span>

                        <span>
                            <i class="fa-solid fa-location-dot"></i>
                            Mount Lavinia Beach
                        </span>

                    </div>

                </div>

                <a
                    href="../disaster/assignment_details.php"
                    class="assignment-arrow"
                >

                    <i class="fa-solid fa-chevron-right"></i>

                </a>

            </article>


            <!-- Assignment 2 -->

            <article class="assignment-item red-assignment">

                <div class="assignment-number">
                    2
                </div>

                <div class="assignment-icon">
                    <i class="fa-solid fa-city"></i>
                </div>

                <div class="assignment-content">

                    <div class="assignment-title-row">

                        <h3>
                            City Clean-Up in Colombo 06
                        </h3>

                        <span class="active-badge">
                            ACTIVE
                        </span>

                    </div>

                    <div class="assignment-meta">

                        <span>
                            <i class="fa-regular fa-calendar"></i>
                            30 Aug 2026
                        </span>

                        <span>
                            <i class="fa-regular fa-clock"></i>
                            6:00 AM – 12:00 PM
                        </span>

                        <span>
                            <i class="fa-solid fa-users"></i>
                            50 Volunteers
                        </span>

                        <span>
                            <i class="fa-solid fa-location-dot"></i>
                            Colombo 06
                        </span>

                    </div>

                </div>

                <a
                    href="../disaster/assignment_details.php"
                    class="assignment-arrow"
                >

                    <i class="fa-solid fa-chevron-right"></i>

                </a>

            </article>

        </section>


        <!-- =========================
             DISASTER ALERTS
        ========================== -->

        <section class="dashboard-panel alerts-panel">

            <div class="panel-heading">

                <div>

                    <span class="section-label">
                        LIVE UPDATES
                    </span>

                    <h2>

                        <i class="fa-solid fa-triangle-exclamation"></i>

                        Disaster Alerts

                    </h2>

                </div>

            </div>


            <article class="alert-card critical-alert">

                <div class="alert-icon">
                    <i class="fa-solid fa-water"></i>
                </div>

                <div class="alert-content">

                    <div class="alert-top">

                        <span class="alert-level">
                            HIGH PRIORITY
                        </span>

                        <span class="alert-time">
                            Today
                        </span>

                    </div>

                    <h3>
                        Flood Warning
                    </h3>

                    <p>
                        Increased water levels reported
                        across several areas in Gampaha.
                    </p>

                    <a href="../disaster/disaster_details.php">

                        View details

                        <i class="fa-solid fa-arrow-right"></i>

                    </a>

                </div>

            </article>


            <article class="alert-card advisory-alert">

                <div class="alert-icon">
                    <i class="fa-solid fa-cloud-showers-heavy"></i>
                </div>

                <div class="alert-content">

                    <div class="alert-top">

                        <span class="alert-level">
                            ADVISORY
                        </span>

                        <span class="alert-time">
                            Updated
                        </span>

                    </div>

                    <h3>
                        Heavy Rain Advisory
                    </h3>

                    <p>
                        Volunteers are advised to remain
                        prepared for possible response requests.
                    </p>

                </div>

            </article>

        </section>

    </div>


    <!-- =========================
         COMMUNITY STRIP
    ========================== -->

    <section class="community-strip">

        <div class="community-message">

            <div class="community-main-icon">

                <i class="fa-solid fa-shield-halved"></i>

            </div>

            <div>

                <h3>
                    Every Action Counts.
                </h3>

                <p>
                    Prepared communities save lives.
                </p>

            </div>

        </div>


        <div class="community-actions">

            <div class="community-item">

                <i class="fa-solid fa-suitcase-rolling"></i>

                <span>
                    Be Prepared
                </span>

            </div>

            <div class="community-divider"></div>

            <div class="community-item">

                <i class="fa-solid fa-users"></i>

                <span>
                    Help Others
                </span>

            </div>

            <div class="community-divider"></div>

            <div class="community-item">

                <i class="fa-solid fa-hand-holding-heart"></i>

                <span>
                    Save Lives
                </span>

            </div>

        </div>

    </section>


<?php
include __DIR__ . "/../layouts/footer.php";
?>

</main>

</div>
