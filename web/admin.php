<?php
/**
 * iPark Admin Portal
 * Consolidated admin dashboard with routing
 * Handles: dashboard, reports, staff management, user management
 */

require_once(__DIR__ . '/config/db.php');
require_once(__DIR__ . '/includes/auth.php');
require_once(__DIR__ . '/Models/User.php');
require_once(__DIR__ . '/Models/Admin.php');
require_once(__DIR__ . '/Models/Report.php');

requireAdmin();

$admin_id = getCurrentAdminId();
$admin_model = new Admin($conn);
$user_model = new User($conn);
$report_model = new Report($conn);
$admin = $admin_model->getAdminById($admin_id);

// Determine page from query string
$page = $_GET['page'] ?? 'dashboard';
$page_title = 'Admin Portal';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');
    
    $action = $_POST['action'] ?? '';
    
    if ($page === 'staff' && $action === 'create') {
        requireAdminRole('super_admin');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $role = $_POST['role'] ?? 'operator';
        
        if (!$username || !$email || !$password || !$full_name) {
            $_SESSION['flash_message'] = ['error' => 'All fields are required'];
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_message'] = ['error' => 'Invalid email format'];
        } else if (strlen($password) < 8) {
            $_SESSION['flash_message'] = ['error' => 'Password must be at least 8 characters'];
        } else {
            $password_hash = hashPassword($password);
            if ($admin_model->createAdmin($username, $email, $password_hash, $full_name, $role)) {
                $_SESSION['flash_message'] = ['success' => 'Staff member created successfully'];
            } else {
                $_SESSION['flash_message'] = ['error' => 'Failed to create staff member'];
            }
        }
        header('Location: /admin.php?page=staff');
        exit();
    } 
    elseif ($page === 'staff' && $action === 'update_role') {
        requireAdminRole('super_admin');
        $staff_id = intval($_POST['staff_id'] ?? 0);
        $role = $_POST['role'] ?? 'operator';
        
        if ($admin_model->updateAdminRole($staff_id, $role)) {
            $_SESSION['flash_message'] = ['success' => 'Staff role updated'];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to update role'];
        }
        header('Location: /admin.php?page=staff');
        exit();
    }
    elseif ($page === 'staff' && $action === 'deactivate') {
        requireAdminRole('super_admin');
        $staff_id = intval($_POST['staff_id'] ?? 0);
        if ($admin_model->deactivateAdmin($staff_id)) {
            $_SESSION['flash_message'] = ['success' => 'Staff member deactivated'];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to deactivate'];
        }
        header('Location: /admin.php?page=staff');
        exit();
    }
    elseif ($page === 'users' && $action === 'deactivate') {
        requireAdminRole('admin', 'super_admin');
        $user_id = intval($_POST['user_id'] ?? 0);
        
        if ($user_model->deactivateUser($user_id)) {
            $_SESSION['flash_message'] = ['success' => 'User deactivated'];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to deactivate user'];
        }
        header('Location: /admin.php?page=users');
        exit();
    }
}

