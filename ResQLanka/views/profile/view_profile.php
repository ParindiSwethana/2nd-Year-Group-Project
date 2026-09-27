<?php

$pageTitle = "My Profile | ResQ Lanka";
$pageCSS = "../../css/view_profile.css";
$activePage = "profile"; 

require_once __DIR__ . "/../../config/session.php";

$fullName = $_SESSION["name"] ?? "John Doe";
$email = $_SESSION["email"] ?? "john.doe@gmail.com";
$tier = $_SESSION["tier"] ?? "Silver";
$points = $_SESSION["points"] ?? 750;
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

?>

<div class="app-layout">
 <?php if (($_SESSION["role"] ?? "") === "registered_user") { include __DIR__ . "/../layouts/sidebar.php"; } elseif (($_SESSION["role"] ?? "") === "district_admin") { include __DIR__ . "/../layouts/district_admin_sidebar.php"; } elseif (($_SESSION["role"] ?? "") === "super_admin") { include __DIR__ . "/../layouts/super_admin_sidebar.php"; } ?>

    <main class="assignments-content">

        <section class="page-heading">
            <h2>My Profile</h2>
            <p>Manage your personal information and track your volunteer progress</p>
        </section>

        <div class="profile-layout">
            
            <div class="profile-main-panel">
                
                <div class="panel-header">
                    <div class="header-title">
                        <div class="icon-circle blue-light">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <div>
                            <h3>Personal Information</h3>
                            <p>Update your personal details here</p>
                        </div>
                    </div>
                    <button class="btn-outline-blue">
                        <i class="fa-solid fa-pen"></i> Edit Profile
                    </button>
                </div>

                <form class="profile-form" action="#" method="POST">
                    
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" value="<?= escape($fullName) ?>" readonly>
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" value="<?= escape($email) ?>" readonly>
                        </div>
                        
                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" value="+94 77 123 4567" readonly>
                        </div>
                        <div class="input-group">
                            <label>Alternate Number</label>
                            <input type="text" value="+94 77 123 9999" readonly>
                        </div>
                    </div>

                    <div class="input-group full-width mt-15">
                        <label>Home Address</label>
                        <input type="text" placeholder="Enter your new home address">
                    </div>

                    <div class="form-grid mt-15">
                        <div class="input-group">
                            <label>District</label>
                            <select>
                                <option disabled selected>Select District</option>
                                <option>Colombo</option>
                                <option>Gampaha</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Province</label>
                            <select>
                                <option disabled selected>Select Province</option>
                                <option>Western</option>
                                <option>Central</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-solid-blue">
                            <i class="fa-solid fa-check"></i> Save Changes
                        </button>
                    </div>
                </form>

                <div class="account-settings-box">
                    <div class="header-title">
                        <div class="icon-circle green-light">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3>Account Settings</h3>
                            <p>Manage your account security and performances</p>
                        </div>
                    </div>
                    <button class="btn-outline-blue">
                        <i class="fa-solid fa-lock"></i> Change Password
                    </button>
                </div>

            </div>

        <?php if (($_SESSION["role"] ?? "") === "registered_user"): ?>
            <aside class="profile-sidebar">
                
                <div class="profile-card ranking-card">
                    <div class="card-header">
                        <i class="fa-solid fa-trophy text-orange"></i>
                        <div>
                            <h3>Volunteer Ranking</h3>
                            <p>Complete more assignments and climb higher</p>
                        </div>
                    </div>

                    <div class="current-tier-box">
                        <div class="tier-badge badge-silver">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div class="tier-details">
                            <span>Current Tier</span>
                            <h4><?= escape($tier) ?> Volunteer</h4>
                            
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <strong><?= escape($points) ?> / 1,500 XP</strong>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 50%;"></div>
                                </div>
                                <span class="progress-hint"><i class="fa-solid fa-star"></i> 750 XP to reach Gold Tier</span>
                            </div>
                        </div>
                    </div>

                    <div class="tier-progression">
                        <div class="progression-header">
                            <h4>Tier Progression</h4>
                            <a href="#">View All Tiers</a>
                        </div>
                        <div class="tier-steps">
                            <div class="step">
                                <div class="mini-badge bronze"><i class="fa-solid fa-star"></i></div>
                                <strong>Bronze</strong>
                                <span>0 - 499 XP</span>
                            </div>
                            <i class="fa-solid fa-arrow-right-long step-arrow"></i>
                            <div class="step active">
                                <div class="mini-badge silver"><i class="fa-solid fa-star"></i></div>
                                <strong>Silver</strong>
                                <span>500 - 1,499 XP</span>
                            </div>
                            <i class="fa-solid fa-arrow-right-long step-arrow"></i>
                            <div class="step">
                                <div class="mini-badge gold"><i class="fa-solid fa-star"></i></div>
                                <strong>Gold</strong>
                                <span>1,500 - 2,999 XP</span>
                            </div>
                            <i class="fa-solid fa-arrow-right-long step-arrow"></i>
                            <div class="step">
                                <div class="mini-badge platinum"><i class="fa-solid fa-star"></i></div>
                                <strong>Platinum</strong>
                                <span>3,000+ XP</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-card impact-card">
                    <div class="card-header">
                        <i class="fa-solid fa-chart-simple text-blue"></i>
                        <div>
                            <h3>Your Impact</h3>
                            <p>Your contribution in numbers</p>
                        </div>
                    </div>
                    
                    <div class="impact-stats-grid">
                        <div class="impact-item">
                            <div class="impact-icon blue"><i class="fa-solid fa-clipboard-check"></i></div>
                            <strong>12</strong>
                            <span>Assignments completed</span>
                        </div>
                        <div class="impact-item">
                            <div class="impact-icon purple"><i class="fa-solid fa-users"></i></div>
                            <strong>158</strong>
                            <span>Hours Volunteered</span>
                        </div>
                        <div class="impact-item">
                            <div class="impact-icon green"><i class="fa-regular fa-circle-check"></i></div>
                            <strong>98%</strong>
                            <span>Success Rate</span>
                        </div>
                        <div class="impact-item">
                            <div class="impact-icon red"><i class="fa-solid fa-heart"></i></div>
                            <strong>240</strong>
                            <span>People Helped</span>
                        </div>
                    </div>
                </div>

            </aside>
            <?php endif; ?>
            
        </div>

    </main>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
