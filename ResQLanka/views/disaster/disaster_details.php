<?php
$pageTitle = "Disaster Details | ResQ Lanka";
$activePage = "disasters";
require_once __DIR__ . "/../../config/session.php";
requireRole("registered_user");
include __DIR__ . "/../layouts/header.php";
include __DIR__ . "/../layouts/navbar.php";
?>
<?php

require_once "../../config/database.php";

$fullName = $_SESSION["name"] ?? "Volunteer";
$tier = $_SESSION["tier"] ?? "Bronze";
$points = $_SESSION["points"] ?? 0;

function escape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

try {
    $database = new Database();
    $conn = $database->connect();

    /*
     * Active disaster cards are created from VOLUNTEER_ASSIGNMENT.
     * The DISASTER table supplies the disaster information.
     *
     * One card = one volunteer assignment.
     */
    $sql = "
        SELECT
            va.assignment_id,
            va.disaster_id,
            va.title AS assignment_title,
            va.assignment_category,
            va.description AS assignment_description,
            va.location AS assignment_location,
            va.start_date,
            va.end_date,
            va.severity,
            va.duration AS assignment_duration,
            va.volunteers_needed,
            va.volunteer_requirements,
            va.additional_info,
            va.status AS assignment_status,
            va.created_at,

            d.title AS disaster_title,
            d.disaster_type,
            d.description AS disaster_description,
            d.location AS disaster_location,
            d.priority,
            d.status AS disaster_status,
            d.duration AS disaster_duration
        FROM VOLUNTEER_ASSIGNMENT va
        INNER JOIN DISASTER d
            ON d.disaster_id = va.disaster_id
        WHERE
            LOWER(va.status) = 'ongoing'
            AND LOWER(d.status) = 'ongoing'
        ORDER BY
            CASE
                WHEN LOWER(va.severity) = 'critical' THEN 1
                WHEN LOWER(va.severity) = 'high' THEN 2
                WHEN LOWER(va.severity) = 'moderate' THEN 3
                ELSE 4
            END,
            va.start_date ASC,
            va.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $assignments = [];
    $dbError = "Unable to load active disasters.";
}
?>
    <!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Active Disasters | ResQ Lanka</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../../css/disaster_details.css"
    >

</head>
<body>
<div class="page-background"></div>

