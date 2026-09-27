<?php

session_start();

require_once "../../config/database.php";

$database = new Database();
$conn = $database->connect();

$createdBy = $_SESSION["user_id"] ?? null;

function escape($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

$successMessage = "";
$errorMessage = "";


$disasters = [];

$disasterQuery = "
    SELECT
        disaster_id,
        title,
        disaster_type,
        location,
        priority,
        status
    FROM DISASTER
    WHERE status <> 'Resolved'
    ORDER BY created_at DESC
";

try {

    $disasterResult = $conn->query($disasterQuery);

    $disasters = $disasterResult->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $errorMessage = "Failed to load disasters: " . $e->getMessage();

}

$locations = [];

$locationQuery = "
    SELECT DISTINCT location
    FROM DISASTER
    WHERE location IS NOT NULL
      AND location <> ''
    ORDER BY location ASC
";

try {

    $locationResult = $conn->query($locationQuery);

    $locationRows = $locationResult->fetchAll(PDO::FETCH_ASSOC);

    foreach ($locationRows as $row) {

        $locations[] = $row["location"];

    }

} catch (PDOException $e) {

    $errorMessage = "Failed to load locations: " . $e->getMessage();

}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    
    $disasterId = $_POST["disaster_id"] ?? "";
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["assignment_category"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $volunteersNeeded = trim($_POST["volunteers_needed"] ?? "");
    $severity = trim($_POST["severity"] ?? "");
    $startDate = $_POST["start_date"] ?? "";
    $endDate = $_POST["end_date"] ?? "";
    $applicationDeadline = $_POST["application_deadline"] ?? "";
    $duration = trim($_POST["duration"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $additionalInfo = trim($_POST["additional_info"] ?? "");

    

    $requirements = $_POST["volunteer_requirements"] ?? [];

    if (!empty($requirements)) {

        $volunteerRequirements = implode(", ", $requirements);

    } else {

        $volunteerRequirements = "";

    }
   

    if (
        empty($disasterId) ||
        empty($title) ||
        empty($category) ||
        empty($location) ||
        empty($volunteersNeeded) ||
        empty($severity) ||
        empty($startDate) ||
        empty($endDate) ||
        empty($applicationDeadline) ||
        empty($duration) ||
        empty($description) ||
        empty($volunteerRequirements)
    ) {

        $errorMessage = "Please fill in all required fields.";

    } elseif ($endDate < $startDate) {

        $errorMessage = "End date cannot be before the start date.";

    } elseif ($applicationDeadline > $endDate) {

        $errorMessage =
            "Application deadline cannot be after the assignment end date.";

    } else {

       

        $sql = "
            INSERT INTO VOLUNTEER_ASSIGNMENT
            (
                disaster_id,
                title,
                assignment_category,
                description,
                location,
                start_date,
                end_date,
                application_deadline,
                severity,
                duration,
                volunteers_needed,
                volunteer_requirements,
                additional_info,
                status,
                created_by
            )
            VALUES
            (
                :disaster_id,
                :title,
                :assignment_category,
                :description,
                :location,
                :start_date,
                :end_date,
                :application_deadline,
                :severity,
                :duration,
                :volunteers_needed,
                :volunteer_requirements,
                :additional_info,
                'Ongoing',
                :created_by
            )
        ";

        try {

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ":disaster_id" => $disasterId,
                ":title" => $title,
                ":assignment_category" => $category,
                ":description" => $description,
                ":location" => $location,
                ":start_date" => $startDate,
                ":end_date" => $endDate,
                ":application_deadline" => $applicationDeadline,
                ":severity" => $severity,
                ":duration" => $duration,
                ":volunteers_needed" => $volunteersNeeded,
                ":volunteer_requirements" => $volunteerRequirements,
                ":additional_info" => $additionalInfo,
                ":created_by" => $createdBy
            ]);

            $successMessage =
                "Assignment request created successfully.";

          

            $disasterId = "";
            $title = "";
            $category = "";
            $location = "";
            $volunteersNeeded = "";
            $severity = "";
            $startDate = "";
            $endDate = "";
            $applicationDeadline = "";
            $duration = "";
            $description = "";
            $additionalInfo = "";
            $requirements = [];

        } catch (PDOException $e) {

            $errorMessage =
                "Failed to create assignment: "
                . $e->getMessage();

        }

    }
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

    <title>Create Assignment | ResQ Lanka</title>


    <!-- PAGE CSS -->

    <link
        rel="stylesheet"
        href="../../css/assignmentstyle.css"
    >

</head>


<body>



<?php include "../layouts/header.php"; ?>

<?php include "../layouts/navbar.php"; ?>

<div class="app-layout">

<?php include "../layouts/district_admin_sidebar.php"; ?>



<main class="main-content">


    <!-- BREADCRUMB -->

    <div class="breadcrumb">

        <span>Manage Disasters</span>

        <i class="fa-solid fa-chevron-right"></i>

        <span>Create Assignment Request</span>

    </div>



    <!-- PAGE TITLE -->

    <section class="page-title">

        <div>

            <h1>
                Create Disaster Assignment Request
            </h1>

            <p>
                Enter the details needed to publish a volunteer
                assignment for this disaster
            </p>

        </div>


        <div class="admin-badge">

            <i class="fa-solid fa-location-dot"></i>

            GAMPAHA ADMIN

        </div>

    </section>


    
    <?php if (!empty($successMessage)): ?>

        <div class="message success-message">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                <?= escape($successMessage) ?>
            </span>

        </div>

    <?php endif; ?>

    
    <?php if (!empty($errorMessage)): ?>

        <div class="message error-message">

            <i class="fa-solid fa-circle-exclamation"></i>

            <span>
                <?= escape($errorMessage) ?>
            </span>

        </div>

    <?php endif; ?>

    

    <div class="content-grid">

        
        <section class="form-card">


            <form
                method="POST"
                action=""
            >


                <!-- ROW 1 -->

                <div class="form-grid">


                    <!-- DISASTER -->

                    <div class="form-group">

                        <label>

                            1. Related Disaster / Event

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <select
                                name="disaster_id"
                                id="disaster_id"
                                required
                            >

                                <option value="">
                                    Select a disaster / event
                                </option>


                                <?php foreach ($disasters as $disaster): ?>

                                    <option
                                        value="<?= escape($disaster['disaster_id']) ?>"
                                        data-location="<?= escape($disaster['location']) ?>"
                                        <?= (
                                            isset($disasterId)
                                            && $disasterId == $disaster['disaster_id']
                                        )
                                            ? "selected"
                                            : ""
                                        ?>
                                    >

                                        <?= escape($disaster['title']) ?>

                                        -

                                        <?= escape($disaster['location']) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <i class="fa-solid fa-chevron-down"></i>

                        </div>

                    </div>



                    <!-- ASSIGNMENT TITLE -->

                    <div class="form-group">

                        <label>

                            2. Assignment Title

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <input
                                type="text"
                                name="title"
                                maxlength="150"
                                placeholder="Enter a clear title for this assignment"
                                value="<?= escape($title ?? '') ?>"
                                required
                            >

                        </div>

                    </div>



                    <!-- CATEGORY -->

                    <div class="form-group">

                        <label>

                            3. Assignment Category

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <select
                                name="assignment_category"
                                required
                            >

                                <option value="">
                                    Select category
                                </option>

                                <option
                                    value="Rescue"
                                    <?= (($category ?? '') === 'Rescue') ? 'selected' : '' ?>
                                >
                                    Rescue
                                </option>

                                <option
                                    value="Relief Distribution"
                                    <?= (($category ?? '') === 'Relief Distribution') ? 'selected' : '' ?>
                                >
                                    Relief Distribution
                                </option>

                                <option
                                    value="Medical Assistance"
                                    <?= (($category ?? '') === 'Medical Assistance') ? 'selected' : '' ?>
                                >
                                    Medical Assistance
                                </option>

                                <option
                                    value="Food Distribution"
                                    <?= (($category ?? '') === 'Food Distribution') ? 'selected' : '' ?>
                                >
                                    Food Distribution
                                </option>

                                <option
                                    value="Clean-up"
                                    <?= (($category ?? '') === 'Clean-up') ? 'selected' : '' ?>
                                >
                                    Clean-up
                                </option>

                                <option
                                    value="Shelter Support"
                                    <?= (($category ?? '') === 'Shelter Support') ? 'selected' : '' ?>
                                >
                                    Shelter Support
                                </option>

                                <option
                                    value="Other"
                                    <?= (($category ?? '') === 'Other') ? 'selected' : '' ?>
                                >
                                    Other
                                </option>

                            </select>

                            <i class="fa-solid fa-chevron-down"></i>

                        </div>

                    </div>



                    <!-- LOCATION -->

                    <div class="form-group">

                        <label>

                            4. Location / Town

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <select
                                name="location"
                                id="location"
                                required
                            >

                                <option value="">
                                    Select location / town
                                </option>


                                <?php foreach ($locations as $loc): ?>

                                    <option
                                        value="<?= escape($loc) ?>"
                                        <?= (($location ?? '') === $loc) ? 'selected' : '' ?>
                                    >

                                        <?= escape($loc) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <i class="fa-solid fa-chevron-down"></i>

                        </div>

                    </div>



                    <!-- VOLUNTEERS -->

                    <div class="form-group">

                        <label>

                            5. Number of Volunteers Needed

                            <span>*</span>

                        </label>

                        <div class="input-wrapper icon-input">

                            <input
                                type="number"
                                name="volunteers_needed"
                                min="1"
                                placeholder="Enter number of volunteers"
                                value="<?= escape($volunteersNeeded ?? '') ?>"
                                required
                            >

                            <i class="fa-solid fa-users"></i>

                        </div>

                    </div>



                    <!-- PRIORITY -->

                    <div class="form-group">

                        <label>

                            6. Priority / Criticality

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <select
                                name="severity"
                                required
                            >

                                <option value="">
                                    Select priority level
                                </option>

                                <option value="">
                                    Select priority level
                                </option>

                                <option
                                    value="Critical"
                                    <?= (($severity ?? '') === 'Critical') ? 'selected' : '' ?>
                                >
                                    Critical
                                </option>

                                <option
                                    value="Moderate"
                                    <?= (($severity ?? '') === 'Moderate') ? 'selected' : '' ?>
                                >
                                    Moderate
                                </option>

                                <option
                                    value="Minor"
                                    <?= (($severity ?? '') === 'Minor') ? 'selected' : '' ?>
                                >
                                    Minor
                                </option>
                            </select>

                            <i class="fa-solid fa-chevron-down"></i>

                        </div>

                    </div>



                    <!-- START DATE -->

                    <div class="form-group">

                        <label>

                            7. Start Date

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <input
                                type="date"
                                name="start_date"
                                value="<?= escape($startDate ?? '') ?>"
                                required
                            >

                        </div>

                    </div>



                    <!-- APPLICATION DEADLINE -->

                    <div class="form-group">

                        <label>

                            8. Application Deadline

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <input
                                type="date"
                                name="application_deadline"
                                id="application_deadline"
                                value="<?= escape($applicationDeadline ?? '') ?>"
                                required
                            >

                        </div>

                    </div>



                    <!-- END DATE -->

                    <div class="form-group">

                        <label>

                            9. End Date

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <input
                                type="date"
                                name="end_date"
                                value="<?= escape($endDate ?? '') ?>"
                                required
                            >

                        </div>

                    </div>



                    <!-- DURATION -->

                    <div class="form-group">

                        <label>

                            10. Estimated Duration

                            <span>*</span>

                        </label>

                        <div class="input-wrapper">

                            <select
                                name="duration"
                                required
                            >

                                <option value="">
                                    Select duration
                                </option>

                                <option value="1-2 Hours">
                                    1-2 Hours
                                </option>

                                <option value="3-4 Hours">
                                    3-4 Hours
                                </option>

                                <option value="5-6 Hours">
                                    5-6 Hours
                                </option>

                                <option value="7-8 Hours">
                                    7-8 Hours
                                </option>

                                <option value="Full Day">
                                    Full Day
                                </option>

                                <option value="Multiple Days">
                                    Multiple Days
                                </option>

                            </select>

                            <i class="fa-solid fa-chevron-down"></i>

                        </div>

                    </div>


                </div>

                

                <div class="form-group full-width">

                    <label>

                        11. Short Description / Assignment Summary

                        <span>*</span>

                    </label>

                    <textarea
                        name="description"
                        maxlength="500"
                        placeholder="Provide a brief summary of the assignment, tasks and expected outcomes..."
                        required
                    ><?= escape($description ?? '') ?></textarea>

                    <div class="character-limit">
                        Maximum 500 characters
                    </div>

                </div>

              

                <div class="form-group full-width">

                    <label>

                        12. Volunteer Requirements

                        <small>
                            (Select all that apply)
                        </small>

                        <span>*</span>

                    </label>


                    <div class="requirements-grid">


                        <label class="checkbox-item">

                            <input
                                type="checkbox"
                                name="volunteer_requirements[]"
                                value="Able to travel to location"
                                <?= in_array(
                                    "Able to travel to location",
                                    $requirements ?? []
                                ) ? "checked" : "" ?>
                            >

                            <span>
                                Able to travel to location
                            </span>

                        </label>



                        <label class="checkbox-item">

                            <input
                                type="checkbox"
                                name="volunteer_requirements[]"
                                value="Physically fit for field work"
                                <?= in_array(
                                    "Physically fit for field work",
                                    $requirements ?? []
                                ) ? "checked" : "" ?>
                            >

                            <span>
                                Physically fit for field work
                            </span>

                        </label>



                        <label class="checkbox-item">

                            <input
                                type="checkbox"
                                name="volunteer_requirements[]"
                                value="Can follow coordinator instructions"
                                <?= in_array(
                                    "Can follow coordinator instructions",
                                    $requirements ?? []
                                ) ? "checked" : "" ?>
                            >

                            <span>
                                Can follow coordinator instructions
                            </span>

                        </label>



                        <label class="checkbox-item">

                            <input
                                type="checkbox"
                                name="volunteer_requirements[]"
                                value="Available during assigned time"
                                <?= in_array(
                                    "Available during assigned time",
                                    $requirements ?? []
                                ) ? "checked" : "" ?>
                            >

                            <span>
                                Available during assigned time
                            </span>

                        </label>


                    </div>

                </div>

                

                <div class="form-group full-width">

                    <label>

                        13. Additional Notes

                        <small>
                            (Optional)
                        </small>

                    </label>

                    <textarea
                        name="additional_info"
                        maxlength="500"
                        placeholder="Add additional information, special instructions, or notes for volunteers..."
                    ><?= escape($additionalInfo ?? '') ?></textarea>

                </div>

                

                <div class="form-actions">

                    <a
                        href="manage_disasters.php"
                        class="cancel-button"
                    >
                        Cancel
                    </a>


                    <button
                        type="reset"
                        class="draft-button"
                    >

                        <i class="fa-regular fa-bookmark"></i>

                        Clear Form

                    </button>


                    <button
                        type="submit"
                        class="submit-button"
                    >

                        <i class="fa-solid fa-paper-plane"></i>

                        Create Assignment Request

                    </button>

                </div>


            </form>

        </section>

        

        <aside class="guidelines-card">


            <div class="guideline-header">

                <div class="guideline-header-icon">

                    <i class="fa-solid fa-circle-info"></i>

                </div>

                <div>

                    <h2>
                        Quick Guidelines
                    </h2>

                    <p>
                        Follow these tips to create an
                        effective assignment request
                    </p>

                </div>

            </div>



            <!-- GUIDELINE 1 -->

            <div class="guideline-item">

                <div class="guideline-icon">

                    <i class="fa-solid fa-pen"></i>

                </div>

                <div>

                    <h3>
                        Keep the title clear
                    </h3>

                    <p>
                        Use a short specific title so
                        volunteers understand the task.
                    </p>

                </div>

            </div>



            <!-- GUIDELINE 2 -->

            <div class="guideline-item">

                <div class="guideline-icon">

                    <i class="fa-solid fa-location-dot"></i>

                </div>

                <div>

                    <h3>
                        Choose the correct location
                    </h3>

                    <p>
                        Select the exact town/area where
                        support is needed.
                    </p>

                </div>

            </div>



            <!-- GUIDELINE 3 -->

            <div class="guideline-item">

                <div class="guideline-icon">

                    <i class="fa-solid fa-users"></i>

                </div>

                <div>

                    <h3>
                        Specify volunteers needed
                    </h3>

                    <p>
                        Enter an accurate number to help
                        mobilize the right people.
                    </p>

                </div>

            </div>



            <!-- GUIDELINE 4 -->

            <div class="guideline-item">

                <div class="guideline-icon">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

                <div>

                    <h3>
                        Set the right priority
                    </h3>

                    <p>
                        Choose the priority level based
                        on the urgency of the situation.
                    </p>

                </div>

            </div>



            <!-- GUIDELINE 5 -->

            <div class="guideline-item">

                <div class="guideline-icon">

                    <i class="fa-solid fa-file-lines"></i>

                </div>

                <div>

                    <h3>
                        Share essential details only
                    </h3>

                    <p>
                        Avoid unnecessary information.
                        Keep it short and actionable.
                    </p>

                </div>

            </div>



            <!-- HELP -->

            <div class="help-box">

                <div class="help-icon">

                    <i class="fa-regular fa-circle-question"></i>

                </div>

                <div>

                    <strong>
                        Need Help?
                    </strong>

                    <p>
                        Contact the district coordination
                        team for assistance.
                    </p>

                </div>

            </div>


        </aside>


    </div>


</main>

</div>


<?php include "../layouts/footer.php"; ?>


<script>


const disasterSelect =
    document.getElementById("disaster_id");

const locationSelect =
    document.getElementById("location");


if (disasterSelect && locationSelect) {

    disasterSelect.addEventListener("change", function () {

        const selectedOption =
            this.options[this.selectedIndex];

        const disasterLocation =
            selectedOption.getAttribute("data-location");

        if (disasterLocation) {

            locationSelect.value =
                disasterLocation;

        }

    });

}


const startDate =
    document.querySelector(
        'input[name="start_date"]'
    );


const applicationDeadline =
    document.querySelector(
        'input[name="application_deadline"]'
    );


const endDate =
    document.querySelector(
        'input[name="end_date"]'
    );


if (startDate && applicationDeadline && endDate) {


    // Start date controls the minimum end date

    startDate.addEventListener("change", function () {

        endDate.min = this.value;

    });


    // End date controls the maximum application deadline

    endDate.addEventListener("change", function () {

        applicationDeadline.max = this.value;

    });

}

</script>


</body>

</html>
