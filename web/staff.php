<?php
/**
 * iPark Staff Portal
 * Consolidated staff dashboard with routing
 * Handles: dashboard, reservation approvals
 */

require_once(__DIR__ . '/config/db.php');
require_once(__DIR__ . '/includes/auth.php');
require_once(__DIR__ . '/Models/Admin.php');
require_once(__DIR__ . '/Models/StaffApproval.php');
require_once(__DIR__ . '/web/Models/Report.php');

requireAdmin();
requireAdminRole('operator');

$staff_id = getCurrentAdminId();
$admin_model = new Admin($conn);
$approval_model = new StaffApproval($conn);
$report_model = new Report($conn);

$staff = $admin_model->getAdminById($staff_id);
$pending_approvals = $approval_model->getStaffApprovals($staff_id);
$pending_count = count($pending_approvals);

// Determine page from query string
$page = $_GET['page'] ?? 'dashboard';
$page_title = 'Staff Portal';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');
    
    $approval_id = intval($_POST['approval_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve') {
        if ($approval_model->approveReservation($approval_id, $staff_id)) {
            $_SESSION['flash_message'] = ['success' => 'Reservation approved!'];
        }
    } elseif ($action === 'reject') {
        $reason = trim($_POST['rejection_reason'] ?? '');
        if ($approval_model->rejectReservation($approval_id, $staff_id, $reason)) {
            $_SESSION['flash_message'] = ['success' => 'Reservation rejected'];
        }
    }
    
    header('Location: /staff.php?page=approvals');
    exit();
}

// Load dashboard data
if ($page === 'dashboard') {
    $kpis = $report_model->getDashboardKPIs();
}

?>
<?php require_once(__DIR__ . '/includes/header.php'); ?>

