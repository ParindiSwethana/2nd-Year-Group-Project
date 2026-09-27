<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../config/database.php";


$fullName = $_SESSION["name"] ?? "Volunteer";
$tier = $_SESSION["tier"] ?? "Bronze";
$points = $_SESSION["points"] ?? 0;
$userId = $_SESSION["user_id"] ?? null;


function escape($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}




$disasterId = filter_input(
    INPUT_GET,
    "disaster_id",
    FILTER_VALIDATE_INT
);




if (!$disasterId) {

    header("Location: active_disasters.php");
    exit;
}


$assignment = null;
$errors = [];
$success = false;
$pageError = "";


try {

    $database = new Database();
    $conn = $database->connect();


    

    $sql = "

        SELECT

            /* -------------------------
               DISASTER DATA
               ------------------------- */

            d.disaster_id,
            d.title AS disaster_title,
            d.disaster_type,
            d.description AS disaster_description,
            d.location AS disaster_location,
            d.district_id,
            d.priority,
            d.status AS disaster_status,
            d.created_at AS disaster_created_at,
            d.resolved_at,
            d.duration AS disaster_duration,


            /* -------------------------
               ASSIGNMENT DATA
               ------------------------- */

            va.assignment_id,
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
            va.created_at AS assignment_created_at,
            va.created_by


        FROM DISASTER d

        LEFT JOIN VOLUNTEER_ASSIGNMENT va
            ON d.disaster_id = va.disaster_id

        WHERE d.disaster_id = :disaster_id

        ORDER BY va.created_at DESC

        LIMIT 1
    ";


    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":disaster_id" => $disasterId
    ]);


    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    

    if (!$assignment) {

        $pageError = "The selected disaster could not be found.";

    }


    

    elseif (empty($assignment["assignment_id"])) {

        $pageError =
            "There is currently no volunteer assignment available for this disaster.";

    }


   

    if (
        empty($pageError) &&
        $_SERVER["REQUEST_METHOD"] === "POST"
    ) {


       

        $contactNumber = trim(
            $_POST["contact_number"] ?? ""
        );

        $availability = trim(
            $_POST["availability"] ?? ""
        );

        $canTravel = trim(
            $_POST["can_travel"] ?? ""
        );

        $comfortableWithFuel = trim(
            $_POST["comfortable_with_fuel"] ?? ""
        );


        

        $skills = $_POST["skills_experience"] ?? [];


        if (!is_array($skills)) {

            $skills = [$skills];

        }


        $skills = array_values(
            array_filter(
                array_map("trim", $skills)
            )
        );


        $skillsExperience = implode(
            ", ",
            $skills
        );


        

        $additionalNote = trim(
            $_POST["additional_note"] ?? ""
        );


       

        $agreedToTerms =
            isset($_POST["agreed_to_terms"])
            ? 1
            : 0;


        
        
        if ($contactNumber === "") {

            $errors[] =
                "Please enter your contact number.";

        }


        if ($availability === "") {

            $errors[] =
                "Please select your availability.";

        }


        if ($canTravel === "") {

            $errors[] =
                "Please select whether you can travel to this location.";

        }


        if ($comfortableWithFuel === "") {

            $errors[] =
                "Please select whether you are comfortable with fuel-related conditions.";

        }


        if ($skillsExperience === "") {

            $errors[] =
                "Please select at least one relevant skill or experience.";

        }


        if ($agreedToTerms !== 1) {

            $errors[] =
                "You must agree to follow the coordinator instructions.";

        }


                

        if (empty($errors)) {


           
            $insertSql = "

                INSERT INTO VOLUNTEER_APPLICATION
                (
                    assignment_id,
                    user_id,
                    contact_number,
                    availability,
                    can_travel,
                    comfortable_with_fuel,
                    skills_experience,
                    additional_note,
                    agreed_to_terms,
                    status
                )

                VALUES
                (
                    :assignment_id,
                    :user_id,
                    :contact_number,
                    :availability,
                    :can_travel,
                    :comfortable_with_fuel,
                    :skills_experience,
                    :additional_note,
                    :agreed_to_terms,
                    'Pending'
                )

            ";


            $insertStmt = $conn->prepare(
                $insertSql
            );


            $insertStmt->execute([

                ":assignment_id"
                    => $assignment["assignment_id"],

                ":user_id"
                    => $userId,

                ":contact_number"
                    => $contactNumber,

                ":availability"
                    => $availability,

                ":can_travel"
                    => $canTravel,

                ":comfortable_with_fuel"
                    => $comfortableWithFuel,

                ":skills_experience"
                    => $skillsExperience,

                ":additional_note"
                    => $additionalNote !== ""
                        ? $additionalNote
                        : null,

                ":agreed_to_terms"
                    => $agreedToTerms

            ]);


            $success = true;
        }
    }


} catch (PDOException $e) {

    

    $pageError =
        "Unable to load this assignment. Please try again later.";

   
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

    <title>

        <?= isset($assignment["assignment_title"])
            ? escape($assignment["assignment_title"])
            : "Apply Assignment"
        ?>

        | ResQ Lanka

    </title>


    <!-- PAGE CSS -->

    <link
        rel="stylesheet"
        href="../../css/apply_assignment.css"
    >

</head>


<body>




<?php include "../layouts/header.php"; ?>

<?php include "../layouts/navbar.php"; ?>



<div class="app-layout">

    

    <?php include "../layouts/sidebar.php"; ?>


   
    <main class="main-content">


        <?php if ($pageError !== ""): ?>

            

            <section class="error-page">

                <div class="error-page-icon">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>


                <h2>
                    Assignment Not Available
                </h2>


                <p>
                    <?= escape($pageError) ?>
                </p>


                <a
                    href="active_disasters.php"
                    class="back-button"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Active Disasters

                </a>

            </section>


        <?php else: ?>

            

            <div class="breadcrumb">

                <a href="active_disasters.php">

                    Active Disasters

                </a>


                <i class="fa-solid fa-chevron-right"></i>


                <span>

                    Disaster Details

                </span>

            </div>

            

            <?php if ($success): ?>

                <section class="success-card">


                    <div class="success-icon">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>


                    <div>

                        <h2>
                            Application Submitted Successfully
                        </h2>


                        <p>

                            Your application has been saved and its
                            current status is

                            <strong>
                                Pending
                            </strong>.

                        </p>

                    </div>


                    <a
                        href="active_disasters.php"
                        class="back-button"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Back to Active Disasters

                    </a>


                </section>

            <?php endif; ?>

            
            <?php if (!empty($errors)): ?>

                <div class="message error-message">


                    <i class="fa-solid fa-circle-exclamation"></i>


                    <div>

                        <strong>
                            Please correct the following:
                        </strong>


                        <ul>

                            <?php foreach ($errors as $error): ?>

                                <li>

                                    <?= escape($error) ?>

                                </li>

                            <?php endforeach; ?>

                        </ul>

                    </div>

                </div>

            <?php endif; ?>


           
            <section class="detail-heading">


                <div>

                    <span class="section-label">

                        ACTIVE DISASTER

                    </span>


                    <h2>

                        <?= escape(
                            $assignment["disaster_title"]
                        ) ?>

                    </h2>


                    <p>

                        <?= escape(
                            $assignment["disaster_type"]
                        ) ?>

                        &nbsp;•&nbsp;

                        <?= escape(
                            $assignment["disaster_location"]
                        ) ?>

                    </p>

                </div>


                <!-- ASSIGNMENT SEVERITY -->

                <span class="severity-badge">

                    <?= escape(
                        strtoupper(
                            $assignment["severity"]
                        )
                    ) ?>

                </span>


            </section>


           

            <div class="details-layout">


                
                <section class="assignment-details-card">

                    

                    <div class="disaster-summary">


                        <div class="summary-icon">

                            <i class="fa-solid fa-tower-broadcast"></i>

                        </div>


                        <div>

                            <span class="summary-label">

                                DISASTER

                            </span>


                            <h3>

                                <?= escape(
                                    $assignment["disaster_title"]
                                ) ?>

                            </h3>


                            <p>

                                <i class="fa-solid fa-location-dot"></i>

                                <?= escape(
                                    $assignment["disaster_location"]
                                ) ?>

                            </p>

                        </div>

                    </div>


                    
                    <div class="info-grid">


                        <!-- LOCATION -->

                        <div class="info-box">

                            <i class="fa-solid fa-location-dot"></i>


                            <div>

                                <span>
                                    Location
                                </span>


                                <strong>

                                    <?= escape(
                                        $assignment["assignment_location"]
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                        <!-- VOLUNTEERS -->

                        <div class="info-box">

                            <i class="fa-solid fa-users"></i>


                            <div>

                                <span>
                                    Volunteers Needed
                                </span>


                                <strong>

                                    <?= escape(
                                        $assignment["volunteers_needed"]
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                        <!-- PRIORITY -->

                        <div class="info-box">

                            <i class="fa-solid fa-triangle-exclamation"></i>


                            <div>

                                <span>
                                    Priority
                                </span>


                                <strong>

                                    <?= escape(
                                        $assignment["priority"]
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                        <!-- START DATE -->

                        <div class="info-box">

                            <i class="fa-regular fa-calendar"></i>


                            <div>

                                <span>
                                    Start Date
                                </span>


                                <strong>

                                    <?= escape(
                                        date(
                                            "M d, Y",
                                            strtotime(
                                                $assignment["start_date"]
                                            )
                                        )
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                        <!-- DURATION -->

                        <div class="info-box">

                            <i class="fa-regular fa-clock"></i>


                            <div>

                                <span>
                                    Duration
                                </span>


                                <strong>

                                    <?= escape(
                                        $assignment["assignment_duration"]
                                        ?: $assignment["disaster_duration"]
                                        ?: "Not specified"
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                        <!-- END DATE -->

                        <div class="info-box">

                            <i class="fa-solid fa-calendar-check"></i>


                            <div>

                                <span>
                                    End Date
                                </span>


                                <strong>

                                    <?= escape(
                                        date(
                                            "M d, Y",
                                            strtotime(
                                                $assignment["end_date"]
                                            )
                                        )
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                    </div>

                 

                    <div class="content-section">


                        <h3>

                            <i class="fa-regular fa-clipboard"></i>

                            <?= escape(
                                $assignment["assignment_title"]
                            ) ?>

                        </h3>


                        <p>

                            <?= nl2br(
                                escape(
                                    $assignment[
                                        "assignment_description"
                                    ]
                                )
                            ) ?>

                        </p>

                    </div>


                    
                    <div class="content-section">


                        <h3>

                            <i class="fa-solid fa-circle-info"></i>

                            Disaster Information

                        </h3>


                        <p>

                            <?= nl2br(
                                escape(
                                    $assignment[
                                        "disaster_description"
                                    ]
                                )
                            ) ?>

                        </p>

                    </div>


                   

                    <?php if (
                        !empty(
                            $assignment["additional_info"]
                        )
                    ): ?>

                        <div class="content-section">


                            <h3>

                                <i class="fa-solid fa-circle-info"></i>

                                Additional Information

                            </h3>


                            <p>

                                <?= nl2br(
                                    escape(
                                        $assignment[
                                            "additional_info"
                                        ]
                                    )
                                ) ?>

                            </p>


                        </div>

                    <?php endif; ?>


                    

                    <div class="content-section">


                        <h3>

                            <i class="fa-solid fa-shield-halved"></i>

                            Requirements

                        </h3>


                        <p>

                            <?= nl2br(
                                escape(
                                    $assignment[
                                        "volunteer_requirements"
                                    ]
                                )
                            ) ?>

                        </p>


                    </div>


                </section>


                

                <section class="application-card">


                    

                    <div class="application-heading">


                        <div class="form-icon">

                            <i class="fa-regular fa-user"></i>

                        </div>


                        <div>

                            <h2>

                                Apply for This Assignment

                            </h2>


                            <p>

                                Your registered profile details
                                will be used automatically.

                            </p>

                        </div>

                    </div>


                    <?php if (!$success): ?>


                       
                        <form
                            method="POST"
                            action="apply_assignment.php?disaster_id=<?= (int)$disasterId ?>"
                        >


                            
                            <div class="form-group">


                                <label for="contact_number">

                                    Contact Number

                                </label>


                                <input
                                    type="tel"
                                    id="contact_number"
                                    name="contact_number"
                                    placeholder="+94 77 123 4567"
                                    value="<?= escape(
                                        $_POST[
                                            "contact_number"
                                        ] ?? ""
                                    ) ?>"
                                    required
                                >

                            </div>


                            
                            <div class="form-group">


                                <label for="availability">

                                    Availability

                                </label>


                                <select
                                    id="availability"
                                    name="availability"
                                    required
                                >


                                    <option value="">

                                        Select your availability

                                    </option>


                                    <option
                                        value="Full Day"
                                        <?= (
                                            ($_POST[
                                                "availability"
                                            ] ?? "")
                                            === "Full Day"
                                        )
                                            ? "selected"
                                            : ""
                                        ?>
                                    >

                                        Full Day

                                    </option>


                                    <option
                                        value="Morning"
                                        <?= (
                                            ($_POST[
                                                "availability"
                                            ] ?? "")
                                            === "Morning"
                                        )
                                            ? "selected"
                                            : ""
                                        ?>
                                    >

                                        Morning

                                    </option>


                                    <option
                                        value="Afternoon"
                                        <?= (
                                            ($_POST[
                                                "availability"
                                            ] ?? "")
                                            === "Afternoon"
                                        )
                                            ? "selected"
                                            : ""
                                        ?>
                                    >

                                        Afternoon

                                    </option>


                                    <option
                                        value="Evening"
                                        <?= (
                                            ($_POST[
                                                "availability"
                                            ] ?? "")
                                            === "Evening"
                                        )
                                            ? "selected"
                                            : ""
                                        ?>
                                    >

                                        Evening

                                    </option>


                                </select>

                            </div>

                            

                            <div class="form-row">


                                <!-- CAN TRAVEL -->

                                <div class="form-group">


                                    <label for="can_travel">

                                        Can you travel to this location?

                                    </label>


                                    <select
                                        id="can_travel"
                                        name="can_travel"
                                        required
                                    >


                                        <option value="">

                                            Select an option

                                        </option>


                                        <option
                                            value="Yes"
                                            <?= (
                                                ($_POST[
                                                    "can_travel"
                                                ] ?? "")
                                                === "Yes"
                                            )
                                                ? "selected"
                                                : ""
                                            ?>
                                        >

                                            Yes

                                        </option>


                                        <option
                                            value="No"
                                            <?= (
                                                ($_POST[
                                                    "can_travel"
                                                ] ?? "")
                                                === "No"
                                            )
                                                ? "selected"
                                                : ""
                                            ?>
                                        >

                                            No

                                        </option>


                                    </select>

                                </div>


                                <!-- FUEL -->

                                <div class="form-group">


                                    <label
                                        for="comfortable_with_fuel"
                                    >

                                        Are you comfortable
                                        with fuel conditions?

                                    </label>


                                    <select
                                        id="comfortable_with_fuel"
                                        name="comfortable_with_fuel"
                                        required
                                    >


                                        <option value="">

                                            Select an option

                                        </option>


                                        <option
                                            value="Yes"
                                            <?= (
                                                ($_POST[
                                                    "comfortable_with_fuel"
                                                ] ?? "")
                                                === "Yes"
                                            )
                                                ? "selected"
                                                : ""
                                            ?>
                                        >

                                            Yes

                                        </option>


                                        <option
                                            value="No"
                                            <?= (
                                                ($_POST[
                                                    "comfortable_with_fuel"
                                                ] ?? "")
                                                === "No"
                                            )
                                                ? "selected"
                                                : ""
                                            ?>
                                        >

                                            No

                                        </option>


                                    </select>

                                </div>


                            </div>


                           

                            <div class="form-group">


                                <label>

                                    Relevant Skills / Experiences

                                </label>


                                <?php

                                $selectedSkills =
                                    $_POST[
                                        "skills_experience"
                                    ] ?? [];


                                if (
                                    !is_array(
                                        $selectedSkills
                                    )
                                ) {

                                    $selectedSkills =
                                        [$selectedSkills];

                                }

                                ?>


                                <div class="checkbox-grid">


                                    <!-- FIRST AID -->

                                    <label class="check-option">


                                        <input
                                            type="checkbox"
                                            name="skills_experience[]"
                                            value="First Aid"

                                            <?= in_array(
                                                "First Aid",
                                                $selectedSkills,
                                                true
                                            )
                                                ? "checked"
                                                : ""
                                            ?>
                                        >


                                        <span>
                                            First Aid
                                        </span>


                                    </label>


                                    <!-- MEDICAL SUPPORT -->

                                    <label class="check-option">


                                        <input
                                            type="checkbox"
                                            name="skills_experience[]"
                                            value="Medical Support"

                                            <?= in_array(
                                                "Medical Support",
                                                $selectedSkills,
                                                true
                                            )
                                                ? "checked"
                                                : ""
                                            ?>
                                        >


                                        <span>
                                            Medical Support
                                        </span>


                                    </label>


                                    <!-- FIELD SUPPORT -->

                                    <label class="check-option">


                                        <input
                                            type="checkbox"
                                            name="skills_experience[]"
                                            value="Field Support"

                                            <?= in_array(
                                                "Field Support",
                                                $selectedSkills,
                                                true
                                            )
                                                ? "checked"
                                                : ""
                                            ?>
                                        >


                                        <span>
                                            Field Support
                                        </span>


                                    </label>


                                   

                                    <label class="check-option">


                                        <input
                                            type="checkbox"
                                            name="skills_experience[]"
                                            value="Driving"

                                            <?= in_array(
                                                "Driving",
                                                $selectedSkills,
                                                true
                                            )
                                                ? "checked"
                                                : ""
                                            ?>
                                        >


                                        <span>
                                            Driving
                                        </span>


                                    </label>


                                    

                                    <label class="check-option">


                                        <input
                                            type="checkbox"
                                            name="skills_experience[]"
                                            value="Distribution"

                                            <?= in_array(
                                                "Distribution",
                                                $selectedSkills,
                                                true
                                            )
                                                ? "checked"
                                                : ""
                                            ?>
                                        >


                                        <span>
                                            Distribution
                                        </span>


                                    </label>


                                   
                                    <label class="check-option">


                                        <input
                                            type="checkbox"
                                            name="skills_experience[]"
                                            value="Community Support"

                                            <?= in_array(
                                                "Community Support",
                                                $selectedSkills,
                                                true
                                            )
                                                ? "checked"
                                                : ""
                                            ?>
                                        >


                                        <span>
                                            Community Support
                                        </span>


                                    </label>


                                </div>

                            </div>


                            

                            <div class="form-group">


                                <label for="additional_note">

                                    Additional Note

                                    <span>
                                        (Optional)
                                    </span>

                                </label>


                                <textarea
                                    id="additional_note"
                                    name="additional_note"
                                    rows="4"
                                    placeholder="Write any additional information..."
                                ><?= escape(
                                    $_POST[
                                        "additional_note"
                                    ] ?? ""
                                ) ?></textarea>


                            </div>


                            

                            <label class="terms-option">


                                <input
                                    type="checkbox"
                                    name="agreed_to_terms"
                                    value="1"

                                    <?= isset(
                                        $_POST[
                                            "agreed_to_terms"
                                        ]
                                    )
                                        ? "checked"
                                        : ""
                                    ?>
                                >


                                <span>

                                    I confirm that I am willing
                                    to follow instructions from
                                    coordinators and work as part
                                    of a team.

                                </span>


                            </label>


                            

                            <button
                                type="submit"
                                class="apply-button"
                            >


                                <i class="fa-solid fa-paper-plane"></i>


                                Apply for Assignment


                            </button>


                        </form>


                    <?php endif; ?>


                </section>


            </div>


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


    </main>


</div>




<?php include "../layouts/footer.php"; ?>


</body>

</html>
