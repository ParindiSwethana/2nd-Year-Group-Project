<?php
require_once __DIR__ . "/../../config/session.php";

$pageTitle = $pageTitle ?? "ResQ Lanka";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8") ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/header_sidebar_navbar.css">

    <?php if (!empty($pageCSS)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($pageCSS, ENT_QUOTES, "UTF-8") ?>">
    <?php endif; ?>
</head>

<body>
    <div class="page-background"></div>
