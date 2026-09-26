<aside class="sidebar">

    <nav class="sidebar-nav">

        <a
            href="../dashboard/district_dashboard.php"
            class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>"
        >

            <span class="nav-icon">
                <i class="fa-solid fa-border-all"></i>
            </span>

            <span>Dashboard</span>

        </a>

        <a
            href="../disaster/manage_disasters.php"
            class="nav-item <?= ($activePage ?? '') === 'disasters' ? 'active' : '' ?>"
        >

            <span class="nav-icon">
                <i class="fa-solid fa-bell"></i>
            </span>

            <span>Manage Disasters</span>

        </a>

        <a
            href="../users/manage_users.php"
            class="nav-item <?= ($activePage ?? '') === 'volunteers' ? 'active' : '' ?>"
        >

            <span class="nav-icon"><i class="fa-solid fa-user-group"></i></span>
            <span>Manage Volunteers</span>

        </a>

        <a
            href="../inventory/inventory_list.php"
            class="nav-item <?= ($activePage ?? '') === 'inventory' ? 'active' : '' ?>"
        >

            <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <span>Manage Inventory</span>

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
