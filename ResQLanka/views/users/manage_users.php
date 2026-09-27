<?php
$pageTitle = "Manage Volunteers | ResQ Lanka";
$pageCSS = "../../css/manage_volunteers.css";
$activePage = "volunteers";
require_once __DIR__ . "/../../config/session.php";
requireRole("district_admin");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<div class="app-layout">
    <?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>
    <main class="main-content">

        
        <section class="page-header">

            <div>
                <h2>Manage Volunteers</h2>

                <p>
                    View and manage volunteers in your district
                </p>
            </div>

            <div class="district-badge">
                <i class="fa-solid fa-location-dot"></i>
                District Gampaha
            </div>

        </section>


       
        <section class="stats-grid">


            <!-- TOTAL VOLUNTEERS -->

            <article class="stat-card blue">

                <div class="stat-icon">
                    <i class="fa-solid fa-globe"></i>
                </div>

                <div class="stat-info">

                    <span class="stat-number">
                        128
                    </span>

                    <span class="stat-label">
                        Total Volunteers
                    </span>

                </div>

            </article>


            <!-- AVAILABLE TODAY -->

            <article class="stat-card green">

                <div class="stat-icon">
                    <i class="fa-regular fa-circle-check"></i>
                </div>

                <div class="stat-info">

                    <span class="stat-number">
                        74
                    </span>

                    <span class="stat-label">
                        Available Today
                    </span>

                </div>

            </article>


            <!-- ASSIGNED -->

            <article class="stat-card orange">

                <div class="stat-icon">
                    <i class="fa-regular fa-clipboard"></i>
                </div>

                <div class="stat-info">

                    <span class="stat-number">
                        36
                    </span>

                    <span class="stat-label">
                        Assigned to Tasks
                    </span>

                </div>

            </article>


            <!-- PENDING -->

            <article class="stat-card red">

                <div class="stat-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="stat-info">

                    <span class="stat-number">
                        12
                    </span>

                    <span class="stat-label">
                        Pending Applications
                    </span>

                </div>

            </article>

        </section>


       

        <section class="filter-section">

            <button class="filter-button active">
                <span></span>
                All Volunteers
            </button>

            <button class="filter-button available">
                <span></span>
                Available
            </button>

            <button class="filter-button assigned">
                <span></span>
                Assigned
            </button>

            <button class="filter-button pending">
                <span></span>
                Pending
            </button>

        </section>


        

        <section class="directory-card">

            <div class="section-title">

                <h3>
                    Volunteer Directory
                    <span>(6)</span>
                </h3>

            </div>


            <div class="table-wrapper">

                <table class="volunteer-table">

                    <thead>

                        <tr>

                            <th>Volunteer Name</th>

                            <th>Location</th>

                            <th>Skills</th>

                            <th>Availability</th>

                            <th>Status</th>

                            <th>Completed Assignments</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>


                        <!-- VOLUNTEER 1 -->

                        <tr>

                            <td>
                                <strong>Nimal Perera</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Kelaniya

                                </span>

                            </td>

                            <td>
                                First Aid, Logistics
                            </td>

                            <td>
                                Full Day
                            </td>

                            <td>

                                <span class="status available-status">
                                    <span></span>
                                    Available
                                </span>

                            </td>

                            <td>
                                18
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button class="view-button">
                                        View Details
                                    </button>

                                    <button class="complete-button">
                                        <i class="fa-regular fa-circle-check"></i>
                                        Mark as Completed
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- VOLUNTEER 2 -->

                        <tr>

                            <td>
                                <strong>Tharushi Fernando</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Negombo

                                </span>

                            </td>

                            <td>
                                Driving, Logistics
                            </td>

                            <td>
                                Weekends
                            </td>

                            <td>

                                <span class="status assigned-status">
                                    <span></span>
                                    Assigned
                                </span>

                            </td>

                            <td>
                                24
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button class="view-button">
                                        View Details
                                    </button>

                                    <button class="complete-button">
                                        <i class="fa-regular fa-circle-check"></i>
                                        Mark as Completed
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- VOLUNTEER 3 -->

                        <tr>

                            <td>
                                <strong>Kasun Silva</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Ja-Ela

                                </span>

                            </td>

                            <td>
                                First Aid, Rescue Support
                            </td>

                            <td>
                                Evenings
                            </td>

                            <td>

                                <span class="status pending-status">
                                    <span></span>
                                    Pending Review
                                </span>

                            </td>

                            <td>
                                0
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button class="view-button">
                                        View Details
                                    </button>

                                    <button class="complete-button">
                                        <i class="fa-regular fa-circle-check"></i>
                                        Mark as Completed
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- VOLUNTEER 4 -->

                        <tr>

                            <td>
                                <strong>Dilini Jayawardana</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Mirigama

                                </span>

                            </td>

                            <td>
                                Community Coordination
                            </td>

                            <td>
                                Full Day
                            </td>

                            <td>

                                <span class="status assigned-status">
                                    <span></span>
                                    Assigned
                                </span>

                            </td>

                            <td>
                                12
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button class="view-button">
                                        View Details
                                    </button>

                                    <button class="complete-button">
                                        <i class="fa-regular fa-circle-check"></i>
                                        Mark as Completed
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- VOLUNTEER 5 -->

                        <tr>

                            <td>
                                <strong>Ravindu Perera</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Gampaha

                                </span>

                            </td>

                            <td>
                                Medical Support
                            </td>

                            <td>
                                Full Day
                            </td>

                            <td>

                                <span class="status available-status">
                                    <span></span>
                                    Available
                                </span>

                            </td>

                            <td>
                                21
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button class="view-button">
                                        View Details
                                    </button>

                                    <button class="complete-button">
                                        <i class="fa-regular fa-circle-check"></i>
                                        Mark as Completed
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- VOLUNTEER 6 -->

                        <tr>

                            <td>
                                <strong>Shalini Fernando</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Wattala

                                </span>

                            </td>

                            <td>
                                Crowd Management
                            </td>

                            <td>
                                Weekends
                            </td>

                            <td>

                                <span class="status available-status">
                                    <span></span>
                                    Available
                                </span>

                            </td>

                            <td>
                                9
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button class="view-button">
                                        View Details
                                    </button>

                                    <button class="complete-button">
                                        <i class="fa-regular fa-circle-check"></i>
                                        Mark as Completed
                                    </button>

                                </div>

                            </td>

                        </tr>


                    </tbody>

                </table>

            </div>

        </section>

        

        <section class="applications-card">

            <div class="section-title">

                <h3>
                    Recent Volunteer Applications
                </h3>

            </div>


            <div class="applications-table-wrapper">

                <table class="applications-table">

                    <thead>

                        <tr>

                            <th>Applicant Name</th>

                            <th>Location</th>

                            <th>Submitted</th>

                            <th>Preferred Role</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>


                        <tr>

                            <td>
                                <strong>Lahiru Madushan</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Negombo

                                </span>

                            </td>

                            <td>

                                <span class="submitted">

                                    <i class="fa-regular fa-clock"></i>

                                    July 10, 2026 10:15 AM

                                </span>

                            </td>

                            <td>
                                Logistics Support
                            </td>

                            <td>
                                <button class="review-button">
                                    Review
                                </button>
                            </td>

                        </tr>


                        <tr>

                            <td>
                                <strong>Pabasara Jayawardana</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Kelaniya

                                </span>

                            </td>

                            <td>

                                <span class="submitted">

                                    <i class="fa-regular fa-clock"></i>

                                    July 20, 2026 12:15 PM

                                </span>

                            </td>

                            <td>
                                First-Aid Responder
                            </td>

                            <td>
                                <button class="review-button">
                                    Review
                                </button>
                            </td>

                        </tr>


                        <tr>

                            <td>
                                <strong>Hasitha Fernando</strong>
                            </td>

                            <td>

                                <span class="location">

                                    <i class="fa-solid fa-location-dot"></i>

                                    Ja-Ela

                                </span>

                            </td>

                            <td>

                                <span class="submitted">

                                    <i class="fa-regular fa-clock"></i>

                                    July 29, 2026 7:15 AM

                                </span>

                            </td>

                            <td>
                                Crowd Management
                            </td>

                            <td>
                                <button class="review-button">
                                    Review
                                </button>
                            </td>

                        </tr>


                    </tbody>

                </table>

            </div>

        </section>


    </main>

</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>

