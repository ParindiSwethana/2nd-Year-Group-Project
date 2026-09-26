<!DOCTYPE html>
<html>
<head>
    <title>Create Disaster</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav>
        <h2>ResQ Lanka Admin</h2>
        <a href="index.php">Dashboard</a>
    </nav>

    <div class="container">
        <h1>Create Disaster Incident</h1>

        <form action="save_incident.php" method="POST">
            <label>Disaster Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description"></textarea>

            <label>District</label>
            <input type="text" name="district" required>

            <label>Location</label>
            <input type="text" name="location" required>

            <label>Volunteers Needed</label>
            <input type="number" name="volunteers" required>

            <label>Priority</label>
            <select name="priority">
                <option>High</option>
                <option>Medium</option>
                <option>Low</option>
            </select>

            <button>Create Incident</button>
        </form>
    </div>
</body>
</html>
