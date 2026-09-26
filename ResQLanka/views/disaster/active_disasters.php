<?php

$pageTitle = "Active Disasters | ResQ Lanka";

$pageCSS = "../../css/active_disasters.css";
$activePage = "disasters";

require_once __DIR__ . "/../../config/session.php";
require_once __DIR__ . "/../../config/database.php";

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

function getDisasterCover($type)
{
    $covers = [
        'Flood' => ['image' => '../../images/flood.jpeg'],
        'Landslide' => ['image' => '../../images/landslide.jpeg'],
        'Road Blockage' => ['image' => '../../images/road_block.jpg'],
        'Strong Wind' => ['image' => '../../images/strong_wind.jpg'],
        'Thunderstorm' => ['image' => '../../images/thunderstorm.jpeg'],
        'Other' => ['image' => '../../images/other.jpeg']
    ];
    return $covers[$type] ?? $covers['Other'];
}

function getSeverityClass($priority)
{
    $class = [
        'Critical' => ['severity_class' => 'critical'],
        'Moderate' => ['severity_class' => 'moderate'],
        'Minor'    => ['severity_class' => 'minor'],
        'Other'    => ['severity_class' => 'default'] 
    ];
    
    
    return $class[ucfirst(strtolower($priority))] ?? $class['Other'];
}

function timeAgo($timestamp) {
    $timeAgo = strtotime($timestamp);
    $currentTime = time();
    $timeDifference = $currentTime - $timeAgo;
    
    $seconds = $timeDifference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);
    
    if ($seconds <= 5) {
        return "Just now";
    } elseif ($minutes <= 60) {
        return $minutes == 1 ? "1m ago" : "$minutes mins ago";
    } elseif ($hours <= 24) {
        return $hours == 1 ? "1h ago" : "$hours hrs ago";
    } elseif ($days <= 7) {
        return $days == 1 ? "Yesterday" : "$days days ago";
    } elseif ($weeks <= 4.3) {
        return $weeks == 1 ? "1 week ago" : "$weeks weeks ago";
    } elseif ($months <= 12) {
        return $months == 1 ? "1 month ago" : "$months months ago";
    } else {
        return $years == 1 ? "1 year ago" : "$years years ago";
    }
}

$db = new Database();
$conn = $db->connect();

$selectedDistrict = $_GET['district'] ?? 'All';
$selectedSeverity = $_GET['severity'] ?? 'All';

