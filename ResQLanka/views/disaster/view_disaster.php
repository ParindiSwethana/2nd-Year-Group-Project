<?php
$pageTitle = "Manage Disasters | ResQ Lanka";
$pageCSS = "../../css/view_disaster.css";
$activePage = "disasters";

require_once __DIR__ . "/../../config/session.php";
require_once __DIR__ . "/../../config/database.php";

$fullName = $_SESSION["name"] ?? "John";
$tier = $_SESSION["tier"] ?? "Bronze";
$points = $_SESSION["points"] ?? 0;
$firstName = explode(" ", trim($fullName))[0];

function escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
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

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_disasters.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_disaster') {
    $disasterId = $_POST['disaster_id'] ?? null;
    
    if ($disasterId) {
        try {
            $stmtUpdate = $conn->prepare("
                UPDATE disaster 
                SET title = ?, location = ?, priority = ?, status = ?, duration = ?, description = ?, created_at = ?
                WHERE disaster_id = ?
            ");
            
            $formattedDate = date('Y-m-d H:i:s', strtotime($_POST['date']));
            
            $stmtUpdate->execute([
                trim($_POST['title'] ?? ''),
                trim($_POST['location'] ?? ''),
                trim($_POST['priority'] ?? ''),
                trim($_POST['status'] ?? ''),
                trim($_POST['duration'] ?? ''),
                trim($_POST['description'] ?? ''),
                $formattedDate,
                $disasterId
            ]);
            
            $successMessage = "Disaster details updated successfully!";
            
        } catch (PDOException $e) {
            $errorMessage = "Database Error: " . $e->getMessage();
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_disaster') {
    $disasterId = $_POST['disaster_id'] ?? null;
    if ($disasterId) {
        $stmtDelete = $conn->prepare("DELETE FROM disaster WHERE disaster_id = ?");
        $stmtDelete->execute([$disasterId]);
        header("Location: manage_disasters.php"); 
        exit();
    }
}


try {
    $stmt = $conn->prepare("SELECT * FROM disaster WHERE disaster_id = ?");
    $stmt->execute([$id]);
    $disaster = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$disaster) {
        header("Location: manage_disasters.php?error=notfound");
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>

<div class="app-layout">
<?php include __DIR__ . "/../layouts/district_admin_sidebar.php"; ?>

    <main class="details-content">
        <section class="page-title-card">
            <div class="breadcrumb">
                <span>Active Disasters</span>
                <i class="fa-solid fa-chevron-right"></i>
                <strong>Disaster Details</strong>
            </div>
            <h2>Disaster Details</h2>
            <p>View the disaster details.</p>
        </section>

       
        <?php if (!empty($successMessage)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <strong>Success!</strong> <?= escape($successMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong>Update Failed!</strong> <?= escape($errorMessage) ?>
            </div>
        <?php endif; ?>

        <div class="details-grid">
            <section class="disaster-card">
                
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_disaster">
                    <input type="hidden" name="disaster_id" value="<?= escape($disaster['disaster_id']) ?>">

                    <div class="disaster-header">
                        <div class="disaster-image">
                            <?php 
                            $coverData = getDisasterCover($disaster['disaster_type'] ?? 'Other');
                            $image = $coverData['image'];
                            ?>
                            <img src="<?= escape($image) ?>" alt="image">
                        </div>

                       
                        <div class="disaster-introduction">
                            <span class="<?= escape($disaster['priority']) ?>-badge">
                                <?= escape($disaster['priority']) ?>
                            </span>

                            <h1 id="view-title"><?= escape($disaster['title']) ?></h1>
                            <input type="text" name="title" id="edit-title" class="edit-input hidden" value="<?= escape($disaster['title']) ?>">

                            <div class="disaster-location">
                                <i class="fa-solid fa-location-dot"></i>
                                <span><?= escape($disaster['location']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="disaster-stats">
                        <div class="detail-stat">
                            <div class="stat-label">
                                <i class="fa-solid fa-location-dot"></i> Location
                            </div>
                            <strong id="view-location"><?= escape($disaster['location']) ?></strong>
                            <input type="text" name="location" id="edit-location" class="edit-input hidden" value="<?= escape($disaster['location']) ?>">
                        </div>

                        <div class="detail-stat">
                            <div class="stat-label">
                                <i class="fa-solid fa-triangle-exclamation"></i> Priority
                            </div>
                            <strong id="view-priority" class="priority-<?= escape($disaster['priority']) ?>">
                                <?= escape($disaster['priority']) ?>
                            </strong>
                            <select name="priority" id="edit-priority" class="edit-input hidden">
                                <option value="High" <?= $disaster['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                                <option value="Medium" <?= $disaster['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="Low" <?= $disaster['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                            </select>
                        </div>

                        <div class="detail-stat">
                            <?php $createdDate = date('d M Y', strtotime($disaster['created_at'])); ?>
                            <div class="stat-label">
                                <i class="fa-regular fa-calendar"></i> Date
                            </div>
                            <strong id="view-date"><?= escape($createdDate) ?></strong>
                            <input type="date" name="date" id="edit-date" class="edit-input hidden" value="<?= date('Y-m-d', strtotime($disaster['created_at'])) ?>">
                        </div>

                       
                        <div class="detail-stat">
                            <div class="stat-label">
                                <i class="fa-regular fa-clock"></i> Status
                            </div>
                            <strong id="view-status"><?= escape($disaster['status']) ?></strong>
                            <select name="status" id="edit-status" class="edit-input hidden">
                                <option value="Pending" <?= $disaster['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Ongoing" <?= $disaster['status'] == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                                <option value="Completed" <?= $disaster['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>

                        
                        <div class="detail-stat">
                            <div class="stat-label">
                                <i class="fa-regular fa-clock"></i> Duration
                            </div>
                            <strong id="view-duration"><?= escape($disaster['duration'] ?? '-') ?></strong>
                            <input type="text" name="duration" id="edit-duration" class="edit-input hidden" value="<?= escape($disaster['duration'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="information-section">
                        <h3>
                            <i class="fa-regular fa-clipboard"></i> Disaster Summary
                        </h3>
                        <p id="view-summary"><?= escape($disaster['description']) ?></p>
                        <textarea name="description" id="edit-summary" class="edit-input hidden" style="width: 100%; min-height: 100px;"><?= escape($disaster['description']) ?></textarea>
                    </div>

                   
                    <div class="update-buttons" style="display: flex; gap: 10px; margin-top: 15px;">
                       
                        <button type="button" id="edit-btn" class="btn btn-edit">
                            <i id="edit-btn-icon" class="fa-solid fa-pen"></i>
                            <span id="edit-btn-text">Edit Disaster</span>
                        </button>
                        
                      
                        <button type="submit" id="save-btn" class="btn btn-edit hidden" style="background-color: #28a745; color: white;">
                            <i class="fa-solid fa-check"></i> Save Changes
                        </button>
                </form>
               
                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this disaster? This action cannot be undone.');">
                    <input type="hidden" name="action" value="delete_disaster">
                    <input type="hidden" name="disaster_id" value="<?= escape($disaster['disaster_id']) ?>">
                    <button type="submit" class="btn btn-delete" title="Delete">
                        <i class="fa-solid fa-trash-can"></i> Delete Disaster
                    </button>
                </form>
                
                </div> 
            </section>
        </div>

        <script src="../../js/view_disaster.js"></script>
    
       
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php"; ?>