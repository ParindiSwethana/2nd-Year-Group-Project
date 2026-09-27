<?php

require_once __DIR__ . "/../../config/session.php";

$registerErrors = $_SESSION["register_errors"] ?? [];
$old = $_SESSION["register_old"] ?? [];

unset($_SESSION["register_errors"]);
unset($_SESSION["register_old"]);

function oldRegisterValue($field, $old)
{
    return htmlspecialchars($old[$field] ?? "", ENT_QUOTES, "UTF-8");
}

function registerSelected($field, $value, $old)
{
    return (($old[$field] ?? "") === $value) ? "selected" : "";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResQ Lanka - Register</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/header_sidebar_navbar.css">
    <link rel="stylesheet" href="../../css/registerstyles.css">
</head>

<body>

<div class="background"></div>

<?php include __DIR__ . "/../layouts/navbar.php"; ?>

<div class="register-container">

    <main>

        <section class="register-card">
            <div class="welcome-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>

            <h2>
                CREATE YOUR
                <br>
                RESQ LANKA ACCOUNT
            </h2>

            <div class="divider"></div>

            <form id="registerForm" action="../../controllers/RegisterController.php" method="POST">

                <?php if (!empty($registerErrors)): ?>

                    <div class="form-errors">
                        <?php foreach ($registerErrors as $error): ?>

                            <p>
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
                            </p>

                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

                <div class="form-grid">

                    <div class="field full">
                        <label for="full_name">
                            Full Name
                        </label>

                        <div class="input-box">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" value="<?= oldRegisterValue("full_name",$old) ?>" maxlength="100" required>
                        </div>
                    </div>

                    <div class="field">
                        <label for="date_of_birth">
                            Date of Birth
                        </label>

                        <div class="input-box">
                            <i class="fa-solid fa-calendar-days"></i>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="<?= oldRegisterValue("date_of_birth",$old) ?>" max="<?= date("Y-m-d") ?>"required>
                        </div>
                    </div>

                    <div class="field">
                        <label for="gender">
                            Gender
                        </label>

                        <div class="input-box">
                            <i class="fa-solid fa-venus-mars"></i>

                            <select id="gender" name="gender" required>
                                <option value="">Select Gender
                                </option>

                                <option value="Male" <?= registerSelected("gender", "Male", $old) ?>>
                                    Male
                                </option>

                                <option value="Female" <?= registerSelected("gender", "Female", $old) ?>>
                                    Female
                                </option>
                            </select>

                        </div>

                    </div>

                    <div class="field">

                        <label for="email">
                            Email Address
                        </label>

                        <div class="input-box">

                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="example@email.com" value="<?= oldRegisterValue("email", $old) ?>" maxlength="100" required>

                        </div>

                    </div>

                    <div class="field">

                        <label for="phone">
                            Contact Number
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-phone"></i>
                            <input type="tel" id="phone" name="phone" placeholder="+94 77 1234567" value="<?= oldRegisterValue("phone", $old) ?>" maxlength="20" required>

                        </div>

                    </div>

                    <div class="field full">

                        <label for="address">
                            Home Address
                        </label>

                        <div class="input-box">

                        <i class="fa-solid fa-location-dot"></i>
                        <input type="text" id="address" name="address" placeholder="Enter your home address" value="<?= oldRegisterValue("address", $old) ?>" required>

                        </div>

                    </div>

                    <div class="field">

                        <label for="district">
                            District
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-map-location-dot"></i>

                            <select id="district" name="district" required>
                                <option value="">
                                    Select District
                                </option>

                                <option value="Ampara" <?= registerSelected("district", "Ampara", $old) ?>>
                                    Ampara
                                </option>

                                <option value="Anuradhapura" <?= registerSelected("district", "Anuradhapura", $old) ?>>
                                    Anuradhapura
                                </option>

                                <option value="Badulla" <?= registerSelected("district", "Badulla", $old) ?>>
                                    Badulla
                                </option>

                                <option value="Batticaloa" <?= registerSelected("district", "Batticaloa", $old) ?>>
                                    Batticaloa
                                </option>

                                <option value="Colombo" <?= registerSelected("district", "Colombo", $old) ?>>
                                    Colombo
                                </option>

                                <option value="Galle" <?= registerSelected("district", "Galle", $old) ?>>
                                    Galle
                                </option>

                                <option value="Gampaha" <?= registerSelected("district", "Gampaha", $old) ?>>
                                    Gampaha
                                </option>

                                <option value="Hambantota" <?= registerSelected("district", "Hambantota", $old) ?>>
                                    Hambantota
                                </option>

                                <option value="Jaffna" <?= registerSelected("district", "Jaffna", $old) ?>>
                                    Jaffna
                                </option>

                                <option value="Kalutara" <?= registerSelected("district", "Kalutara", $old) ?>>
                                    Kalutara
                                </option>

                                <option value="Kandy" <?= registerSelected("district", "Kandy", $old) ?>>
                                    Kandy
                                </option>

                                <option value="Kegalle" <?= registerSelected("district", "Kegalle", $old) ?>>
                                    Kegalle
                                </option>

                                <option value="Kilinochchi" <?= registerSelected("district", "Kilinochchi", $old) ?>>
                                    Kilinochchi
                                </option>

                                <option value="Kurunegala" <?= registerSelected("district", "Kurunegala", $old) ?>>
                                    Kurunegala
                                </option>

                                <option value="Mannar" <?= registerSelected("district", "Mannar", $old) ?>>
                                    Mannar
                                </option>

                                <option value="Matale" <?= registerSelected("district", "Matale", $old) ?>>
                                    Matale
                                </option>

                                <option value="Matara" <?= registerSelected("district", "Matara", $old) ?>>
                                    Matara
                                </option>

                                <option value="Monaragala" <?= registerSelected("district", "Monaragala", $old) ?>>
                                    Monaragala
                                </option>

                                <option value="Mullaitivu" <?= registerSelected("district", "Mullaitivu", $old) ?>>
                                    Mullaitivu
                                </option>

                                <option value="Nuwara Eliya" <?= registerSelected("district", "Nuwara Eliya", $old) ?>>
                                    Nuwara Eliya
                                </option>

                                <option value="Polonnaruwa" <?= registerSelected("district", "Polonnaruwa", $old) ?>>
                                    Polonnaruwa
                                </option>

                                <option value="Puttalam" <?= registerSelected("district", "Puttalam", $old) ?>>
                                    Puttalam
                                </option>

                                <option value="Ratnapura" <?= registerSelected("district", "Ratnapura", $old) ?>>
                                    Ratnapura
                                </option>

                                <option value="Trincomalee" <?= registerSelected("district", "Trincomalee", $old) ?>>
                                    Trincomalee
                                </option>

                                <option value="Vavuniya" <?= registerSelected("district", "Vavuniya", $old) ?>>
                                    Vavuniya
                                </option>
                            </select>

                        </div>

                    </div>

                    <div class="field">

                        <label for="occupation">
                            Occupation
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-briefcase"></i>
                            <input type="text" id="occupation" name="occupation" placeholder="Student / Engineer / Teacher" value="<?= oldRegisterValue("occupation", $old) ?>" maxlength="100" required>

                        </div>

                    </div>

                    <div class="field full">

                        <label for="nic">
                            NIC Number
                        </label>

                        <div class="input-box">

                            <i class="fa-regular fa-id-card"></i>
                            <input type="text" id="nic" name="nic" placeholder="Enter NIC Number" value="<?= oldRegisterValue("nic", $old) ?>" maxlength="12" required>

                        </div>

                    </div>

                    <div class="field">

                        <label for="password">
                            Password
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Create password" minlength="8" required>
                            <i class="fa-regular fa-eye toggle-password" data-target="password"></i>

                        </div>

                    </div>

                    <div class="field">

                        <label for="confirm_password">
                            Confirm Password
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" minlength="8" required>
                            <i class="fa-regular fa-eye toggle-password" data-target="confirm_password"></i>

                        </div>

                    </div>

                    <div class="field">

                        <label for="emergency_contact_name">
                            Emergency Contact Name
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-user-shield"></i>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" placeholder="Emergency Contact" value="<?= oldRegisterValue("emergency_contact_name", $old) ?>" maxlength="100" required>

                        </div>

                    </div>

                    <div class="field">

                        <label for="emergency_contact_phone">
                            Emergency Contact Number
                        </label>

                        <div class="input-box">

                            <i class="fa-solid fa-phone-volume"></i>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" placeholder="+94 71 1234567" value="<?= oldRegisterValue("emergency_contact_phone", $old) ?>" maxlength="20" required>

                        </div>
                    </div>

                </div>

                <div class="terms">

                    <input type="checkbox" id="terms" name="terms" value="1" <?= ($old["terms"] ?? "") === "1" ? "checked" : "" ?> required>
                    <label for="terms">
                        I agree to the <a href="#"> Terms &amp; Conditions </a> and <a href="#"> Privacy Policy </a>
                    </label>
                </div>

                <button type="submit" class="register-btn-main">
                    <i class="fa-solid fa-user-plus"></i>
                    CREATE ACCOUNT
                </button>
                
                <p class="login-link">
                    Already have an account?
                    <a href="login.php">Sign In</a>
                </p>
            </form>

        </section>

        <section class="assignment-card">

            <h2>WHY JOIN RESQ LANKA?</h2>

            <div class="assignment">
                <div class="number blue">1</div>
                <div class="icon blue-bg">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                </div>

                <div class="content">
                    <h3>
                        VOLUNTEER DURING
                        <br>
                        EMERGENCIES
                    </h3>
                    <p>
                        <i class="fa-solid fa-circle-check"></i>
                        Participate in rescue and disaster relief missions.
                    </p>
                </div>
                <div class="status">
                    OPEN
                </div>
            </div>

            <div class="assignment">
                <div class="number red">2</div>
                <div class="icon red-bg">
                    <i class="fa-solid fa-box-open"></i>
                </div>

                <div class="content">
                    <h3>
                        DONATE RELIEF
                        <br>
                        SUPPLIES
                    </h3>
                    <p>
                        <i class="fa-solid fa-circle-check"></i>
                        Help communities with food, medicine and essentials.
                    </p>
                </div>

                <div class="status">
                    ACTIVE
                </div>
            </div>

            <div class="info-box">

                <div class="shield">
                    <i class="fa-solid fa-shield"></i>
                </div>
                
                <div>
                    <h3>Together We Save Lives</h3>
                    <p>
                        Register today and become a trusted member
                        of Sri Lanka's disaster response network.
                    </p>
                </div>
            </div>

        </section>

    </main>

</div>

<script src="../../js/register.js"></script>
<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