<div class="dashboard-wrapper flex">
    <!-- Sidebar -->
    <aside class="sidebar bg-slate-900 text-white w-64 min-h-screen p-6 fixed left-0 top-0">
        <div class="mb-8">
            <h2 class="text-2xl font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">local_parking</span>
                iPark Staff
            </h2>
        </div>

        <nav class="space-y-2">
            <a href="?page=dashboard" class="block px-4 py-3 <?php echo $page === 'dashboard' ? 'bg-primary rounded-lg' : 'hover:bg-slate-800 rounded-lg'; ?> font-semibold transition-colors">
                <i class="material-symbols-outlined align-middle">dashboard</i> Dashboard
            </a>
            <a href="?page=approvals" class="block px-4 py-3 <?php echo $page === 'approvals' ? 'bg-primary rounded-lg' : 'hover:bg-slate-800 rounded-lg'; ?> font-semibold transition-colors">
                <i class="material-symbols-outlined align-middle">checklist</i> Approvals
                <?php if ($pending_count > 0): ?>
                    <span class="ml-2 bg-red-600 px-2 py-1 rounded text-xs"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="border-t border-slate-700 my-4"></div>
            <a href="/logout.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg transition-colors font-semibold">
                <i class="material-symbols-outlined align-middle">logout</i> Logout
            </a>
        </nav>

        <div class="mt-8 pt-4 border-t border-slate-700">
            <p class="text-xs text-slate-400">Logged in as:</p>
            <p class="font-semibold"><?php echo htmlspecialchars($staff['full_name']); ?></p>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 p-8 min-h-screen bg-slate-50 dark:bg-slate-950 flex-1">
        <div class="max-w-6xl mx-auto">

            <!-- DASHBOARD PAGE -->
            <?php if ($page === 'dashboard'): ?>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-slate-900 dark:text-white">Welcome, <?php echo htmlspecialchars($staff['full_name']); ?></h1>
                <p class="text-slate-600 dark:text-slate-400">Manage and approve parking reservations</p>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Pending Approvals</p>
                    <h3 class="text-3xl font-bold text-orange-600"><?php echo $pending_count; ?></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Awaiting action</p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Reservations</p>
                    <h3 class="text-3xl font-bold text-blue-600"><?php echo $kpis['today_reservations'] ?? 0; ?></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Today</p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Occupancy</p>
                    <h3 class="text-3xl font-bold text-green-600"><?php echo $kpis['occupancy_percentage'] ?? 0; ?>%</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Current</p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Occupied Slots</p>
                    <h3 class="text-3xl font-bold text-purple-600"><?php echo $kpis['occupied_slots'] ?? 0; ?>/<?php echo $kpis['total_slots'] ?? 0; ?></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Total available</p>
                </div>
            </div>

            <!-- Pending Approvals -->
            <section class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">Pending Approvals</h2>

                <?php if ($pending_count > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($pending_approvals as $approval): ?>
                            <div class="p-4 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($approval['full_name']); ?></h3>
                                        <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($approval['email']); ?></p>
                                    </div>
                                    <span class="px-3 py-1 bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded-full text-xs font-bold">
                                        Pending
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 gap-4 mb-4 pb-4 border-b border-slate-200 dark:border-slate-800">
                                    <div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Slot</p>
                                        <p class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($approval['slot_number']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Location</p>
                                        <p class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($approval['parking_lot']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Amount</p>
                                        <p class="font-semibold text-primary">₱<?php echo number_format($approval['total_amount'], 2); ?></p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <a href="?page=approvals" class="flex-1 bg-primary text-white font-bold py-2 rounded-lg hover:bg-primary/90 transition-colors text-center">
                                        View & Approve
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <p class="text-slate-600 dark:text-slate-400">No pending approvals at this moment. Great work!</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- APPROVALS PAGE -->
            <?php elseif ($page === 'approvals'): ?>

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Reservation Approvals</h1>
                <p class="text-slate-600 dark:text-slate-400">Review and approve pending parking reservations</p>
            </div>

            <?php if (count($pending_approvals) > 0): ?>
                <div class="space-y-4">
                    <?php foreach ($pending_approvals as $approval): ?>
                        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 p-6">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">User</p>
                                    <p class="font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($approval['full_name']); ?></p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($approval['email']); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Parking Slot</p>
                                    <p class="font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($approval['slot_number']); ?></p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($approval['parking_lot']); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Duration</p>
                                    <p class="font-bold text-slate-900 dark:text-white">
                                        <?php 
                                        $start = new DateTime($approval['start_time']);
                                        $end = new DateTime($approval['end_time']);
                                        $interval = $start->diff($end);
                                        echo $interval->h . 'h ' . $interval->i . 'm';
                                        ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Amount</p>
                                    <p class="font-bold text-primary text-lg">₱<?php echo number_format($approval['total_amount'], 2); ?></p>
                                </div>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 mb-6">
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2"><strong>Requested Time:</strong></p>
                                <p class="font-semibold text-slate-900 dark:text-white">
                                    <?php echo date('M d, Y H:i', strtotime($approval['start_time'])); ?> to 
                                    <?php echo date('M d, Y H:i', strtotime($approval['end_time'])); ?>
                                </p>
                            </div>

                            <div class="flex gap-3">
                                <form method="POST" class="flex-1">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <input type="hidden" name="approval_id" value="<?php echo $approval['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition-colors">
                                        ✓ Approve
                                    </button>
                                </form>

                                <form method="POST" class="flex-1">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <input type="hidden" name="approval_id" value="<?php echo $approval['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="rejection_reason" value="Rejected by operator">
                                    <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700 transition-colors"
                                            onclick="return confirm('Are you sure you want to reject this reservation?')">
                                        ✕ Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-12 text-center">
                    <p class="text-slate-600 dark:text-slate-400 text-lg">No pending approvals at this time</p>
                </div>
            <?php endif; ?>

            <?php endif; ?>

        </div>
    </main>
</div>

<style>
    .dashboard-wrapper {
        display: flex;
    }
</style>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
