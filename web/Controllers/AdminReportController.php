<?php
/**
 * AdminReportController
 * Handles analytics and reporting
 */

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../Models/Report.php');
require_once(__DIR__ . '/../Models/Admin.php');

class AdminReportController {
    private $report_model;
    private $admin_model;
    
    public function __construct($conn) {
        $this->report_model = new Report($conn);
        $this->admin_model = new Admin($conn);
    }
    
    /**
     * Load reports dashboard
     */
    public function reports() {
        requireAdmin();
        
        $admin_id = getCurrentAdminId();
        $admin = $this->admin_model->getAdminById($admin_id);
        
        $kpis = $this->report_model->getDashboardKPIs();
        $revenue_data = $this->report_model->getRevenueReport();
        $top_lots = $this->report_model->getTopParkingLots();
        $user_activity = $this->report_model->getUserActivityReport(10);
        $total_revenue = $this->report_model->getTotalRevenue();
        
        return compact('admin', 'kpis', 'revenue_data', 'top_lots', 'user_activity', 'total_revenue');
    }
}
?>
