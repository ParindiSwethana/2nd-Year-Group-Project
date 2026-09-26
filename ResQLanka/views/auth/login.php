<?php
require_once("../../config/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResQ Lanka Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../loginstyles.css">
</head>

<body>

<div class="background"></div>

<div class="container">

    <header>
        <div class="logo-area">
            <img src="images/logo.png" alt="logo">
            <div>
                <h1>ResQ Lanka</h1>
                <p>Disaster & Crisis Management System</p>
            </div>
        </div>

        <div class="header-buttons">
            <a href="../fuel/search.php" class="fuel">
                <i class="fa-solid fa-gas-pump"></i>
                <div>
                    <span>CHECK FUEL</span>
                    <span>AVAILABILITY</span>
                </div>
            </a>

            <a href="../disaster/report_disaster.php" class="danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <span>INFORM ABOUT</span>
                    <span>DISASTER!</span>
                </div>
            </a>
        </div>

    </header>


    <main>

        <!-- LEFT PANEL -->
        <section class="login-card">
            <div class="welcome-icon">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <h2>
                JOINING HANDS TO MAKE
                <br>
                THE WORLD A BETTER PLACE
            </h2>
            <div class="divider"></div>

            <form action="../../controllers/AuthController.php" method="POST">
                <?php
                    if(isset($_GET["error"])){echo "<p style='color:red;'>Invalid Username or Password</p>";}
                ?>
                
                <label>Username</label>
                <div class="input-box">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="text" name="username" placeholder="Enter your username" required>
                </div>

                <label>Password</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" required>
                    <i class="fa-regular fa-eye"></i>
                </div>

                <button type="submit" class="signin">
                    <i class="fa-solid fa-lock"></i>
                    SIGN IN
                </button>

                <a href="#" class="forgot">
                    Forgot password?
                </a>
            </form>

            <div class="register-box">
                <div class="register-left">
                    <div class="circle">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>
                        <h4>New here?</h4>
                        <p>Create an account to get started.</p>
                    </div>
                </div>

                <a href="register.php" class="register-btn">
                    REGISTER
                </a>
            </div>
        </section>

        <!-- RIGHT PANEL -->
        <section class="assignment-card">
            <h2>ONGOING ASSIGNMENTS</h2>

            <!-- Assignment 1 -->
            <div class="assignment">
                <div class="number blue">
                    1
                </div>
                <div class="icon blue-bg">
                    <i class="fa-solid fa-tree"></i>
                </div>

                <div class="content">
                    <h3> BEACH CLEAN-UP <br> IN MOUNT LAVINIA </h3>
                    <p>
                        <i class="fa-regular fa-calendar"></i>

                        DATE:
                        <span>25TH AUGUST 2026</span>
                    </p>
                    <p>
                        <i class="fa-solid fa-users"></i>

                        <span>100</span>

                        VOLUNTEERS NEEDED
                    </p>
                </div>

                <div class="status">
                    ACTIVE
                </div>
                <i class="fa-solid fa-chevron-right arrow"></i>
            </div>

            <!-- Assignment 2 -->
            <div class="assignment">
                <div class="number red">
                    2
                </div>
                <div class="icon red-bg">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div class="content">
                    <h3>
                        CITY CLEAN-UP
                        <br>
                        IN COLOMBO 06
                    </h3>
                    <p>
                        <i class="fa-regular fa-calendar"></i>
                        DATE:
                        <span class="red-text">
                            31ST AUGUST 2026
                        </span>
                    </p>
                    <p>
                        <i class="fa-solid fa-users"></i>
                        <span class="red-text">
                            50
                        </span>
                        VOLUNTEERS NEEDED
                    </p>
                </div>

                <div class="status">
                    ACTIVE
                </div>

                <i class="fa-solid fa-chevron-right arrow"></i>
            </div>

            <!-- Bottom Banner -->
            <div class="info-box">
                <div class="shield">
                    <i class="fa-solid fa-shield"></i>
                </div>

                <div>
                    <h3>Every action counts.</h3>
                    <p>
                        Stay informed.
                        Stay prepared.
                        Stay safe.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div>
            <i class="fa-solid fa-shield"></i>
            Building safer communities through preparedness,
            response and resilience.
        </div>
        <div>
            © 2026 ResQ Lanka. All rights reserved.
        </div>
    </footer>

</div>
</body>
</html>
