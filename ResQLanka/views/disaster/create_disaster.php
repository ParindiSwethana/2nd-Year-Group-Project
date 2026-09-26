<?php

$pageTitle = "Create Assignment | ResQ Lanka";
$pageCSS = "../../css/create_disaster.css";
$activePage = "disasters"; 

require_once __DIR__ . "/../../config/session.php";
require_once __DIR__ . "/../../config/database.php";

$fullName = $_SESSION["name"] ?? "John Doe";
$tier = $_SESSION["tier"] ?? "Silver";
$firstName = explode(" ", trim($fullName))[0];

function escape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

$db = new Database();
$conn = $db->connect();

$adminDistrictId = $_SESSION['district_id'] ?? 1;
$adminDistrict = $_SESSION['district'] ?? 'Colombo';
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";

?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div style="background-color: #ffcccc; color: #cc0000; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <strong>Error:</strong> <?= $_SESSION['error_message'] ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="app-layout">

<?php
include __DIR__ . "/../layouts/district_admin_sidebar.php";
?>

    <main class="assignments-content">

        <div class="page-top-section">
            <div class="breadcrumb">
                Manage Disasters &gt; <span>Create Assignment Request</span>
            </div>
            
            <div class="page-header-flex">
                <div class="page-heading">
                    <h2>Create Disaster Assignment Request</h2>
                    <p>Enter the details needed to publish a volunteer assignment for this disaster</p>
                </div>
                <div class="admin-badge">
                    <i class="fa-solid fa-location-dot"></i> 
                    <span><?= escape($adminDistrict) ?> Admin</span>
                </div>
            </div>
        </div>

        <div class="create-layout">
            
            <div class="form-panel">
                <form action="../../controllers/DisasterController.php" method="POST">
                    
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label><span class="label-num">1.</span> Assignment Title</label>
                            <input type="text" name="title" placeholder="Enter a clear title for this assignment" required>
                        </div>

                        <div class="form-group">
                            <label><span class="label-num">2.</span>Disaster Type</label>
                            <select name="disaster_type" required>
                                <option value="" disabled selected>Select type</option>
                                <option value="Flood">Flood</option>
                                <option value="Landslide">Landslide</option>
                                <option value="Road Blockage">Road Blockage</option>
                                <option value="Strong Wind">Strong Wind</option>
                                <option value="Thunderstorm">Thunderstorm</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><span class="label-num">3.</span> Location / Town</label>
                            <input type="text" name="location" placeholder="Location/Town">
                        </div>

                        <div class="form-group">
                            <label><span class="label-num">4.</span>District</label>
                            <input 
                                type="text" 
                                value="<?= escape($adminDistrict) ?>" 
                                readonly
                            >

                            <input 
                                type="hidden" 
                                name="district_id" 
                                value="<?= escape($adminDistrictId) ?>"
                            >
                        </div>


                        <div class="form-group">
                            <label><span class="label-num">5.</span> Priority</label>
                            <select name="priority">
                                <option value="" disabled selected>Select priority level</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>   
                    </div>
                    
                    <div class="form-group mt-15 full-width">
                        <label><span class="label-num">6.</span> Short Description / Assignment Summary</label>
                        <textarea name="description" placeholder="Provide a brief summary of the assignment, tasks and expected outcomes..." rows="3"></textarea>
                        <div class="char-count">0/500</div>
                    </div>

                
                    <div class="form-actions">
                        <button type="button" class="btn-cancel">Cancel</button>
                        <div class="right-actions">
                            <button type="button" class="btn-draft">
                                <i class="fa-solid fa-bookmark"></i> Save Draft
                            </button>
                            <button type="submit" name="submit_disaster" class="btn-submit">
                                <i class="fa-solid fa-paper-plane"></i> Create Assignment Request
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <aside class="create-sidebar">
                
                <div class="side-card guidelines-card">
                    <div class="card-header">
                        <i class="fa-solid fa-circle-info text-blue"></i>
                        <h3>Quick Guidelines</h3>
                    </div>
                    <p class="guideline-intro">Follow these tips to create an effective assignment request</p>
                    
                    <div class="guide-list">
                        <div class="guide-item">
                            <div class="guide-icon"><i class="fa-regular fa-clipboard"></i></div>
                            <div class="guide-text">
                                <strong>Keep the title clear</strong>
                                <span>Use a short specific title so volunteers understand the task</span>
                            </div>
                        </div>
                        
                        <div class="guide-item">
                            <div class="guide-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="guide-text">
                                <strong>Choose the correct location</strong>
                                <span>Select the exact town/area where support is needed</span>
                            </div>
                        </div>

                        <div class="guide-item">
                            <div class="guide-icon"><i class="fa-solid fa-user-group"></i></div>
                            <div class="guide-text">
                                <strong>Specify volunteers needed</strong>
                                <span>Enter an accurate number to help us mobilize the right people</span>
                            </div>
                        </div>

                        <div class="guide-item">
                            <div class="guide-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div class="guide-text">
                                <strong>Set the right priority</strong>
                                <span>Choose the priority level based on the urgency of the situation</span>
                            </div>
                        </div>

                        <div class="guide-item">
                            <div class="guide-icon"><i class="fa-solid fa-list-ul"></i></div>
                            <div class="guide-text">
                                <strong>Share essential details only</strong>
                                <span>Avoid unnecessary information. Keep it short and actionable</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="side-card help-card">
                    <div class="card-header">
                        <i class="fa-regular fa-circle-question text-blue"></i>
                        <h3>Need Help?</h3>
                    </div>
                    <p>Contact the district coordination team for assistance.</p>
                </div>

            </aside>
            
        </div>
    </main>
</div>
<?php include __DIR__ . "/../layouts/footer.php";?>
