<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$fullName = $_SESSION["name"] ?? "Volunteer";
$tier = $_SESSION["tier"] ?? "Bronze";
$points = $_SESSION["points"] ?? 0;

$userId = $_SESSION["user_id"] ?? $_SESSION["id"] ?? null;

function escape($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

require_once "../../config/database.php";

$database = new Database();
$conn = $database->connect();

$applications = [];
$totalApplications = 0;
$upcomingCount = 0;
$inProgressCount = 0;
$completedCount = 0;
$totalHours = 0;


if ($userId !== null) {

    try {

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
                va.duration,
                va.volunteers_needed,
                va.volunteer_requirements,
                va.additional_info,
                va.status AS assignment_status,

                app.application_id,
                app.contact_number,
                app.availability,
                app.can_travel,
                app.comfortable_with_fuel,
                app.skills_experience,
                app.additional_note,
                app.agreed_to_terms,
                app.status AS application_status,
                app.applied_at,

                d.title AS disaster_title,
                d.disaster_type,
                d.description AS disaster_description,
                d.location AS disaster_location,
                d.priority AS disaster_priority,
                d.status AS disaster_status

            FROM VOLUNTEER_APPLICATION app

            INNER JOIN VOLUNTEER_ASSIGNMENT va
                ON app.assignment_id = va.assignment_id

            INNER JOIN DISASTER d
                ON va.disaster_id = d.disaster_id

            WHERE app.user_id = :user_id

            ORDER BY
                CASE
                    WHEN app.status = 'Pending' THEN 1
                    WHEN app.status = 'Approved' THEN 2
                    WHEN app.status = 'In Progress' THEN 3
                    WHEN app.status = 'Completed' THEN 4
                    WHEN app.status = 'Cancelled' THEN 5
                    ELSE 6
                END,

                va.start_date ASC
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ":user_id" => $userId
        ]);

        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        $totalApplications = count($applications);

        foreach ($applications as $application) {

            $status = strtolower(
                trim($application["application_status"] ?? "")
            );

            $assignmentStatus = strtolower(
                trim($application["assignment_status"] ?? "")
            );


            if (
                $status === "pending" ||
                $status === "approved"
            ) {
                $upcomingCount++;
            }


            if (
                $status === "in progress" ||
                $assignmentStatus === "in progress"
            ) {
                $inProgressCount++;
            }


            if ($status === "completed") {
                $completedCount++;
            }


            $duration = $application["duration"] ?? "";

            if (
                preg_match(
                    '/(\d+(?:\.\d+)?)\s*(?:hours?|hrs?|h)/i',
                    $duration,
                    $matches
                )
            ) {
                $totalHours += (float)$matches[1];
            }
        }

    } catch (PDOException $e) {

        $applications = [];

    }
}


$firstName = explode(
    " ",
    trim($fullName)
)[0];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Volunteer Assignments | ResQ Lanka</title>

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
        href="../../css/assignments.css"
    >

</head>


<body>

<div class="page-background"></div>


<?php include "../layouts/header.php"; ?>

<?php include "../layouts/navbar.php"; ?>


