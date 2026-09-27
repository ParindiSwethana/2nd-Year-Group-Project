<?php
$pageTitle = "International Volunteering | ResQ Lanka";
$pageCSS = "../../css/international_volunteering.css";
$activePage = "international";

require_once __DIR__ . "/../../config/session.php";
requireRole("registered_user");

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>

<div class="app-layout">
    <?php include __DIR__ . "/../layouts/sidebar.php"; ?>

    <main class="international-content">
        <section class="international-panel">
            <div class="international-heading">
                <div>
                    <span class="section-label">GLOBAL OPPORTUNITIES</span>
                    <h2>International Volunteering</h2>
                    <p>Explore available abroad opportunities and apply online.</p>
                </div>
            </div>

            <section class="international-stats">
                <article class="international-stat stat-blue">
                    <div class="international-stat-icon"><i class="fa-solid fa-earth-asia"></i></div>
                    <div><strong>6</strong><span>Open opportunities</span></div>
                </article>
                <article class="international-stat stat-green">
                    <div class="international-stat-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div><strong>4</strong><span>Countries available</span></div>
                </article>
                <article class="international-stat stat-orange">
                    <div class="international-stat-icon"><i class="fa-regular fa-clipboard"></i></div>
                    <div><strong>2</strong><span>Applications submitted</span></div>
                </article>
                <article class="international-stat stat-purple">
                    <div class="international-stat-icon"><i class="fa-regular fa-calendar-days"></i></div>
                    <div><strong>3</strong><span>Upcoming deadlines</span></div>
                </article>
            </section>

            <section class="international-filters">
                <div class="opportunity-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search opportunities">
                </div>

                <select aria-label="Country filter">
                    <option>All Countries</option>
                    <option>Vietnam</option>
                    <option>Nepal</option>
                    <option>Indonesia</option>
                </select>

                <select aria-label="Program filter">
                    <option>All Programs</option>
                    <option>Education</option>
                    <option>Community Support</option>
                    <option>Youth Development</option>
                </select>

                <select class="sort-select" aria-label="Sort opportunities">
                    <option>Sort: Deadline</option>
                    <option>Sort: Country</option>
                    <option>Sort: Points</option>
                </select>
            </section>

            <div class="international-grid">
                <section class="opportunities-list">
                    <article class="opportunity-card opportunity-blue">
                        <div class="opportunity-icon blue-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                        <div class="opportunity-details">
                            <span class="open-badge">OPEN</span>
                            <h3>Global Classroom - Vietnam</h3>
                            <div class="opportunity-meta">
                                <span><i class="fa-solid fa-location-dot"></i> Dong Ha, Quang Tri, Vietnam</span>
                                <span><i class="fa-regular fa-clock"></i> 06 Weeks</span>
                                <span><i class="fa-regular fa-calendar"></i> 31st August 2026</span>
                                <span><i class="fa-solid fa-ranking-star"></i> 20 Points</span>
                            </div>
                            <p>Support school students through English learning and cultural exchange activities.</p>
                        </div>
                        <div class="opportunity-actions">
                            <a href="#" class="primary-action">Download Form</a>
                            <a href="#" class="secondary-action">Upload Form</a>
                        </div>
                    </article>

                    <article class="opportunity-card opportunity-green">
                        <div class="opportunity-icon green-icon"><i class="fa-solid fa-mountain-sun"></i></div>
                        <div class="opportunity-details">
                            <span class="open-badge">OPEN</span>
                            <h3>Community Support - Nepal</h3>
                            <div class="opportunity-meta">
                                <span><i class="fa-solid fa-location-dot"></i> Pokhara, Nepal</span>
                                <span><i class="fa-regular fa-clock"></i> 04 Weeks</span>
                                <span><i class="fa-regular fa-calendar"></i> 3rd September 2026</span>
                                <span><i class="fa-solid fa-ranking-star"></i> 15 Points</span>
                            </div>
                            <p>Assist with community outreach, education support, and local volunteer projects.</p>
                        </div>
                        <div class="opportunity-actions">
                            <a href="#" class="primary-action">Download Form</a>
                            <a href="#" class="secondary-action">Upload Form</a>
                        </div>
                    </article>

                    <article class="opportunity-card opportunity-orange">
                        <div class="opportunity-icon orange-icon"><i class="fa-solid fa-people-group"></i></div>
                        <div class="opportunity-details">
                            <span class="open-badge">OPEN</span>
                            <h3>Youth Development - Indonesia</h3>
                            <div class="opportunity-meta">
                                <span><i class="fa-solid fa-location-dot"></i> Bandung, Indonesia</span>
                                <span><i class="fa-regular fa-clock"></i> 05 Weeks</span>
                                <span><i class="fa-regular fa-calendar"></i> 31st September 2026</span>
                                <span><i class="fa-solid fa-ranking-star"></i> 15 Points</span>
                            </div>
                            <p>Take part in youth empowerment workshops and awareness campaigns.</p>
                        </div>
                        <div class="opportunity-actions">
                            <a href="#" class="primary-action">Download Form</a>
                            <a href="#" class="secondary-action">Upload Form</a>
                        </div>
                    </article>
                </section>

                <aside class="international-side-column">
                    <section class="guide-card">
                        <div class="side-card-heading">
                            <div class="side-heading-icon"><i class="fa-regular fa-hourglass-half"></i></div>
                            <h3>Application Guide</h3>
                        </div>
                        <div class="guide-step"><span>1</span><p>Download the form</p></div>
                        <div class="guide-step"><span>2</span><p>Complete your details</p></div>
                        <div class="guide-step"><span>3</span><p>Upload the application</p></div>
                        <div class="guide-step"><span>4</span><p>Wait for confirmation</p></div>
                    </section>

                    <section class="status-card">
                        <div class="side-card-heading">
                            <div class="side-heading-icon status-heading-icon"><i class="fa-regular fa-circle-check"></i></div>
                            <h3>My Application Status</h3>
                        </div>
                        <div class="application-status pending-status">
                            <strong>VIETNAM</strong>
                            <span>Status: <b>Pending</b></span>
                        </div>
                        <div class="application-status rejected-status">
                            <strong>NEPAL</strong>
                            <span>Status: <b>Rejected</b></span>
                        </div>
                    </section>
                </aside>
            </div>
        </section>
    </main>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
