<aside class="sidebar">

    <nav class="sidebar-nav">

        <a
            href="../dashboard/user_dashboard.php"
            class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>"
        >

            <span class="nav-icon">
                <i class="fa-solid fa-border-all"></i>
            </span>

            <span>Dashboard</span>

        </a>

        <a
            href="../disaster/active_disasters.php"
            class="nav-item <?= ($activePage ?? '') === 'disasters' ? 'active' : '' ?>"
        >

            <span class="nav-icon">
                <i class="fa-solid fa-tower-broadcast"></i>
            </span>

            <span>Active Disasters</span>

        </a>

        <a
            href="../disaster/volunteer_assignments.php"
            class="nav-item <?= ($activePage ?? '') === 'assignments' ? 'active' : '' ?>"
        >

            <span class="nav-icon">
                <i class="fa-regular fa-file-lines"></i>
            </span>

            <span>Volunteer Assignments</span>

        </a>

        <a
            href="../international/international_programs.php"
            class="nav-item <?= ($activePage ?? '') === 'international' ? 'active' : '' ?>"
        >

            <span class="nav-icon">
                <i class="fa-solid fa-earth-asia"></i>
            </span>

            <span>International Volunteering</span>

        </a>

    </nav>

    <div class="sidebar-volunteer-card">

        <div class="sidebar-volunteer-icon">
            <i class="fa-solid fa-hand-holding-heart"></i>
        </div>

        <h3>Make an Impact</h3>

        <p>
            Every hour you volunteer helps build
            stronger and safer communities.
        </p>

    </div>

    <a
        href="../../controllers/AuthController.php?action=logout"
        class="logout-button"
    >

        <i class="fa-solid fa-arrow-right-from-bracket"></i>

        <span>Log Out</span>

    </a>

</aside>