<div class="app-layout">
    <?php include __DIR__ . "/../layouts/sidebar.php"; ?>
    <main class="main-content">

        <div class="page-heading">

            <div>

                <span class="section-label">
                    LIVE RESPONSE OPPORTUNITIES
                </span>

                <h2>
                    Active Disasters
                </h2>

                <p>
                    View ongoing disasters and volunteer assignments available across Sri Lanka.
                </p>

            </div>

            <div class="heading-badge">

                <i class="fa-solid fa-bullhorn"></i>

                <span>
                    <?= count($assignments) ?> Active
                </span>

            </div>

        </div>

        <?php if (!empty($dbError)): ?>

            <div class="message error-message">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= escape($dbError) ?>

            </div>

        <?php endif; ?>

        <?php if (empty($assignments)): ?>

            <section class="empty-state">

                <div class="empty-icon">

                    <i class="fa-solid fa-shield-heart"></i>

                </div>

                <h3>
                    No Active Volunteer Assignments
                </h3>

                <p>
                    There are currently no ongoing volunteer assignments.
                    Please check again later.
                </p>

            </section>

        <?php else: ?>

            <section class="disaster-list">

                <?php foreach ($assignments as $index => $assignment): ?>

                    <?php

                    $severity = strtolower(
                        trim(
                            $assignment["severity"]
                            ?? $assignment["priority"]
                            ?? "moderate"
                        )
                    );

                    $severityClass = "moderate";

                    if (in_array($severity, ["critical", "high"], true)) {
                        $severityClass = "critical";
                    } elseif (in_array($severity, ["minor", "low"], true)) {
                        $severityClass = "minor";
                    }

                    $title =
                        $assignment["assignment_title"]
                        ?: $assignment["disaster_title"];

                    $location =
                        $assignment["assignment_location"]
                        ?: $assignment["disaster_location"];

                    ?>

                    <article class="disaster-card <?= escape($severityClass) ?>">

                        <div class="disaster-image">

                            <div class="image-placeholder">

                                <?php if ($severityClass === "critical"): ?>

                                    <i class="fa-solid fa-house-flood-water"></i>

                                <?php elseif ($severityClass === "moderate"): ?>

                                    <i class="fa-solid fa-mountain-sun"></i>

                                <?php else: ?>

                                    <i class="fa-solid fa-cloud-rain"></i>

                                <?php endif; ?>

                            </div>

                            <span class="severity-badge">
                                <?= escape(strtoupper($assignment["severity"])) ?>
                            </span>

                        </div>

                        <div class="disaster-info">

                            <div class="disaster-top">

                                <div>

                                    <span class="disaster-type">
                                        <?= escape($assignment["disaster_type"]) ?>
                                    </span>

                                    <h3>
                                        <?= escape($title) ?>
                                    </h3>

                                </div>

                                <span class="status-badge">
                                    <?= escape($assignment["assignment_status"]) ?>
                                </span>

                            </div>

                            <div class="location-row">

                                <i class="fa-solid fa-location-dot"></i>

                                <?= escape($location) ?>

                            </div>

                            <p class="description">

                                <?= escape($assignment["assignment_description"]) ?>

                            </p>

                            <div class="disaster-meta">

                                <div>

                                    <i class="fa-regular fa-calendar"></i>

                                    <span>

                                        <?= escape(
                                            date(
                                                "M d, Y",
                                                strtotime($assignment["start_date"])
                                            )
                                        ) ?>

                                        -

                                        <?= escape(
                                            date(
                                                "M d, Y",
                                                strtotime($assignment["end_date"])
                                            )
                                        ) ?>

                                    </span>

                                </div>

                                <div>

                                    <i class="fa-solid fa-users"></i>

                                    <span>

                                        <?= escape(
                                            $assignment["volunteers_needed"]
                                        ) ?>

                                        volunteers requested

                                    </span>

                                </div>

                                <div>

                                    <i class="fa-regular fa-clock"></i>

                                    <span>

                                        <?= escape(
                                            $assignment["assignment_duration"]
                                            ?: "Not specified"
                                        ) ?>

                                    </span>

                                </div>

                            </div>

                            <div class="disaster-bottom">

                                <span class="reported">

                                    <i class="fa-regular fa-clock"></i>

                                    Reported
                                    <?= escape(
                                        date(
                                            "M d, Y",
                                            strtotime($assignment["created_at"])
                                        )
                                    ) ?>

                                </span>

                                <a
                                    href="apply_assignment.php?assignment_id=<?= (int)$assignment["assignment_id"] ?>"
                                    class="view-details"
                                >

                                    View Details

                                    <i class="fa-solid fa-chevron-right"></i>

                                </a>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

        <section class="community-strip">

            <div class="community-message">

                <div class="community-main-icon">

                    <i class="fa-solid fa-shield-halved"></i>

                </div>

                <div>

                    <h3>
                        Every Action Counts.
                    </h3>

                    <p>
                        Prepared communities save lives.
                    </p>

                </div>

            </div>

            <div class="community-actions">

                <div class="community-item">

                    <i class="fa-solid fa-suitcase-rolling"></i>

                    <span>
                        Be Prepared
                    </span>

                </div>

                <div class="community-divider"></div>

                <div class="community-item">

                    <i class="fa-solid fa-users"></i>

                    <span>
                        Help Others
                    </span>

                </div>

                <div class="community-divider"></div>

                <div class="community-item">

                    <i class="fa-solid fa-hand-holding-heart"></i>

                    <span>
                        Save Lives
                    </span>

                </div>

            </div>

        </section>

        <?php include __DIR__ . "/../layouts/footer.php"; ?>

    </main>

</div>
</body>
</html>    

