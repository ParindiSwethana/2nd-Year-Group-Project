<aside class="sidebar">
    <nav class="sidebar-nav">
        <a href="../dashboard/super_dashboard.php" class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-border-all"></i></span>
            <span>Dashboard</span>
        </a>
        <a href="../disaster/manage_disasters.php" class="nav-item <?= ($activePage ?? '') === 'disasters' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-bell"></i></span>
            <span>Manage Disasters</span>
        </a>
        <a href="../fuel/manage_stations.php" class="nav-item <?= ($activePage ?? '') === 'fuel' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-gas-pump"></i></span>
            <span>Manage Fuel Stations</span>
        </a>
        <a href="../inventory/inventory_list.php" class="nav-item <?= ($activePage ?? '') === 'inventory' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <span>Manage Inventory</span>
        </a>
    </nav>

    <div class="sidebar-volunteer-card">
        <div class="sidebar-volunteer-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <h3>National Overview</h3>
        <p>Coordinate disaster response and resources across Sri Lanka.</p>
    </div>

    <a href="../../controllers/AuthController.php?action=logout" class="logout-button">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
        <span>Log Out</span>
    </a>
</aside>