// Load data based on page
if ($page === 'dashboard') {
    $kpi_query = "
        SELECT 
            (SELECT COUNT(*) FROM ipark_reservations WHERE DATE(created_at) = CURDATE()) as today_reservations,
            (SELECT SUM(total_amount) FROM ipark_reservations WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid') as today_revenue,
            (SELECT COUNT(*) FROM ipark_parking_slots WHERE status = 'occupied') as occupied_slots,
            (SELECT COUNT(*) FROM ipark_parking_slots) as total_slots,
            (SELECT COUNT(*) FROM ipark_users WHERE is_active = TRUE) as active_users,
            (SELECT COUNT(*) FROM ipark_reservations WHERE reservation_status = 'pending_approval') as pending_approvals
    ";
    $kpi_result = $conn->query($kpi_query);
    $kpi_data = $kpi_result->fetch_assoc();
    $occupancy_percent = ($kpi_data['total_slots'] > 0) ? 
        round(($kpi_data['occupied_slots'] / $kpi_data['total_slots']) * 100, 1) : 0;
    
    $reservations_query = "
        SELECT r.id, r.created_at, u.full_name, s.slot_number, s.parking_lot, r.total_amount, r.reservation_status
        FROM ipark_reservations r
        JOIN ipark_users u ON r.user_id = u.id
        JOIN ipark_parking_slots s ON r.parking_slot_id = s.id
        ORDER BY r.created_at DESC
        LIMIT 10
    ";
    $reservations_result = $conn->query($reservations_query);
    $recent_reservations = [];
    while ($row = $reservations_result->fetch_assoc()) {
        $recent_reservations[] = $row;
    }
}
elseif ($page === 'reports') {
    requireAdminRole('admin', 'super_admin');
    $kpis = $report_model->getDashboardKPIs();
    $revenue = $report_model->getRevenueReport();
    $top_lots = $report_model->getTopParkingLots();
    $user_activity = $report_model->getUserActivityReport(10);
    $total_revenue = $report_model->getTotalRevenue();
}
elseif ($page === 'staff') {
    requireAdminRole('super_admin');
    $staff_list = $admin_model->getAllAdmins(100, 0);
}
elseif ($page === 'users') {
    requireAdminRole('admin', 'super_admin');
    $page_num = intval($_GET['page_num'] ?? 1);
    $limit = 20;
    $offset = ($page_num - 1) * $limit;
    $users = $user_model->getAllUsers($limit, $offset);
    $total_users = $user_model->getTotalUserCount();
    $total_pages = ceil($total_users / $limit);
}

?>
<?php require_once(__DIR__ . '/includes/header.php'); ?>

<div class="flex min-h-screen gap-0">

    <!-- Sidebar -->
    <aside class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col sticky h-screen">

        <!-- Logo -->
        <div class="p-6 flex items-center gap-3">
            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined">local_parking</span>
            </div>
            <div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white"><?php echo SITE_NAME; ?> Admin</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Control Center</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 space-y-1 mt-4">
            <a class="flex items-center gap-3 px-3 py-2.5 <?php echo $page === 'dashboard' ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?> rounded-lg font-medium transition-colors" href="?page=dashboard">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 <?php echo $page === 'users' ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?> rounded-lg font-medium transition-colors" href="?page=users">
                <span class="material-symbols-outlined text-xl">group</span>
                <span>User Management</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 <?php echo $page === 'staff' ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?> rounded-lg font-medium transition-colors" href="?page=staff">
                <span class="material-symbols-outlined text-xl">badge</span>
                <span>Staff Management</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 <?php echo $page === 'reports' ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?> rounded-lg font-medium transition-colors" href="?page=reports">
                <span class="material-symbols-outlined text-xl">description</span>
                <span>Reports</span>
            </a>
        </nav>

        <!-- User Profile -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 p-2">
                <div class="size-8 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                    <?php echo substr($_SESSION['admin_name'] ?? 'A', 0, 1); ?>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-semibold truncate text-slate-900 dark:text-white"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></p>
                    <p class="text-xs text-slate-500 truncate"><?php echo ucfirst($_SESSION['admin_role'] ?? 'admin'); ?></p>
                </div>
                <a href="<?php echo SITE_URL; ?>/logout.php" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-lg">logout</span>
                </a>
            </div>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">

        <!-- DASHBOARD PAGE -->
        <?php if ($page === 'dashboard'): ?>
        
        <!-- Header -->
        <header class="sticky top-0 z-10 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md px-8 py-6 flex justify-between items-center border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Dashboard Overview</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>. Here's what's happening today.</p>
            </div>
        </header>

        <!-- Content -->
        <div class="p-8">

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                        <span class="text-xs font-bold text-secondary flex items-center">
                            <span class="material-symbols-outlined text-sm">trending_up</span> 12.5%
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Today's Revenue</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">$<?php echo number_format($kpi_data['today_revenue'] ?? 0, 2); ?></h3>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-secondary/10 text-secondary rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined">directions_car</span>
                        </div>
                        <span class="text-xs font-bold text-secondary flex items-center">
                            <span class="material-symbols-outlined text-sm">trending_up</span> 5.2%
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Overall Occupancy</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1"><?php echo $occupancy_percent; ?>%</h3>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <span class="text-xs font-bold text-secondary flex items-center">
                            <span class="material-symbols-outlined text-sm">trending_up</span> 8.1%
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Active Users</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1"><?php echo number_format($kpi_data['active_users']); ?></h3>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined">pending_actions</span>
                        </div>
                        <span class="text-xs font-bold text-red-500 flex items-center">
                            <span class="material-symbols-outlined text-sm">trending_down</span> 2.4%
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Pending Approvals</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1"><?php echo intval($kpi_data['pending_approvals']); ?></h3>
                </div>

            </div>

            <!-- Recent Reservations Table -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">

                <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 dark:text-white">Recent Reservations</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">User</th>
                                <th class="px-6 py-4 text-left">Slot</th>
                                <th class="px-6 py-4 text-left">Location</th>
                                <th class="px-6 py-4 text-left">Amount</th>
                                <th class="px-6 py-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            <?php foreach ($recent_reservations as $res): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">
                                                <?php echo strtoupper(substr($res['full_name'], 0, 1)); ?>
                                            </div>
                                            <span class="font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($res['full_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($res['slot_number']); ?></td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($res['parking_lot']); ?></td>
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">$<?php echo number_format($res['total_amount'] ?? 0, 2); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                                            <?php 
                                                if ($res['reservation_status'] === 'approved') echo 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
                                                elseif ($res['reservation_status'] === 'pending_approval') echo 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400';
                                                else echo 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300';
                                            ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $res['reservation_status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        <!-- REPORTS PAGE -->
        <?php elseif ($page === 'reports'): ?>

        <div class="max-w-[1400px] mx-auto px-4 md:px-10 lg:px-40 py-10">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Reports & Analytics</h1>
            <p class="text-slate-600 dark:text-slate-400 mb-8">Comprehensive business metrics and insights</p>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Revenue</p>
                    <h3 class="text-3xl font-bold text-primary">${<?php echo number_format($total_revenue, 2); ?></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">All time</p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Today's Revenue</p>
                    <h3 class="text-3xl font-bold text-green-600">${<?php echo number_format($kpis['today_revenue'] ?? 0, 2); ?></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Last 24 hours</p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Occupancy</p>
                    <h3 class="text-3xl font-bold text-blue-600"><?php echo $kpis['occupancy_percentage'] ?? 0; ?>%</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Current</p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Active Users</p>
                    <h3 class="text-3xl font-bold text-purple-600"><?php echo $kpis['active_users'] ?? 0; ?></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Verified accounts</p>
                </div>
            </div>

            <!-- Revenue Trends -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Revenue Trends</h2>
                    <div class="space-y-3">
                        <?php foreach (array_slice($revenue, 0, 7) as $day): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600 dark:text-slate-400"><?php echo date('M d', strtotime($day['date'])); ?></span>
                            <div class="flex items-center gap-3 flex-1 ml-4">
                                <div class="flex-1 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-primary h-full rounded-full" style="width: <?php echo min(100, ($day['revenue'] / 1000) * 100); ?>%"></div>
                                </div>
                                <span class="font-bold text-slate-900 dark:text-white text-right min-w-[60px]">${<?php echo number_format($day['revenue'], 0); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Top Parking Lots</h2>
                    <div class="space-y-3">
                        <?php foreach ($top_lots as $lot): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($lot['parking_lot']); ?></p>
                                <p class="text-xs text-slate-600 dark:text-slate-400"><?php echo $lot['reservations']; ?> reservations</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-primary">${<?php echo number_format($lot['revenue'] ?? 0, 0); ?></p>
                                <p class="text-xs text-slate-600 dark:text-slate-400"><?php echo $lot['occupied']; ?> occupied</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- User Activity -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Top Users</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-slate-900 dark:text-white">User</th>
                                <th class="text-right py-3 px-4 font-semibold text-slate-900 dark:text-white">Reservations</th>
                                <th class="text-right py-3 px-4 font-semibold text-slate-900 dark:text-white">Total Spent</th>
                                <th class="text-right py-3 px-4 font-semibold text-slate-900 dark:text-white">Last Reservation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            <?php foreach ($user_activity as $user): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['full_name']); ?></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right font-semibold text-slate-900 dark:text-white"><?php echo $user['total_reservations'] ?? 0; ?></td>
                                <td class="py-3 px-4 text-right font-bold text-primary">${<?php echo number_format($user['total_spent'] ?? 0, 2); ?></td>
                                <td class="py-3 px-4 text-right text-slate-600 dark:text-slate-400"><?php echo $user['last_reservation'] ? date('M d', strtotime($user['last_reservation'])) : 'N/A'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- STAFF MANAGEMENT PAGE -->
        <?php elseif ($page === 'staff'): ?>

        <div class="max-w-[1200px] mx-auto px-4 md:px-10 lg:px-40 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Create New Staff Form (Sidebar) -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6 sticky top-20">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Staff Member</h3>
                        
                        <form method="POST" class="space-y-3">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                            <div>
                                <label class="text-xs font-semibold text-slate-900 dark:text-white block mb-1">Username</label>
                                <input type="text" name="username" required
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-900 dark:text-white block mb-1">Email</label>
                                <input type="email" name="email" required
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-900 dark:text-white block mb-1">Password</label>
                                <input type="password" name="password" required
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-900 dark:text-white block mb-1">Full Name</label>
                                <input type="text" name="full_name" required
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-900 dark:text-white block mb-1">Role</label>
                                <select name="role"
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
                                    <option value="operator">Operator</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-primary text-white font-bold py-2 rounded-lg hover:bg-primary/90 transition-colors text-sm">
                                Add Staff Member
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Staff List -->
                <div class="lg:col-span-3">
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Staff Members</h1>
                    <p class="text-slate-600 dark:text-slate-400 mb-6"><?php echo count($staff_list); ?> total staff members</p>

                    <div class="space-y-3">
                        <?php foreach ($staff_list as $member): ?>
                        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-4 flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($member['full_name']); ?></h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($member['email']); ?></p>
                                <div class="flex gap-2 mt-2">
                                    <span class="px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        <?php echo ucfirst(str_replace('_', ' ', $member['role'])); ?>
                                    </span>
                                    <?php if ($member['is_active']): ?>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="update_role">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <input type="hidden" name="staff_id" value="<?php echo $member['id']; ?>">
                                    <select name="role" onchange="this.form.submit()"
                                            class="px-3 py-1 rounded text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                        <option value="operator" <?php echo $member['role'] === 'operator' ? 'selected' : ''; ?>>Operator</option>
                                        <option value="admin" <?php echo $member['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="super_admin" <?php echo $member['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                                    </select>
                                </form>

                                <?php if ($member['is_active']): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="deactivate">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                        <input type="hidden" name="staff_id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" class="px-3 py-1 text-sm font-bold text-red-600 hover:text-red-700"
                                                onclick="return confirm('Deactivate this staff member?')">Remove</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- USER MANAGEMENT PAGE -->
        <?php elseif ($page === 'users'): ?>

        <div class="max-w-[1400px] mx-auto px-4 md:px-10 lg:px-40 py-10">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">User Management</h1>
            <p class="text-slate-600 dark:text-slate-400 mb-8">Manage user accounts and access</p>

            <!-- Users Table -->
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-slate-900 dark:text-white">User</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-900 dark:text-white">Email</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-900 dark:text-white">Phone</th>
                                <th class="px-6 py-3 text-center font-semibold text-slate-900 dark:text-white">Status</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-900 dark:text-white">Joined</th>
                                <th class="px-6 py-3 text-center font-semibold text-slate-900 dark:text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['full_name']); ?></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($user['username']); ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($user['is_active']): ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <span class="w-2 h-2 rounded-full bg-green-600"></span> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            <span class="w-2 h-2 rounded-full bg-red-600"></span> Inactive
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!$user['email_verified_at']): ?>
                                        <span class="inline-block ml-2 px-2 py-1 rounded text-xs font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            Unverified
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($user['is_active']): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="deactivate">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm"
                                                    onclick="return confirm('Deactivate this user?')">Deactivate</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-sm">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="flex items-center justify-center gap-2 mt-8">
                <?php if ($page_num > 1): ?>
                    <a href="?page=users&page_num=<?php echo $page_num - 1; ?>" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800">Previous</a>
                <?php endif; ?>

                <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                    <a href="?page=users&page_num=<?php echo $i; ?>" class="px-4 py-2 rounded-lg <?php echo $i === $page_num ? 'bg-primary text-white' : 'border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page_num < $total_pages): ?>
                    <a href="?page=users&page_num=<?php echo $page_num + 1; ?>" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php endif; ?>

    </main>

</div>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
