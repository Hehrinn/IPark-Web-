<?php
/**
 * AdminDashboardController
 * Handles admin dashboard operations
 */

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../Models/Admin.php');
require_once(__DIR__ . '/../Models/Report.php');
require_once(__DIR__ . '/../Models/Reservation.php');
require_once(__DIR__ . '/../Models/Parking.php');

class AdminDashboardController {
    private $admin_model;
    private $report_model;
    private $reservation_model;
    private $parking_model;
    
    public function __construct($conn) {
        $this->admin_model = new Admin($conn);
        $this->report_model = new Report($conn);
        $this->reservation_model = new Reservation($conn);
        $this->parking_model = new Parking($conn);
    }
    
    /**
     * Load admin dashboard
     */
    public function dashboard() {
        requireAdmin();
        
        $admin_id = getCurrentAdminId();
        $admin = $this->admin_model->getAdminById($admin_id);
        $kpis = $this->report_model->getDashboardKPIs();
        $recent_reservations = $this->reservation_model->getAllReservations(null, 5);
        $occupancy = $this->parking_model->getOccupancyStats();
        
        return compact('admin', 'kpis', 'recent_reservations', 'occupancy');
    }
}
?>
