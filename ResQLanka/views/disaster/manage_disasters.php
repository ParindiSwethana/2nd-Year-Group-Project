<?php

$pageTitle = "Manage Disasters | ResQ Lanka";
$pageCSS = "../../css/manage_disasters.css";
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

function getDisasterIcon($type)
{
    $icons = [
        'Flood' => ['icon' => 'fa-house-flood-water', 'class' => 'blue'],
        'Landslide' => ['icon' => 'fa-hill-rockslide', 'class' => 'brown'],
        'Road Blockage' => ['icon' => 'fa-tree', 'class' => 'green-alt'],
        'Strong Wind' => ['icon' => 'fa-wind', 'class' => 'purple'],
        'Thunderstorm' => ['icon' => 'fa-cloud-bolt', 'class' => 'yellow'],
        'Other' => ['icon' => 'fa-triangle-exclamation', 'class' => 'red']
    ];
    return $icons[$type] ?? $icons['Other'];
}

function getPriorityClass($priority) 
{
    switch(strtolower($priority ?? '')) {
        case 'high': 
            return 'background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca;'; // Light red background & red text
        case 'medium': 
            return 'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;'; // Light yellow/orange background & amber text
        case 'low': 
            return 'background-color: #d1fae5; color: #059669; border: 1px solid #a7f3d0;'; // Light green background & green text
        default: 
            return 'background-color: #f3f4f6; color: #4b5563;';
    }
}

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_completed') {
    $disasterId = $_POST['disaster_id'] ?? null;
    
    if ($disasterId) {
        
        $stmtSelect = $conn->prepare("SELECT created_at FROM disaster WHERE disaster_id = ?");
        $stmtSelect->execute([$disasterId]);
        $disasterData = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        
        if ($disasterData) {
           
            $start = new DateTime($disasterData['created_at']);
            $end = new DateTime(); 
            
            $interval = $start->diff($end);

            if ($interval->days > 0) {
                $durationStr = $interval->days . ' day' . ($interval->days > 1 ? 's' : '');
                if ($interval->h > 0) {
                    $durationStr .= ' ' . $interval->h . ' hr' . ($interval->h > 1 ? 's' : '');
                }
            } else {
                $hours = $interval->h;
                if ($hours > 0) {
                    $durationStr = $hours . ' hour' . ($hours > 1 ? 's' : '');
                } else {
                    $durationStr = max(1, $interval->i) . ' mins'; 
                }
            }
            
            $stmtUpdate = $conn->prepare("
                UPDATE disaster 
                SET status = 'Completed', 
                    resolved_at = CURRENT_TIMESTAMP, 
                    duration = ? 
                WHERE disaster_id = ?
            ");
            $stmtUpdate->execute([$durationStr, $disasterId]);

            $stmtUpdateAssignment = $conn->prepare("
                UPDATE volunteer_assignment
                SET status = 'Completed'
                WHERE disaster_id = ?
            ");

            $stmtUpdateAssignment->execute([
                $disasterId
            ]);
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_disaster') {
    $disasterId = $_POST['disaster_id'] ?? null;
    
    if ($disasterId) {
        $stmtDelete = $conn->prepare("DELETE FROM disaster WHERE disaster_id = ?");
        $stmtDelete->execute([$disasterId]);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$adminDistrictId = $_SESSION['district_id'] ?? 1;
$adminDistrict=$_SESSION['district_name'] ?? 'Colombo';



$ongoingStmt = $conn->prepare("
    SELECT a.*, d.district_name
    FROM disaster a
    JOIN district d ON a.district_id = d.district_id
    WHERE a.status = 'Ongoing' AND d.district_name = ? 
    ORDER BY a.created_at DESC
");
$ongoingStmt->execute([$adminDistrict]);
$ongoingDisasters = $ongoingStmt->fetchAll(PDO::FETCH_ASSOC);

$completedStmt = $conn->prepare("
    SELECT a.*, d.district_name
    FROM disaster a
    JOIN district d ON a.district_id = d.district_id
    WHERE a.status = 'Completed' AND d.district_name = ? 
    ORDER BY a.created_at DESC
");
$completedStmt->execute([$adminDistrict]);
$completedDisasters = $completedStmt->fetchAll(PDO::FETCH_ASSOC);


include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/district_admin_sidebar.php";
?>

    <main class="assignments-content">

        <div class="page-header-actions">
            <div class="page-heading">
                <h2>Manage Disasters</h2>
                <p>View and manage the disasters reported in the district</p>
            </div>
            <div class="create_button">
                <a 
                href="create_disaster.php"
                class="btn-primary"
            >
            <i class="fa-solid fa-plus"></i> Create Disaster Event
            </a>
            <a 
                href="assignment_details.php"
                class="btn-primary"
            >
            <i class="fa-solid fa-plus"></i> Create Disaster Assignment Request
            </a>
            </div>
            
           
        </div>

      
        <div class="summary-cards">
            <!-- Active Disasters -->
            <div class="summary-card active-card">
                <div class="card-icon blue-icon"><i class="fa-solid fa-bell"></i></div>
                <div class="card-data">
                    <h2><?= count($ongoingDisasters) ?></h2>
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
                    <h2><?= count($completedDisasters) ?></h2>
                    <div class="card-text">
                        <strong>Completed Disasters</strong>
                        <span>All time</span>
                    </div>
                </div>
            </div>

            <div class="summary-card assignment-card">
                <div class="card-icon orange-icon"><i class="fa-solid fa-clipboard-list"></i></i></div>
                <div class="card-data">
                    <h2><?= count($completedDisasters) ?></h2>
                    <div class="card-text">
                        <strong>Volunteer Assignments</strong>
                        <a href="../profile/view_profile.php"> View Assignments <i class="fa-solid fa-arrow-right"></i> </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tabs-container">
            <div class="tabs">
                <a href="#" class="tab" data-tab="ongoing-section">Ongoing</a>
                <a href="#" class="tab" data-tab="completed-section">Completed</a>
                <a href="#" class="tab active" data-tab="all">All</a>
            </div>
            <div class="tab-filter">
                <span><?= escape($adminDistrict) ?> District Only <i class="fa-solid fa-lock"></i></span>
            </div>
        </div>

        <!-- ONGOING DISASTERS TABLE -->
        <div class="table-section" id="ongoing-section">
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
                        <?php foreach ($ongoingDisasters as $disaster): 
                            $createdDate = date('d M Y', strtotime($disaster['created_at']));
                            $createdTime = date('h:i A', strtotime($disaster['created_at']));
                            $iconData = getDisasterIcon($disaster['disaster_type'] ?? 'Other');
                        ?>
                        <tr>
                            <td>
                                <div class="title-cell">
                                    <div class="title-icon <?= escape($iconData['class']) ?>">
                                        <i class="fa-solid <?= escape($iconData['icon']) ?>"></i>
                                    </div>
                                    <div class="title-text">
                                        <strong><?= escape($disaster['title'] ?? 'N/A') ?></strong>
                                        <span><?= escape($disaster['description'] ?? 'N/A') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="location-cell">
                                    <i class="fa-solid fa-location-dot"></i> <?= escape($disaster['location'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <div>
                                        <span><?= escape($createdDate ?? '') ?></span>
                                        <span class="time-sub"><?= escape($createdTime ?? '') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="priority-badge" style="<?= getPriorityClass($disaster['priority'] ?? 'Low') ?>">
                                    <?= escape($disaster['priority'] ?? 'Low') ?>
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
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="mark_completed">
                                        <input type="hidden" name="disaster_id" value="<?= $disaster['disaster_id'] ?>">
                                        <button type="submit" class="btn-outline-green">
                                            <i class="fa-regular fa-circle-check"></i> Mark as Completed
                                        </button>
                                    </form>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this disaster? This action cannot be undone.');">
                                        <input type="hidden" name="action" value="delete_disaster">
                                        <input type="hidden" name="disaster_id" value="<?= $disaster['disaster_id'] ?>">
                                        <button type="submit" style="color: #ef4444; border: 1px solid #ffffff; background-color: #ffffff; border-radius: 4px; padding: 6px 10px; cursor: pointer; margin-left: 5px;" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- COMPLETED DISASTERS TABLE -->
        <div class="table-section" id="completed-section">
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
                        <?php foreach ($completedDisasters as $disaster): 
                            $resolvedDate = date('d M Y', strtotime($disaster['resolved_at']));
                            $resolvedTime = date('h:i A', strtotime($disaster['resolved_at']));
                        ?>
                        <tr>
                            <td>
                                <div class="title-cell">
                                    <div class="title-icon green-icon">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <div class="title-text">
                                        <strong><?= escape($disaster['title'] ?? '') ?></strong>
                                        <span><?= escape($disaster['description'] ?? '') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="location-cell">
                                    <i class="fa-solid fa-location-dot"></i> <?= escape($disaster['location'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <div>
                                        
                                        <span><?= escape($resolvedDate ?? '') ?></span>
                                        <span class="time-sub"><?= escape($resolvedTime ?? '') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="duration-text"><?= escape($disaster['duration'] ?? '') ?></span>
                            </td>
                            <td>
                                <span class="status-badge-completed">
                                    <i class="fa-regular fa-circle-check"></i> Completed
                                </span>
                            </td>
                            <td>
                                <button class="btn-outline-blue">View Details</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this disaster? This action cannot be undone.');">
                                        <input type="hidden" name="action" value="delete_disaster">
                                        <input type="hidden" name="disaster_id" value="<?= $disaster['disaster_id'] ?>">
                                        <button type="submit" style="color: #ef4444; border: 1px solid #ffffff; background-color: #ffffff; border-radius: 4px; padding: 6px 10px; cursor: pointer; margin-left: 5px;" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
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

        <script src="../../js/manage_disaster.js"></script>
    </main>
</div>
