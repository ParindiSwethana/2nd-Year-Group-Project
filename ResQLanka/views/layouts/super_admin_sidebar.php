<aside class="sidebar">
    <nav class="sidebar-nav">
        <a
            href="../dashboard/super_dashboard.php"
            class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>"
        >
            <span class="nav-icon">
                <i class="fa-solid fa-border-all"></i>
            </span>
            <span>Dashboard</span>
        </a>

        <a
            href="../users/manage_disctict_admins.php"
            class="nav-item <?= ($activePage ?? '') === 'district_admins' ? 'active' : '' ?>"
        >
            <span class="nav-icon">
                <i class="fa-solid fa-user-shield"></i>
            </span>
            <span>District Administrators</span>
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