if ($selectedDistrict !== 'All' && $selectedSeverity !== 'All' ) {
    $disasterStmt = $conn->prepare("
        SELECT a.*, d.title AS disaster_title, d.disaster_type, dist.district_name
        FROM volunteer_assignment a
        JOIN disaster d ON a.disaster_id = d.disaster_id
        JOIN district dist ON d.district_id = dist.district_id
        WHERE dist.district_name = ? AND a.severity = ? AND a.status = 'Ongoing'
        ORDER BY a.created_at DESC
    ");
    $disasterStmt->execute([$selectedDistrict,$selectedSeverity]);
} 
else if ($selectedDistrict !== 'All' && $selectedSeverity === 'All'){
   
    $disasterStmt = $conn->prepare("
        SELECT a.*, d.title AS disaster_title, d.disaster_type, dist.district_name
        FROM volunteer_assignment a
        JOIN disaster d ON a.disaster_id = d.disaster_id
        JOIN district dist ON d.district_id = dist.district_id
        WHERE dist.district_name = ? AND a.status = 'Ongoing'
        ORDER BY a.created_at DESC
    ");
    $disasterStmt->execute([$selectedDistrict]);
}
else if ($selectedDistrict === 'All' && $selectedSeverity !== 'All'){
   
    $disasterStmt = $conn->prepare("
        SELECT a.*, d.title AS disaster_title, d.disaster_type, dist.district_name
        FROM volunteer_assignment a
        JOIN disaster d ON a.disaster_id = d.disaster_id
        JOIN district dist ON d.district_id = dist.district_id
        WHERE a.severity = ? AND a.status = 'Ongoing'
        ORDER BY a.created_at DESC
    ");
    $disasterStmt->execute([$selectedSeverity]);
}
else {
   
    $disasterStmt = $conn->prepare("
        SELECT a.*, d.title AS disaster_title, d.disaster_type, dist.district_name
        FROM volunteer_assignment a
        JOIN disaster d ON a.disaster_id = d.disaster_id
        JOIN district dist ON d.district_id = dist.district_id
        WHERE a.status = 'Ongoing'
        ORDER BY a.created_at DESC
    ");
    $disasterStmt->execute();
}

$disasters = $disasterStmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";


?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/sidebar.php";
?>

    <main class="assignments-content">

        <section class="page-heading">
            <h2>Active Disasters</h2>
            <p>Stay updated on emergencies happening across Sri Lanka</p>
        </section>

        <section class="assignment-stats">
            <?php
                $criticalCount = 0;
                foreach ($disasters as $disaster) {
                    if (($disaster['severity'] ?? '') === 'Critical') {
                        $criticalCount++;
                    }
                }
            ?>
            <article class="assignment-stat stat-box-critical">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-information stat-info-critical">
                    <strong><?=escape($criticalCount)?></strong>
                    <span>Critical</span>
                    <small>High priority</small>
                </div>
            </article>

             <?php
                $moderateCount = 0;
                foreach ($disasters as $disaster) {
                    if (($disaster['severity'] ?? '') === 'Moderate') {
                        $moderateCount++;
                    }
                }
            ?>
            <article class="assignment-stat stat-box-moderate">
                <div class="stat-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div class="stat-information stat-info-moderate">
                    <strong><?=escape($moderateCount)?></strong>
                    <span>Moderate</span>
                    <small>Monitor closely</small>
                </div>
            </article>

             <?php
                $minorCount = 0;
                foreach ($disasters as $disaster) {
                    if (($disaster['severity'] ?? '') === 'Minor') {
                        $minorCount++;
                    }
                }
            ?>
            <article class="assignment-stat stat-box-monitor">
                <div class="stat-icon"><i class="fa-regular fa-circle-check"></i></div>
                <div class="stat-information stat-info-monitor">
                    <strong><?=escape($minorCount)?></strong>
                    <span>Minor</span>
                    <small>Under control</small>
                </div>
            </article>

            <article class="assignment-stat stat-box-volunteers">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-information stat-info-volunteers">
                    <strong>0</strong>
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
                <div class="tab-select">
                    <form method="GET" action="" style="margin: 0;">
                        <select name="severity" onchange="this.form.submit()" class="custom-select">
                            <option value="All" <?= $selectedSeverity === 'All' ? 'selected' : '' ?>>All Severity</option>
                            <option value="Critical" <?= $selectedSeverity === 'Critical' ? 'selected' : '' ?>>Critical</option>
                             <option value="Moderate" <?= $selectedSeverity === 'Moderate' ? 'selected' : '' ?>>Moderate</option>
                              <option value="Minor" <?= $selectedSeverity === 'Minor' ? 'selected' : '' ?>>Minor</option>
                        </select>

                    </form>
                    
                </div>
                <div class="tab-select">
                    <form method="GET" action="" style="margin: 0;">
                    <select name="district" onchange="this.form.submit()" class="custom-select">
                        <option value="All" <?= $selectedDistrict === 'All' ? 'selected' : '' ?>>All Districts</option>
                        <option value="Ampara" <?= $selectedDistrict === 'Ampara' ? 'selected' : '' ?>>Ampara</option>
                        <option value="Anuradhapura" <?= $selectedDistrict === 'Anuradhapura' ? 'selected' : '' ?>>Anuradhapura</option>
                        <option value="Badulla" <?= $selectedDistrict === 'Badulla' ? 'selected' : '' ?>>Badulla</option>
                        <option value="Batticaloa" <?= $selectedDistrict === 'Batticaloa' ? 'selected' : '' ?>>Batticaloa</option>
                        <option value="Colombo" <?= $selectedDistrict === 'Colombo' ? 'selected' : '' ?>>Colombo</option>
                        <option value="Galle" <?= $selectedDistrict === 'Galle' ? 'selected' : '' ?>>Galle</option>
                        <option value="Gampaha" <?= $selectedDistrict === 'Gampaha' ? 'selected' : '' ?>>Gampaha</option>
                        <option value="Hambantota" <?= $selectedDistrict === 'Hambantota' ? 'selected' : '' ?>>Hambantota</option>
                        <option value="Jaffna" <?= $selectedDistrict === 'Jaffna' ? 'selected' : '' ?>>Jaffna</option>
                        <option value="Kalutara" <?= $selectedDistrict === 'Kalutara' ? 'selected' : '' ?>>Kalutara</option>
                        <option value="Kandy" <?= $selectedDistrict === 'Kandy' ? 'selected' : '' ?>>Kandy</option>
                        <option value="Kegalle" <?= $selectedDistrict === 'Kegalle' ? 'selected' : '' ?>>Kegalle</option>
                        <option value="Kilinochchi" <?= $selectedDistrict === 'Kilinochchi' ? 'selected' : '' ?>>Kilinochchi</option>
                        <option value="Kurunegala" <?= $selectedDistrict === 'Kurunegala' ? 'selected' : '' ?>>Kurunegala</option>
                        <option value="Mannar" <?= $selectedDistrict === 'Mannar' ? 'selected' : '' ?>>Mannar</option>
                        <option value="Matale" <?= $selectedDistrict === 'Matale' ? 'selected' : '' ?>>Matale</option>
                        <option value="Matara" <?= $selectedDistrict === 'Matara' ? 'selected' : '' ?>>Matara</option>
                        <option value="Monaragala" <?= $selectedDistrict === 'Monaragala' ? 'selected' : '' ?>>Monaragala</option>
                        <option value="Mullaitivu" <?= $selectedDistrict === 'Mullaitivu' ? 'selected' : '' ?>>Mullaitivu</option>
                        <option value="Nuwara Eliya" <?= $selectedDistrict === 'Nuwara Eliya' ? 'selected' : '' ?>>Nuwara Eliya</option>
                        <option value="Polonnaruwa" <?= $selectedDistrict === 'Polonnaruwa' ? 'selected' : '' ?>>Polonnaruwa</option>
                        <option value="Puttalam" <?= $selectedDistrict === 'Puttalam' ? 'selected' : '' ?>>Puttalam</option>
                        <option value="Ratnapura" <?= $selectedDistrict === 'Ratnapura' ? 'selected' : '' ?>>Ratnapura</option>
                        <option value="Trincomalee" <?= $selectedDistrict === 'Trincomalee' ? 'selected' : '' ?>>Trincomalee</option>
                        <option value="Vavuniya" <?= $selectedDistrict === 'Vavuniya' ? 'selected' : '' ?>>Vavuniya</option>
                    </select>
                </form>
                </div>
            </div>
            <div class="filter-controls-right">
                <span class="sort-text">Sort: <strong>Newest</strong></span>
            </div>
        </section>

        <div class="assignment-layout">

            <section class="assignment-list">
                <?php foreach ($disasters as $disaster): 
                    $severity = getSeverityClass($disaster['severity'] ?? 'Other');
                    $coverData = getDisasterCover($disaster['disaster_type'] ?? 'Other');
                    $image = $coverData['image'];?>
                   
                    <article class="assignment-card disaster-card border-<?= escape($severity['severity_class']) ?>">
                
                        <img src="<?= escape($image) ?>" alt="Disaster Area" class="disaster-img">

                        <div class="disaster-info-main">
                            <div class="disaster-top-row">
                                <span class="status-badge badge-<?= escape($severity['severity_class']) ?>">
                                    <?= escape($disaster['severity']) ?>
                                </span>
                                <a 
                                    href="disaster_details.php?id=<?= escape($disaster['assignment_id'])?>"
                                    class="view-details btn-<?= escape($severity['severity_class']) ?>"
                                >
                                    View Details <i class="fa-solid fa-caret-down"></i>
                                </a>
                            </div>
                            
                            <h3 class="disaster-title"><?= escape($disaster['disaster_title']) ?></h3>
                            
                            <div class="disaster-location">
                                <i class="fa-solid fa-location-dot"></i> <?= escape($disaster['location']) ?>
                            </div>
                            
                            <p class="disaster-desc"><?= escape($disaster['description']) ?></p>
                            
                            <div class="disaster-meta-footer">
                                <span><i class="fa-regular fa-clock"></i> <?= escape(timeAgo($disaster['created_at'])) ?></span>
                                <span><i class="fa-solid fa-users"></i> <?= escape($disaster['volunteers_needed']) ?> volunteers requested</span>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </section>

            <aside class="right-panel">
                
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
