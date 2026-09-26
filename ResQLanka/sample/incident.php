<?php
include "db.php";

$id = $_GET['id'];

$result = mysqli_query(
    $conn,
    "SELECT * FROM disaster_incidents WHERE id=$id"
);

$data = mysqli_fetch_assoc($result);
?>

<html>
<head>
    <title>Incident Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav>
        <h2>ResQ Lanka</h2>
    </nav>

    <div class="container">
        <div class="card">
            <h1><?php echo $data['title']; ?></h1>

            <p><?php echo $data['description']; ?></p>

            <h3>Location:</h3>
            <p><?php echo $data['location']; ?></p>

            <h3>Volunteers Required:</h3>
            <p><?php echo $data['volunteers_needed']; ?></p>

            <h3>Priority:</h3>
            <p><?php echo $data['priority']; ?></p>

            <a class="btn">Accept Assignment</a>
        </div>
    </div>
</body>
</html>
