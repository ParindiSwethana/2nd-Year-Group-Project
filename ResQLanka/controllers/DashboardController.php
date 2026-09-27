<?php
require_once __DIR__ . "/../config/session.php";

class DashboardController
{
    public function userDashboard(): void
    {
        requireRole("registered_user");
        require __DIR__ . "/../views/dashboard/user_dashboard.php";
    }

    public function districtDashboard(): void
    {
        requireRole("district_admin");
        require __DIR__ . "/../views/dashboard/district_dashboard.php";
    }

    public function superDashboard(): void
    {
        requireRole("super_admin");
        require __DIR__ . "/../views/dashboard/super_dashboard.php";
    }
}
?>
