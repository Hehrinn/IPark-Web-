<?php
/**
 * iPark - Dashboard
 * Single entry point for all user dashboard functionality
 * Handles: Home, Profile, Reservations
 */

require_once(__DIR__ . '/config/db.php');
require_once(__DIR__ . '/includes/auth.php');
require_once(__DIR__ . '/Models/User.php');
require_once(__DIR__ . '/Models/Parking.php');
require_once(__DIR__ . '/Models/Reservation.php');

requireUser();

$user_id = getCurrentUserId();
$user_model = new User($conn);
$parking_model = new Parking($conn);
$reservation_model = new Reservation($conn);

$page = $_GET['page'] ?? 'home';
$page_title = 'Dashboard';
$style_file = 'dashboard'; // Load dashboard-specific styles

$user = $user_model->getUserById($user_id);

// Handle Slot Booking/Cancellation (CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_id'])) {
    verifyCSRFToken($_POST['csrf_token'] ?? '');
    $slot_id = intval($_POST['slot_id']);
    
    // Check if I already reserved this slot
    $active_res = $reservation_model->getActiveReservation($user_id, $slot_id);
    
    if ($active_res) {
        // Cancel Functionality
        $reservation_model->cancelReservation($active_res['id']);
        $parking_model->updateSlotStatus($slot_id, 'available'); // Reset status
        $_SESSION['flash_message'] = ['success' => 'Reservation cancelled successfully'];
    } else {
        // Reserve Functionality
        $slot = $parking_model->getSlotById($slot_id);
        
        if ($slot && $slot['status'] === 'available') {
            // Create 1-hour default reservation
            $start = date('Y-m-d H:i:s');
            $end = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $reservation_model->createReservation($user_id, $slot_id, $start, $end, $slot['hourly_rate']);
            // REMOVED: Slot remains 'available' until admin approves
            $_SESSION['flash_message'] = ['success' => 'Slot reserved successfully (Quick Reserve)'];
        }
    }
    // Silent Update: Reload page to reflect changes
    header('Location: dashboard.php?page=' . $page);
    exit();
}

// Handle POST requests (Reserve, Cancel, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');
    
    if ($page === 'profile' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if ($full_name && $phone) {
            if ($user_model->updateUser($user_id, $full_name, $phone)) {
                $_SESSION['flash_message'] = ['success' => 'Profile updated successfully'];
                $user = $user_model->getUserById($user_id);
                header('Location: dashboard.php?page=profile');
                exit();
            }
        }
    } elseif ($page === 'reservations' && isset($_POST['action']) && $_POST['action'] === 'create') {
        $slot_id = intval($_POST['parking_slot_id'] ?? 0);
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        
        // Validate: Prevent Past-Time Booking
        $start_dt = new DateTime($start_time);
        $now = new DateTime();
        if ($start_dt < $now) {
            $_SESSION['flash_message'] = ['error' => 'Error: You cannot book a slot for a time that has already passed.'];
            header('Location: dashboard.php?page=reservations');
            exit();
        }
        
        if ($slot_id && $start_time && $end_time) {
            $slot = $parking_model->getSlotById($slot_id);
            if ($slot) {
                $start = new DateTime($start_time);
                $end = new DateTime($end_time);
                $interval = $start->diff($end);
                $hours = ($interval->days * 24) + $interval->h;
                $minutes = $interval->i;
                $total_hours = $hours + ($minutes / 60);
                $total_amount = $total_hours * $slot['hourly_rate'];

                $new_reservation_id = $reservation_model->createReservation($user_id, $slot_id, $start_time, $end_time, $total_amount);
                if ($new_reservation_id) {
                    // REMOVED: Slot remains 'available' until admin approves
                    
                    logActivity('reservation_created', 'ipark_reservations', $new_reservation_id, null, [
                        'user_id' => $user_id,
                        'slot_id' => $slot_id,
                        'total_amount' => $total_amount
                    ]);
                    $_SESSION['flash_message'] = ['success' => 'Reservation created! Awaiting approval'];
                    header('Location: dashboard.php?page=reservations');
                    exit();
                }
            }
        }
    } elseif ($page === 'reservations' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $res_id = intval($_POST['reservation_id'] ?? 0);
        $slot_id = intval($_POST['parking_slot_id'] ?? 0);
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';

        if ($res_id && $slot_id && $start_time && $end_time) {
            $slot = $parking_model->getSlotById($slot_id);
            if ($slot) {
                $start = new DateTime($start_time);
                $end = new DateTime($end_time);
                $interval = $start->diff($end);
                $hours = ($interval->days * 24) + $interval->h;
                $minutes = $interval->i;
                $total_hours = $hours + ($minutes / 60);
                $total_amount = $total_hours * $slot['hourly_rate'];

                if ($reservation_model->updateReservationDetails($res_id, $user_id, $slot_id, $start_time, $end_time, $total_amount)) {
                    $_SESSION['flash_message'] = ['success' => 'Reservation updated and re-submitted for approval'];
                    header('Location: dashboard.php?page=reservations');
                    exit();
                }
            }
        }
    } elseif ($page === 'reservations' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
        $res_id = intval($_POST['reservation_id'] ?? 0);
        if ($res_id) {
            // Get reservation to find the slot
            $res = $reservation_model->getReservationById($res_id);
            
            if ($reservation_model->cancelReservation($res_id)) {
                // DELETE/UPDATE: Free up the slot (set back to available)
                if ($res) {
                    $parking_model->updateSlotStatus($res['parking_slot_id'], 'available');
                }
                
                $_SESSION['flash_message'] = ['success' => 'Reservation cancelled'];
                header('Location: dashboard.php?page=reservations');
                exit();
            }
        }
    } elseif ($page === 'reservations' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $res_id = intval($_POST['reservation_id'] ?? 0);
        if ($res_id && $reservation_model->deleteReservation($res_id, $user_id)) {
            $_SESSION['flash_message'] = ['success' => 'Notification deleted'];
            header('Location: dashboard.php?page=reservations');
            exit();
        }
    }
}

