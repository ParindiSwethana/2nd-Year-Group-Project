<?php

require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../config/csrf.php";
require_once __DIR__ . "/../models/DistrictAdmin.php";

$listPage = "../views/users/manage_disctict_admins.php";
$formPage = "../views/users/create_district_admin.php";

if (!isLoggedIn() || $_SESSION["role"] !== "super_admin") {
    header("Location: ../views/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . $listPage);
    exit();
}

verifyCsrfToken();

$action = $_POST["action"] ?? "";
$districtAdminModel = new DistrictAdmin();

if ($action === "delete") {
    $id = (int) ($_POST["user_id"] ?? 0);

    try {
        if ($id > 0 && $districtAdminModel->delete($id)) {
            $_SESSION["district_admin_success"] = "District Administrator account deleted.";
        } else {
            $_SESSION["district_admin_errors"] = ["District Administrator account not found."];
        }
    } catch (PDOException $exception) {
        $_SESSION["district_admin_errors"] = [
            $exception->getCode() === "23000"
                ? "This account cannot be deleted because other records (such as notices) are linked to it. Set its status to Inactive instead."
                : "A database error occurred while deleting the account."
        ];
    }

    header("Location: " . $listPage);
    exit();
}

if ($action !== "create" && $action !== "update") {
    header("Location: " . $listPage);
    exit();
}

$isUpdate = $action === "update";
$id = $isUpdate ? (int) ($_POST["user_id"] ?? 0) : 0;

if ($isUpdate && !$districtAdminModel->getById($id)) {
    $_SESSION["district_admin_errors"] = ["District Administrator account not found."];
    header("Location: " . $listPage);
    exit();
}

$districts = require __DIR__ . "/../config/districts.php";

$fullName = trim($_POST["full_name"] ?? "");
$dateOfBirth = trim($_POST["date_of_birth"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$phone = trim($_POST["phone"] ?? "");
$address = trim($_POST["address"] ?? "");
$district = trim($_POST["district"] ?? "");
$nic = strtoupper(trim($_POST["nic"] ?? ""));
$emergencyContactName = trim($_POST["emergency_contact_name"] ?? "");
$emergencyContactPhone = trim($_POST["emergency_contact_phone"] ?? "");
$status = $_POST["status"] ?? "active";
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";

$errors = [];

if ($fullName === "") {
    $errors[] = "Full name is required.";
}

if ($dateOfBirth === "") {
    $errors[] = "Date of birth is required.";
} elseif ($dateOfBirth > date("Y-m-d", strtotime("-18 years"))) {
    $errors[] = "A District Administrator must be at least 18 years old.";
}

if (!in_array($gender, ["Male", "Female"], true)) {
    $errors[] = "Please select a gender.";
}

if ($email === "") {
    $errors[] = "Email address is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
} elseif ($districtAdminModel->emailExists($email, $id)) {
    $errors[] = "This email address is already registered.";
}

if ($phone === "") {
    $errors[] = "Contact number is required.";
} elseif (!preg_match("/^[0-9+\-\s]{9,20}$/", $phone)) {
    $errors[] = "Enter a valid contact number.";
}

if ($address === "") {
    $errors[] = "Office address is required.";
}

if (!in_array($district, $districts, true)) {
    $errors[] = "Please select the district this administrator will manage.";
}

if ($nic === "") {
    $errors[] = "NIC number is required.";
} elseif (!preg_match("/^([0-9]{9}[VX]|[0-9]{12})$/", $nic)) {
    $errors[] = "Enter a valid NIC number.";
} elseif ($districtAdminModel->nicExists($nic, $id)) {
    $errors[] = "This NIC number is already registered.";
}

if ($emergencyContactName === "") {
    $errors[] = "Emergency contact name is required.";
}

if ($emergencyContactPhone === "") {
    $errors[] = "Emergency contact number is required.";
} elseif (!preg_match("/^[0-9+\-\s]{9,20}$/", $emergencyContactPhone)) {
    $errors[] = "Enter a valid emergency contact number.";
}

if ($isUpdate && !in_array($status, ["active", "inactive"], true)) {
    $errors[] = "Please select a valid account status.";
}

// Password is required when creating; when editing it only changes if a new one is entered
if (!$isUpdate || $password !== "") {
    if (strlen($password) < 8) {
        $errors[] = "Password must contain at least 8 characters.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
}

$oldValues = [
    "full_name" => $fullName,
    "date_of_birth" => $dateOfBirth,
    "gender" => $gender,
    "email" => $email,
    "phone" => $phone,
    "address" => $address,
    "district" => $district,
    "nic" => $nic,
    "emergency_contact_name" => $emergencyContactName,
    "emergency_contact_phone" => $emergencyContactPhone,
    "status" => $status
];

$backToForm = $formPage . ($isUpdate ? "?id=" . $id : "");

if (!empty($errors)) {
    $_SESSION["district_admin_errors"] = $errors;
    $_SESSION["district_admin_old"] = $oldValues;
    header("Location: " . $backToForm);
    exit();
}

$nameParts = preg_split("/\s+/", $fullName, 2);

$accountData = [
    "first_name" => $nameParts[0],
    "last_name" => $nameParts[1] ?? "",
    "date_of_birth" => $dateOfBirth,
    "gender" => $gender,
    "email" => $email,
    "phone" => $phone,
    "address" => $address,
    "district" => $district,
    "nic" => $nic,
    "emergency_contact_name" => $emergencyContactName,
    "emergency_contact_phone" => $emergencyContactPhone,
    "status" => $status
];

try {
    if ($isUpdate) {
        $districtAdminModel->update($id, $accountData);

        if ($password !== "") {
            $districtAdminModel->updatePassword($id, password_hash($password, PASSWORD_DEFAULT));
        }

        $_SESSION["district_admin_success"] = "District Administrator account updated.";
    } else {
        $accountData["password"] = password_hash($password, PASSWORD_DEFAULT);
        $districtAdminModel->create($accountData);

        $_SESSION["district_admin_success"] = "District Administrator account created. They can sign in with their email address.";
    }
} catch (PDOException $exception) {
    $_SESSION["district_admin_errors"] = ["A database error occurred. Please check the details and try again."];
    $_SESSION["district_admin_old"] = $oldValues;
    header("Location: " . $backToForm);
    exit();
}

header("Location: " . $listPage);
exit();