<div class="app-layout">

    <?php include "../layouts/sidebar.php"; ?>


    <main class="assignment-content">

        <section class="page-heading">

            <div>

                <span class="section-label">
                    VOLUNTEER ACTIVITY
                </span>

                <h2>
                    Volunteer Assignments
                </h2>

                <p>
                    View and manage all the disaster response
                    activities you have applied for.
                </p>

            </div>

        </section>


        <section class="stats-grid">

            <article class="stat-card stat-blue">

                <div class="stat-top">

                    <div class="stat-icon">
                        <i class="fa-regular fa-calendar"></i>
                    </div>

                    <span class="stat-trend">
                        Upcoming
                    </span>

                </div>

                <div class="stat-number">
                    <?= escape($upcomingCount) ?>
                </div>

                <h3>
                    Upcoming Assignments
                </h3>

            </article>


            <article class="stat-card stat-green">

                <div class="stat-top">

                    <div class="stat-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <span class="stat-trend">
                        Active
                    </span>

                </div>

                <div class="stat-number">
                    <?= escape($inProgressCount) ?>
                </div>

                <h3>
                    In Progress
                </h3>

            </article>


            <article class="stat-card stat-orange">

                <div class="stat-top">

                    <div class="stat-icon">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>

                    <span class="stat-trend">
                        Completed
                    </span>

                </div>

                <div class="stat-number">
                    <?= escape($completedCount) ?>
                </div>

                <h3>
                    Completed Assignments
                </h3>

            </article>


            <article class="stat-card stat-purple">

                <div class="stat-top">

                    <div class="stat-icon">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>

                    <span class="stat-trend">
                        Total
                    </span>

                </div>

                <div class="stat-number">
                    <?= escape($totalHours) ?>
                </div>

                <h3>
                    Total Hours
                </h3>

            </article>

        </section>


        <section class="filter-bar">

            <div class="filter-tabs">

                <button
                    class="filter-tab active"
                    data-filter="all"
                    type="button"
                >
                    All
                </button>

                <button
                    class="filter-tab"
                    data-filter="upcoming"
                    type="button"
                >
                    Upcoming
                </button>

                <button
                    class="filter-tab"
                    data-filter="in-progress"
                    type="button"
                >
                    In Progress
                </button>

                <button
                    class="filter-tab"
                    data-filter="completed"
                    type="button"
                >
                    Completed
                </button>

                <button
                    class="filter-tab"
                    data-filter="cancelled"
                    type="button"
                >
                    Cancelled
                </button>

            </div>

        </section>


        <section class="assignment-list">

            <?php if (empty($applications)): ?>

                <div class="empty-state">

                    <div class="empty-icon">
                        <i class="fa-regular fa-file-lines"></i>
                    </div>

                    <h3>
                        No Volunteer Assignments Yet
                    </h3>

                    <p>
                        You have not applied for any volunteer
                        assignments yet.
                    </p>

                    <a
                        href="../disaster/disaster_details.php"
                        class="browse-button"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Browse Active Disasters
                    </a>

                </div>

            <?php else: ?>


                <?php foreach ($applications as $index => $application): ?>

                    <?php

                    $applicationStatus =
                        strtolower(
                            trim(
                                $application["application_status"]
                                ?? "Pending"
                            )
                        );

                    $assignmentStatus =
                        strtolower(
                            trim(
                                $application["assignment_status"]
                                ?? "Ongoing"
                            )
                        );


                    if (
                        $applicationStatus === "completed"
                    ) {

                        $statusClass = "completed";
                        $statusText = "COMPLETED";

                    } elseif (
                        $applicationStatus === "cancelled"
                    ) {

                        $statusClass = "cancelled";
                        $statusText = "CANCELLED";

                    } elseif (
                        $applicationStatus === "in progress" ||
                        $assignmentStatus === "in progress"
                    ) {

                        $statusClass = "in-progress";
                        $statusText = "IN PROGRESS";

                    } elseif (
                        $applicationStatus === "approved"
                    ) {

                        $statusClass = "upcoming";
                        $statusText = "APPROVED";

                    } else {

                        $statusClass = "upcoming";
                        $statusText = "PENDING";

                    }


                    $startDate = !empty(
                        $application["start_date"]
                    )
                        ? date(
                            "d M Y",
                            strtotime(
                                $application["start_date"]
                            )
                        )
                        : "Not specified";


                    $endDate = !empty(
                        $application["end_date"]
                    )
                        ? date(
                            "d M Y",
                            strtotime(
                                $application["end_date"]
                            )
                        )
                        : "Not specified";


                    $appliedDate = !empty(
                        $application["applied_at"]
                    )
                        ? date(
                            "d M Y",
                            strtotime(
                                $application["applied_at"]
                            )
                        )
                        : "Not available";


                    $category =
                        strtolower(
                            $application["assignment_category"]
                            ?? ""
                        );


                    if (
                        strpos($category, "medical") !== false
                    ) {

                        $icon = "fa-solid fa-kit-medical";

                    } elseif (
                        strpos($category, "food") !== false ||
                        strpos($category, "distribution") !== false
                    ) {

                        $icon = "fa-solid fa-box-open";

                    } elseif (
                        strpos($category, "clean") !== false
                    ) {

                        $icon = "fa-solid fa-broom";

                    } elseif (
                        strpos($category, "relief") !== false
                    ) {

                        $icon = "fa-solid fa-people-carry-box";

                    } else {

                        $icon = "fa-solid fa-people-group";

                    }

                    ?>


                    <article
                        class="assignment-card <?= escape($statusClass) ?>"
                        data-status="<?= escape($statusClass) ?>"
                    >

                        <div class="assignment-icon">

                            <i class="<?= escape($icon) ?>"></i>

                        </div>


                        <div class="assignment-main">

                            <div class="assignment-title-row">

                                <span
                                    class="status-badge <?= escape($statusClass) ?>"
                                >
                                    <?= escape($statusText) ?>
                                </span>

                                <span class="application-badge">
                                    Applied
                                </span>

                            </div>


                            <h3>
                                <?= escape(
                                    $application["assignment_title"]
                                ) ?>
                            </h3>


                            <div class="assignment-meta">

                                <span>

                                    <i class="fa-solid fa-location-dot"></i>

                                    <?= escape(
                                        $application["assignment_location"]
                                    ) ?>

                                </span>


                                <span>

                                    <i class="fa-regular fa-calendar"></i>

                                    <?= escape($startDate) ?>

                                    <?php if (
                                        $startDate !== $endDate
                                    ): ?>

                                        -
                                        <?= escape($endDate) ?>

                                    <?php endif; ?>

                                </span>


                                <span>

                                    <i class="fa-solid fa-users"></i>

                                    <?= escape(
                                        $application["volunteers_needed"]
                                    ) ?>

                                    Volunteers Needed

                                </span>


                                <?php if (
                                    !empty(
                                        $application["duration"]
                                    )
                                ): ?>

                                    <span>

                                        <i class="fa-regular fa-clock"></i>

                                        <?= escape(
                                            $application["duration"]
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="assignment-extra">

                                <span>

                                    <strong>
                                        Disaster:
                                    </strong>

                                    <?= escape(
                                        $application["disaster_title"]
                                    ) ?>

                                </span>


                                <span>

                                    <strong>
                                        Applied:
                                    </strong>

                                    <?= escape($appliedDate) ?>

                                </span>

                            </div>

                        </div>


                        <div class="assignment-right">

                            <div class="severity">

                                <span>
                                    Severity
                                </span>

                                <strong>
                                    <?= escape(
                                        $application["severity"]
                                    ) ?>
                                </strong>

                            </div>


                            <a
                                href="assignment_details.php?id=<?= escape(
                                    $application["assignment_id"]
                                ) ?>"
                                class="view-button"
                            >
                                View Details

                                <i class="fa-solid fa-chevron-right"></i>

                            </a>

                        </div>

                    </article>


                <?php endforeach; ?>

            <?php endif; ?>

        </section>


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


        <?php include "../layouts/footer.php"; ?>


    </main>

</div>


<script>

const filterTabs =
    document.querySelectorAll(".filter-tab");

const assignmentCards =
    document.querySelectorAll(".assignment-card");


filterTabs.forEach(function(tab) {

    tab.addEventListener("click", function() {

        filterTabs.forEach(function(item) {
            item.classList.remove("active");
        });


        tab.classList.add("active");


        const filter =
            tab.getAttribute("data-filter");


        assignmentCards.forEach(function(card) {

            const status =
                card.getAttribute("data-status");


            if (
                filter === "all" ||
                status === filter
            ) {

                card.style.display = "grid";

            } else {

                card.style.display = "none";

            }

        });

    });

});

</script>


</body>
</html>