// Get data based on page
$all_slots = $parking_model->getAllSlots(); // Get all slots to show reserved status
$user_reservations = $reservation_model->getUserReservations($user_id, 100);
$occupancy = $parking_model->getOccupancyStats();

// Check for Edit Mode (Re-book)
$edit_res = null;
if ($page === 'reservations' && isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $fetched_res = $reservation_model->getReservationById($edit_id);
    // Ensure user owns this reservation and it is cancelled
    if ($fetched_res && $fetched_res['user_id'] == $user_id && $fetched_res['reservation_status'] === 'cancelled') {
        $edit_res = $fetched_res;
    }
}

?>
<?php require_once(__DIR__ . '/includes/header.php'); ?>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col md:flex-row">
    
    <!-- Google-style Sidebar -->
    <aside class="w-full md:w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex-shrink-0">
        <div class="p-6 flex items-center gap-3">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-xl">local_parking</span>
            </div>
            <span class="font-display font-bold text-xl text-slate-800 dark:text-white"><?php echo SITE_NAME; ?></span>
        </div>
        
        <nav class="px-3 space-y-1">
            <a href="?page=home" class="flex items-center gap-3 px-4 py-3 rounded-full <?php echo $page === 'home' ? 'bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'; ?> transition-colors font-medium">
                <span class="material-symbols-outlined">dashboard</span>
                Overview
            </a>
            <a href="?page=reservations" class="flex items-center gap-3 px-4 py-3 rounded-full <?php echo $page === 'reservations' ? 'bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'; ?> transition-colors font-medium">
                <span class="material-symbols-outlined">calendar_month</span>
                Reservations
            </a>
            <a href="?page=profile" class="flex items-center gap-3 px-4 py-3 rounded-full <?php echo $page === 'profile' ? 'bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'; ?> transition-colors font-medium">
                <span class="material-symbols-outlined">account_circle</span>
                My Profile
            </a>
            <div class="h-px bg-slate-200 dark:bg-slate-800 my-2 mx-4"></div>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="flex items-center gap-3 px-4 py-3 rounded-full text-slate-600 dark:text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors font-medium">
                <span class="material-symbols-outlined">logout</span>
                Sign Out
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-4 md:p-8 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
            
            <!-- Header User Info -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><?php echo $page === 'home' ? 'Dashboard' : ucfirst($page); ?></h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
            </div>

        <!-- Home Page -->
        <?php if ($page === 'home'): ?>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">local_parking</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Available Slots</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white"><?php echo $occupancy['available_slots']; ?></p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">analytics</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Occupancy Rate</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white"><?php echo $occupancy['occupancy_percentage']; ?>%</p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">confirmation_number</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Your Reservations</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white"><?php echo count($user_reservations); ?></p>
                </div>
            </div>
        </div>

        <!-- Available Parking Slots -->
        <section class="mb-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">Parking Slots</h2>
                <a href="?page=reservations" class="text-sm font-medium text-primary hover:underline">View All</a>
            </div>
            
            <!-- READ: Display 10 slots -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach (array_slice($all_slots, 0, 10) as $slot): ?>
                <?php 
                    $is_mine = $reservation_model->getActiveReservation($user_id, $slot['id']); 
                    
                    // Default: Available (Green)
                    $status_color = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                    $status_text = 'Available';

                    // Logic for Owner
                    if ($is_mine) {
                        // Owner sees Blue, with specific text
                        $status_color = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                        $status_text = ($is_mine['reservation_status'] === 'pending_approval') ? 'Pending' : 'My Spot';
                    } 
                    // Logic for Global Reserved (Admin Approved)
                    elseif ($slot['status'] === 'reserved') {
                        $status_color = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                        $status_text = 'Occupied';
                    }
                    // Else: Slot is 'available' (even if pending by others)
                ?>
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 rounded-lg <?php echo $status_color; ?> flex items-center justify-center">
                                <span class="material-symbols-outlined">local_parking</span>
                            </div>
                            <span class="px-2 py-1 <?php echo $status_color; ?> rounded-md text-xs font-bold uppercase tracking-wide">
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1"><?php echo htmlspecialchars($slot['slot_number']); ?></h3>
                        <p class="text-sm text-slate-500 mb-4"><?php echo htmlspecialchars($slot['parking_lot']); ?> • Floor <?php echo htmlspecialchars($slot['floor_level']); ?></p>
                        
                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-2">
                            <span class="material-symbols-outlined text-base">directions_car</span>
                            <?php echo ucfirst($slot['vehicle_type']); ?>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                            <span class="material-symbols-outlined text-base">payments</span>
                            ₱<?php echo number_format($slot['hourly_rate'], 2); ?>/hr
                        </div>
                    </div>
                    
                    <!-- UPDATE: Reserve Button -->
                    <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                            
                            <?php if ($is_mine): ?>
                                <button type="submit" class="w-full py-2 bg-white dark:bg-slate-800 border border-red-200 dark:border-red-900 text-red-600 font-semibold rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all text-sm">
                                    Cancel Reservation
                                </button>
                            <?php elseif ($slot['status'] === 'available'): ?>
                                <!-- Redirect to reservations page with slot_id -->
                                <a href="?page=reservations&slot_id=<?php echo $slot['id']; ?>" 
                                   class="block w-full text-center py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-primary font-semibold rounded-lg hover:bg-primary hover:text-white hover:border-primary transition-all text-sm">
                                    Reserve Now
                                </a>
                            <?php else: ?>
                                <button type="button" disabled class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-400 font-semibold rounded-lg cursor-not-allowed text-sm">
                                    Unavailable
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Profile Page -->
        <?php elseif ($page === 'profile'): ?>
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8 max-w-2xl">
            <h2 class="text-xl font-bold mb-6 text-slate-800 dark:text-white">Account Settings</h2>
            
            <form method="POST" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <input type="hidden" name="action" value="update">

                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required
                           class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                           class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-500 cursor-not-allowed">
                    <p class="text-xs text-slate-500 mt-1">Email cannot be changed</p>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required
                           class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                </div>

                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-lg shadow-lg shadow-primary/20 transition-all">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Reservations Page -->
        <?php elseif ($page === 'reservations'): ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- New Reservation Form -->
            <div class="lg:col-span-1 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 h-fit sticky top-6">
                <h3 class="text-lg font-bold mb-4 text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary"><?php echo $edit_res ? 'edit' : 'add_circle'; ?></span> 
                    <?php echo $edit_res ? 'Edit Reservation' : 'Make Reservation'; ?>
                </h3>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input type="hidden" name="action" value="<?php echo $edit_res ? 'update' : 'create'; ?>">
                    <?php if ($edit_res): ?>
                        <input type="hidden" name="reservation_id" value="<?php echo $edit_res['id']; ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Parking Slot</label>
                        <select name="parking_slot_id" required class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="">Select a slot...</option>
                            <?php foreach ($all_slots as $slot): ?>
                            <?php if ($slot['status'] === 'available'): ?>
                            <option value="<?php echo $slot['id']; ?>" 
                                <?php 
                                    // Pre-select if editing or if GET param exists
                                    if ($edit_res && $edit_res['parking_slot_id'] == $slot['id']) echo 'selected';
                                    elseif (isset($_GET['slot_id']) && $_GET['slot_id'] == $slot['id']) echo 'selected';
                                ?>
                            >
                                <?php echo $slot['slot_number']; ?> - <?php echo $slot['parking_lot']; ?> (₱<?php echo $slot['hourly_rate']; ?>/hr)
                            </option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Start Time</label>
                        <input type="datetime-local" name="start_time" required 
                               min="<?php echo date('Y-m-d\TH:i'); ?>"
                               value="<?php echo $edit_res ? date('Y-m-d\TH:i', strtotime($edit_res['start_time'])) : ''; ?>"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">End Time</label>
                        <input type="datetime-local" name="end_time" required 
                               value="<?php echo $edit_res ? date('Y-m-d\TH:i', strtotime($edit_res['end_time'])) : ''; ?>"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>

                    <button type="submit" class="w-full py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-lg shadow-lg shadow-primary/20 transition-all">
                        <?php echo $edit_res ? 'Update & Re-submit' : 'Confirm Reservation'; ?>
                    </button>
                    
                    <?php if ($edit_res): ?>
                        <a href="?page=reservations" class="block w-full text-center mt-3 text-sm text-slate-500 hover:text-slate-700">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Reservation List -->
            <div class="lg:col-span-2 space-y-4">
                <?php if (count($user_reservations) > 0): ?>
                    <?php foreach ($user_reservations as $res): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                <span class="material-symbols-outlined">confirmation_number</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-lg"><?php echo htmlspecialchars($res['slot_number']); ?></h4>
                                <p class="text-sm text-slate-500"><?php echo htmlspecialchars($res['parking_lot']); ?> • ₱<?php echo number_format($res['total_amount'], 2); ?></p>
                                <p class="text-xs text-slate-400 mt-1">
                                    <?php echo date('M d, H:i', strtotime($res['start_time'])); ?> - <?php echo date('M d, H:i', strtotime($res['end_time'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2 w-full sm:w-auto">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                <?php 
                                    if($res['reservation_status'] === 'approved') echo 'bg-green-100 text-green-700';
                                    elseif($res['reservation_status'] === 'cancelled') echo 'bg-red-100 text-red-700';
                                    else echo 'bg-orange-100 text-orange-700';
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $res['reservation_status'])); ?>
                            </span>
                            
                            <!-- DELETE/UPDATE: Cancel Button -->
                            <?php if ($res['reservation_status'] === 'pending_approval' || $res['reservation_status'] === 'approved'): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 hover:underline" onclick="return confirm('Cancel this reservation?')">
                                    Cancel Reservation
                                </button>
                            </form>
                            <?php elseif ($res['reservation_status'] === 'cancelled'): ?>
                            <!-- Delete Button for Cancelled Reservations -->
                            <div class="flex items-center gap-2">
                                <a href="?page=reservations&edit_id=<?php echo $res['id']; ?>" class="text-sm font-medium text-primary hover:underline">Re-book</a>
                                <span class="text-slate-300">|</span>
                                <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                <button type="submit" class="text-sm font-medium text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" onclick="return confirm('Permanently delete this notification?')">
                                    Delete
                                </button>
                            </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">event_busy</span>
                    <p class="text-slate-500">No reservations found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php endif; ?>
        </div>
    </main> <!-- End Main Content -->
</div>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
