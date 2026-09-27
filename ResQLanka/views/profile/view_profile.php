<?php
$pageTitle = "My Profile | ResQ Lanka";
$pageCSS = "../../css/view_profile.css";
$activePage = "profile"; 

require_once __DIR__ . "/../../config/session.php";

require_once __DIR__ . "/../../config/database.php"; 


class DistrictAdmin
{
    private $conn;
    private $table = "USERS"; 
    private $role = "registered_user";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getById($id)
    {
        $query = "
            SELECT user_id, first_name, last_name, date_of_birth, gender, email, phone,
                   address, district, nic, emergency_contact_name, emergency_contact_phone, status, role, tier, points
            FROM {$this->table}
            WHERE user_id = :id
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
    $query = "
        UPDATE {$this->table}
        SET first_name = :first_name,
            last_name = :last_name,
            email = :email,
            phone = :phone,
            address = :address,
            district = :district,
            emergency_contact_name = :emergency_contact_name,
            emergency_contact_phone = :emergency_contact_phone
        WHERE user_id = :id
    ";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([
        "first_name" => $data["first_name"],
        "last_name" => $data["last_name"],
        "email" => $data["email"], // Add this line
        "phone" => $data["phone"],
        "address" => $data["address"],
        "district" => $data["district"],
        "emergency_contact_name" => $data["emergency_contact_name"],
        "emergency_contact_phone" => $data["emergency_contact_phone"],
        "id" => $id
    ]);
    }
}


$userId = $_SESSION["user_id"] ?? 1; 
$adminObj = new DistrictAdmin();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    
    $updateData = [
        "first_name" => trim($_POST['first_name'] ?? ''),
        "last_name" => trim($_POST['last_name'] ?? ''),
        "email" => trim($_POST['email'] ?? ''),
        "phone" => trim($_POST['phone'] ?? ''),
        "address" => trim($_POST['address'] ?? ''),
        "district" => trim($_POST['district'] ?? ''),
        "emergency_contact_name" => trim($_POST['emergency_contact_name'] ?? ''),
        "emergency_contact_phone" => trim($_POST['emergency_contact_phone'] ?? '')
    ];

    $success = $adminObj->update($userId, $updateData);

    if ($success) {
        $successMessage = "Profile updated successfully!";
        $_SESSION["name"] = $updateData['first_name'] . " " . $updateData['last_name'];
    } else {
        $errorMessage = "Failed to update profile.";
    }
}

$user = $adminObj->getById($userId);

