<?php

require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../models/User.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/auth/register.php");
    exit();
}

$fullName = trim($_POST["full_name"] ?? "");
$dateOfBirth = trim($_POST["date_of_birth"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$phone = trim($_POST["phone"] ?? "");
$address = trim($_POST["address"] ?? "");
$district = trim($_POST["district"] ?? "");
$occupation = trim($_POST["occupation"] ?? "");
$nic = strtoupper(trim($_POST["nic"] ?? ""));
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";
$emergencyContactName =
    trim($_POST["emergency_contact_name"] ?? "");
$emergencyContactPhone =
    trim($_POST["emergency_contact_phone"] ?? "");
$termsAccepted = isset($_POST["terms"]);

$errors = [];

if ($fullName === "") {
    $errors[] = "Full name is required.";
}

if ($dateOfBirth === "") {
    $errors[] = "Date of birth is required.";
} elseif ($dateOfBirth > date("Y-m-d")) {
    $errors[] = "Date of birth cannot be a future date.";
}

if (!in_array($gender, ["Male", "Female"], true)) {
    $errors[] = "Please select your gender.";
}

if ($email === "") {
    $errors[] = "Email address is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}

if ($phone === "") {
    $errors[] = "Contact number is required.";
} elseif (!preg_match("/^[0-9+\-\s]{9,20}$/", $phone)) {
    $errors[] = "Enter a valid contact number.";
}

if ($address === "") {
    $errors[] = "Home address is required.";
}

if ($district === "") {
    $errors[] = "Please select your district.";
}

if ($occupation === "") {
    $errors[] = "Occupation is required.";
}

if ($nic === "") {
    $errors[] = "NIC number is required.";
} elseif (!preg_match(
    "/^([0-9]{9}[VXvx]|[0-9]{12})$/",
    $nic
)) {
    $errors[] = "Enter a valid NIC number.";
}

if (strlen($password) < 8) {
    $errors[] = "Password must contain at least 8 characters.";
}

if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if ($emergencyContactName === "") {
    $errors[] = "Emergency contact name is required.";
}

if ($emergencyContactPhone === "") {
    $errors[] = "Emergency contact number is required.";
} elseif (!preg_match(
    "/^[0-9+\-\s]{9,20}$/",
    $emergencyContactPhone
)) {
    $errors[] = "Enter a valid emergency contact number.";
}

if (!$termsAccepted) {
    $errors[] = "You must accept the terms and conditions.";
}

$userModel = new User();

/*
The registration page does not contain a separate username field.
Therefore, the email is used as the username.

The login model accepts either a username or an email.
*/
$username = $email;

if ($email !== "" && $userModel->emailExists($email)) {
    $errors[] = "This email address is already registered.";
}

if ($nic !== "" && $userModel->nicExists($nic)) {
    $errors[] = "This NIC number is already registered.";
}

$oldValues = [
    "full_name" => $fullName,
    "date_of_birth" => $dateOfBirth,
    "gender" => $gender,
    "email" => $email,
    "phone" => $phone,
    "address" => $address,
    "district" => $district,
    "occupation" => $occupation,
    "nic" => $nic,
    "emergency_contact_name" => $emergencyContactName,
    "emergency_contact_phone" => $emergencyContactPhone,
    "terms" => $termsAccepted ? "1" : ""
];

if (!empty($errors)) {
    $_SESSION["register_errors"] = $errors;
    $_SESSION["register_old"] = $oldValues;

    header("Location: ../views/auth/register.php");
    exit();
}

/*
Split the full name into first name and last name
because the users table stores them separately.
*/
$nameParts = preg_split(
    "/\s+/",
    $fullName,
    2
);

$firstName = $nameParts[0];
$lastName = $nameParts[1] ?? "";

$userData = [
    "first_name" => $firstName,
    "last_name" => $lastName,
    "date_of_birth" => $dateOfBirth,
    "gender" => $gender,
    "username" => $username,
    "email" => $email,
    "password" => password_hash(
        $password,
        PASSWORD_DEFAULT
    ),
    "phone" => $phone,
    "address" => $address,
    "district" => $district,
    "occupation" => $occupation,
    "nic" => $nic,
    "emergency_contact_name" => $emergencyContactName,
    "emergency_contact_phone" => $emergencyContactPhone
];

try {
    if ($userModel->register($userData)) {
        unset($_SESSION["register_errors"]);
        unset($_SESSION["register_old"]);

        $_SESSION["login_success"] =
            "Registration successful. Use your email address to sign in.";

        header("Location: ../views/auth/login.php");
        exit();
    }

    $_SESSION["register_errors"] = [
        "Registration failed. Please try again."
    ];
} catch (PDOException $exception) {
    $_SESSION["register_errors"] = [
        "A database error occurred while creating your account."
    ];
}

$_SESSION["register_old"] = $oldValues;

header("Location: ../views/auth/register.php");
exit();
