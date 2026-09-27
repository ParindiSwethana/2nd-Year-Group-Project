<?php

$pageTitle = "District Admin Dashboard | ResQ Lanka";
$pageCSS = "../../css/district_dashboard.css";
$activePage = "dashboard";

require_once __DIR__ . "/../../config/session.php";

requireRole(["district_admin"]);

$firstName = $_SESSION["first_name"] ?? "Admin";
$district = $_SESSION["district"] ?? "Gampaha";

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<div class="app-layout">

    <?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>

    <main class="district-dashboard">

        <section class="dashboard-welcome">

            <div>
                <span class="dashboard-label">DISTRICT ADMINISTRATION</span>

                <h2>
                    Hi, <?= htmlspecialchars($firstName, ENT_QUOTES, "UTF-8") ?>!
                    <span class="wave">👋</span>
                </h2>

                <p>
                    Here’s what’s happening in your district today.
                </p>
            </div>

            <div class="district-admin-badge">
                <i class="fa-solid fa-location-dot"></i>

                <?= strtoupper(
                    htmlspecialchars($district, ENT_QUOTES, "UTF-8")
                ) ?> ADMIN
            </div>

        </section>

        <section class="dashboard-stats">

            <article class="stat-card">

                <div class="stat-icon stat-blue">
                    <i class="fa-solid fa-earth-asia"></i>
                </div>

                <div>
                    <strong>9</strong>
                    <span>Volunteer Programs Created</span>
                </div>

            </article>

            <article class="stat-card">

                <div class="stat-icon stat-green">
                    <i class="fa-solid fa-location-dot"></i>
                </div>

                <div>
                    <strong>20</strong>
                    <span>Cities Controlled</span>
                </div>

            </article>

            <article class="stat-card">

                <div class="stat-icon stat-orange">
                    <i class="fa-regular fa-clipboard"></i>
                </div>

                <div>
                    <strong>3</strong>
                    <span>Ongoing Assignments</span>
                </div>

            </article>

            <article class="stat-card">

                <div class="stat-icon stat-red">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div>
                    <strong>60</strong>
                    <span>Pending Applications</span>
                </div>

            </article>

        </section>

        <div class="dashboard-content-grid">

            <div class="dashboard-main-column">

                <section class="dashboard-card">

                    <div class="card-heading">

                        <div>
                            <span class="section-label">INCIDENT REPORTS</span>
                            <h3>User Informed Disasters</h3>
                        </div>

                        <a href="../disaster/manage_disasters.php">
                            View All
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                    <div class="dashboard-table-wrap">

                        <table class="dashboard-table">

                            <thead>
                                <tr>
                                    <th>Disaster Title</th>
                                    <th>Location</th>
                                    <th>Priority</th>
                                    <th>Reported</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>

                                    <td>
                                        <div class="disaster-name">

                                            <div class="mini-icon blue-mini">
                                                <i class="fa-solid fa-water"></i>
                                            </div>

                                            <span>Flooding in Kelaniya</span>

                                        </div>
                                    </td>

                                    <td>
                                        <i class="fa-solid fa-location-dot location-icon"></i>
                                        Kelaniya
                                    </td>

                                    <td>
                                        <span class="priority high">
                                            High
                                        </span>
                                    </td>

                                    <td>10 min ago</td>

                                    <td>
                                        <span class="new-badge">New</span>
                                    </td>

                                </tr>

                                <tr>

                                    <td>
                                        <div class="disaster-name">

                                            <div class="mini-icon brown-mini">
                                                <i class="fa-solid fa-mountain"></i>
                                            </div>

                                            <span>Landslide near Yakkala</span>

                                        </div>
                                    </td>

                                    <td>
                                        <i class="fa-solid fa-location-dot location-icon"></i>
                                        Yakkala
                                    </td>

                                    <td>
                                        <span class="priority high">
                                            High
                                        </span>
                                    </td>

                                    <td>45 min ago</td>

                                    <td>
                                        <span class="new-badge">New</span>
                                    </td>

                                </tr>

                                <tr>

                                    <td>
                                        <div class="disaster-name">

                                            <div class="mini-icon green-mini">
                                                <i class="fa-solid fa-tree"></i>
                                            </div>

                                            <span>Fallen tree blockage</span>

                                        </div>
                                    </td>

                                    <td>
                                        <i class="fa-solid fa-location-dot location-icon"></i>
                                        Negombo
                                    </td>

                                    <td>
                                        <span class="priority medium">
                                            Medium
                                        </span>
                                    </td>

                                    <td>1 hr ago</td>

                                    <td>
                                        <span class="new-badge">New</span>
                                    </td>

                                </tr>

                                <tr>

                                    <td>
                                        <div class="disaster-name">

                                            <div class="mini-icon blue-mini">
                                                <i class="fa-solid fa-water"></i>
                                            </div>

                                            <span>Waterlogging in Ja-ela</span>

                                        </div>
                                    </td>

                                    <td>
                                        <i class="fa-solid fa-location-dot location-icon"></i>
                                        Ja-ela
                                    </td>

                                    <td>
                                        <span class="priority low">
                                            Low
                                        </span>
                                    </td>

                                    <td>2 hrs ago</td>

                                    <td>
                                        <span class="new-badge">New</span>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </section>

                <section class="dashboard-card">

                    <div class="card-heading">

                        <div>
                            <span class="section-label">ACTIVE RESPONSE</span>
                            <h3>Ongoing Disasters</h3>
                        </div>

                        <a href="../disaster/manage_disasters.php">
                            View All
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                    <div class="ongoing-list">

                        <article class="ongoing-item">

                            <div class="ongoing-info">

                                <div class="mini-icon blue-mini">
                                    <i class="fa-solid fa-water"></i>
                                </div>

                                <div>
                                    <strong>Flooding in Attanagalla</strong>
                                    <span>Heavy rainfall causing flooding</span>
                                </div>

                            </div>

                            <div class="ongoing-location">
                                <i class="fa-solid fa-location-dot"></i>
                                Attanagalla
                            </div>

                            <span class="priority high">High</span>

                            <span class="response-status ongoing">
                                <i class="fa-solid fa-circle"></i>
                                Ongoing
                            </span>

                        </article>

                        <article class="ongoing-item">

                            <div class="ongoing-info">

                                <div class="mini-icon brown-mini">
                                    <i class="fa-solid fa-mountain"></i>
                                </div>

                                <div>
                                    <strong>Landslide in Mirigama</strong>
                                    <span>Soil movement reported</span>
                                </div>

                            </div>

                            <div class="ongoing-location">
                                <i class="fa-solid fa-location-dot"></i>
                                Mirigama
                            </div>

                            <span class="priority high">High</span>

                            <span class="response-status ongoing">
                                <i class="fa-solid fa-circle"></i>
                                Ongoing
                            </span>

                        </article>

                        <article class="ongoing-item">

                            <div class="ongoing-info">

                                <div class="mini-icon purple-mini">
                                    <i class="fa-solid fa-wind"></i>
                                </div>

                                <div>
                                    <strong>Cyclone Warning</strong>
                                    <span>Strong winds expected</span>
                                </div>

                            </div>

                            <div class="ongoing-location">
                                <i class="fa-solid fa-location-dot"></i>
                                Gampaha
                            </div>

                            <span class="priority medium">Medium</span>

                            <span class="response-status monitoring">
                                <i class="fa-solid fa-circle"></i>
                                Monitoring
                            </span>

                        </article>

                        <article class="ongoing-item">

                            <div class="ongoing-info">

                                <div class="mini-icon green-mini">
                                    <i class="fa-solid fa-tree"></i>
                                </div>

                                <div>
                                    <strong>Road Blockage in Dompe</strong>
                                    <span>Tree fallen on main road</span>
                                </div>

                            </div>

                            <div class="ongoing-location">
                                <i class="fa-solid fa-location-dot"></i>
                                Dompe
                            </div>

                            <span class="priority low">Low</span>

                            <span class="response-status progress">
                                <i class="fa-solid fa-circle"></i>
                                In Progress
                            </span>

                        </article>

                    </div>

                </section>

            </div>

            <aside class="dashboard-side-column">

                <section class="dashboard-card inventory-alert-card">

                    <div class="card-heading">

                        <div>
                            <span class="section-label">STOCK STATUS</span>
                            <h3>Inventory Alerts</h3>
                        </div>

                        <a href="../inventory/inventory_list.php">
                            View All
                        </a>

                    </div>

                    <div class="inventory-alert-list">

                        <article class="inventory-alert">

                            <div class="inventory-alert-info">

                                <div class="alert-icon water">
                                    <i class="fa-solid fa-droplet"></i>
                                </div>

                                <div>
                                    <strong>Bottled Water (1.5L)</strong>
                                    <span>Critical stock</span>
                                </div>

                            </div>

                            <strong class="stock-count">
                                50 Units
                            </strong>

                        </article>

                        <article class="inventory-alert">

                            <div class="inventory-alert-info">

                                <div class="alert-icon medical">
                                    <i class="fa-solid fa-kit-medical"></i>
                                </div>

                                <div>
                                    <strong>First Aid Kits</strong>
                                    <span>Critical stock</span>
                                </div>

                            </div>

                            <strong class="stock-count">
                                22 Units
                            </strong>

                        </article>

                        <article class="inventory-alert">

                            <div class="inventory-alert-info">

                                <div class="alert-icon shelter">
                                    <i class="fa-solid fa-house"></i>
                                </div>

                                <div>
                                    <strong>Blankets</strong>
                                    <span>Critical stock</span>
                                </div>

                            </div>

                            <strong class="stock-count">
                                05 Units
                            </strong>

                        </article>

                        <article class="inventory-alert">

                            <div class="inventory-alert-info">

                                <div class="alert-icon food">
                                    <i class="fa-solid fa-bowl-food"></i>
                                </div>

                                <div>
                                    <strong>Instant Noodles</strong>
                                    <span>Critical stock</span>
                                </div>

                            </div>

                            <strong class="stock-count">
                                10 Units
                            </strong>

                        </article>

                        <article class="inventory-alert">

                            <div class="inventory-alert-info">

                                <div class="alert-icon supply">
                                    <i class="fa-solid fa-box"></i>
                                </div>

                                <div>
                                    <strong>Soya Meat</strong>
                                    <span>Critical stock</span>
                                </div>

                            </div>

                            <strong class="stock-count">
                                17 Units
                            </strong>

                        </article>

                    </div>

                </section>

                <section class="dashboard-card quick-actions-card">

                    <div class="card-heading">

                        <div>
                            <span class="section-label">SHORTCUTS</span>
                            <h3>Quick Actions</h3>
                        </div>

                    </div>

                    <div class="quick-actions">

                        <a
                            href="../disaster/manage_disasters.php"
                            class="quick-action-card blue-action"
                        >
                            <i class="fa-solid fa-plus"></i>
                            <span>Create Assignment</span>
                        </a>

                        <a
                            href="../disaster/review_applications.php"
                            class="quick-action-card green-action"
                        >
                            <i class="fa-solid fa-user-check"></i>
                            <span>Review Applications</span>
                        </a>

                        <a
                            href="../inventory/inventory_list.php"
                            class="quick-action-card orange-action"
                        >
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <span>Update Inventory</span>
                        </a>

                    </div>

                </section>

            </aside>

        </div>

    </main>

</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