$fullName = $_SESSION["name"] ?? (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$tier = $user['tier'] ?? "Silver";
$points = $user['points'] ?? "750";

function escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
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

        <?php if (isset($successMessage)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fa-solid fa-circle-check"></i> <?= escape($successMessage) ?>
            </div>
        <?php endif; ?>

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
                    
                    <button type="button" class="btn-outline-blue" id="editProfileBtn">
                        <i class="fa-solid fa-pen"></i> Edit Profile
                    </button>
                </div>

                <form class="profile-form" action="" method="POST" id="profileForm">
                    
                    <div class="form-grid">
                        <div class="input-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="editable-field" value="<?= escape($user['first_name'] ?? '') ?>" readonly required>
                        </div>
                        <div class="input-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="editable-field" value="<?= escape($user['last_name'] ?? '') ?>" readonly required>
                        </div>

                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="editable-field" value="<?= escape($user['email'] ?? '') ?>" readonly required>
                        </div>
                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" name="phone" class="editable-field" value="<?= escape($user['phone'] ?? '') ?>" readonly required>
                        </div>
                        
                        <div class="input-group">
                            <label>Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="editable-field" value="<?= escape($user['emergency_contact_name'] ?? '') ?>" readonly>
                        </div>
                        <div class="input-group">
                            <label>Emergency Contact Number</label>
                            <input type="text" name="emergency_contact_phone" class="editable-field" value="<?= escape($user['emergency_contact_phone'] ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="input-group full-width mt-15">
                        <label>Home Address</label>
                        <input type="text" name="address" class="editable-field" value="<?= escape($user['address'] ?? '') ?>" readonly required>
                    </div>

                    <div class="form-grid mt-15">
                        <div class="input-group">
                            <label>District</label>
                            <?php if (($_SESSION["role"] ?? "") === "registered_user"): ?>
                            <select name="district" class="editable-field" disabled required>
                                <option disabled <?= empty($user['district']) ? 'selected' : '' ?>>Select District</option>
                                <option value="Colombo" <?= ($user['district'] ?? '') === 'Colombo' ? 'selected' : '' ?>>Colombo</option>
                                <option value="Gampaha" <?= ($user['district'] ?? '') === 'Gampaha' ? 'selected' : '' ?>>Gampaha</option>
                                <option value="Kalutara" <?= ($user['district'] ?? '') === 'Kalutara' ? 'selected' : '' ?>>Kalutara</option>

                                <option value="Kandy" <?= ($user['district'] ?? '') === 'Kandy' ? 'selected' : '' ?>>Kandy</option>
                                <option value="Matale" <?= ($user['district'] ?? '') === 'Matale' ? 'selected' : '' ?>>Matale</option>
                                <option value="Nuwara Eliya" <?= ($user['district'] ?? '') === 'Nuwara Eliya' ? 'selected' : '' ?>>Nuwara Eliya</option>

                                <option value="Galle" <?= ($user['district'] ?? '') === 'Galle' ? 'selected' : '' ?>>Galle</option>
                                <option value="Matara" <?= ($user['district'] ?? '') === 'Matara' ? 'selected' : '' ?>>Matara</option>
                                <option value="Hambantota" <?= ($user['district'] ?? '') === 'Hambantota' ? 'selected' : '' ?>>Hambantota</option>

                                <option value="Jaffna" <?= ($user['district'] ?? '') === 'Jaffna' ? 'selected' : '' ?>>Jaffna</option>
                                <option value="Kilinochchi" <?= ($user['district'] ?? '') === 'Kilinochchi' ? 'selected' : '' ?>>Kilinochchi</option>
                                <option value="Mannar" <?= ($user['district'] ?? '') === 'Mannar' ? 'selected' : '' ?>>Mannar</option>
                                <option value="Mullaitivu" <?= ($user['district'] ?? '') === 'Mullaitivu' ? 'selected' : '' ?>>Mullaitivu</option>
                                <option value="Vavuniya" <?= ($user['district'] ?? '') === 'Vavuniya' ? 'selected' : '' ?>>Vavuniya</option>

                                <option value="Batticaloa" <?= ($user['district'] ?? '') === 'Batticaloa' ? 'selected' : '' ?>>Batticaloa</option>
                                <option value="Ampara" <?= ($user['district'] ?? '') === 'Ampara' ? 'selected' : '' ?>>Ampara</option>
                                <option value="Trincomalee" <?= ($user['district'] ?? '') === 'Trincomalee' ? 'selected' : '' ?>>Trincomalee</option>

                                <option value="Kurunegala" <?= ($user['district'] ?? '') === 'Kurunegala' ? 'selected' : '' ?>>Kurunegala</option>
                                <option value="Puttalam" <?= ($user['district'] ?? '') === 'Puttalam' ? 'selected' : '' ?>>Puttalam</option>

                                <option value="Anuradhapura" <?= ($user['district'] ?? '') === 'Anuradhapura' ? 'selected' : '' ?>>Anuradhapura</option>
                                <option value="Polonnaruwa" <?= ($user['district'] ?? '') === 'Polonnaruwa' ? 'selected' : '' ?>>Polonnaruwa</option>

                                <option value="Badulla" <?= ($user['district'] ?? '') === 'Badulla' ? 'selected' : '' ?>>Badulla</option>
                                <option value="Monaragala" <?= ($user['district'] ?? '') === 'Monaragala' ? 'selected' : '' ?>>Monaragala</option>

                                <option value="Ratnapura" <?= ($user['district'] ?? '') === 'Ratnapura' ? 'selected' : '' ?>>Ratnapura</option>
                                <option value="Kegalle" <?= ($user['district'] ?? '') === 'Kegalle' ? 'selected' : '' ?>>Kegalle</option>
                            </select>
                            <?php endif; ?>
                            <?php if (($_SESSION["role"] ?? "") === "district_admin"): ?>
                                <input type="text" value="<?= escape($user['district'] ) ?>" placeholder="<?= escape($user['district'] ) ?>" readonly required>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="form-actions mt-15">
                        <button type="submit" name="update_profile" class="btn-solid-blue" id="saveChangesBtn" style="display: none;">
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editProfileBtn');
    const saveBtn = document.getElementById('saveChangesBtn');
    const editableFields = document.querySelectorAll('.editable-field');

    if (editBtn) {
        editBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            
            editableFields.forEach(field => {
                if (field.tagName === 'SELECT') {
                    field.removeAttribute('disabled');
                } else {
                    field.removeAttribute('readonly');
                }
                
               
                field.style.border = "1px solid #0056b3"; 
                field.style.backgroundColor = "#ffffff";
            });

        
            saveBtn.style.display = 'inline-block';
            editBtn.style.display = 'none';
        });
    }
});
</script>

<?php include __DIR__ . "/../layouts/footer.php"; ?>