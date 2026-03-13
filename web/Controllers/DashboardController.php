<?php
/**
 * DashboardController
 * Handles user dashboard operations
 */

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Parking.php');
require_once(__DIR__ . '/../Models/Reservation.php');

class DashboardController {
    private $user_model;
    private $parking_model;
    private $reservation_model;
    
    public function __construct($conn) {
        $this->user_model = new User($conn);
        $this->parking_model = new Parking($conn);
        $this->reservation_model = new Reservation($conn);
    }
    
    /**
     * Load user home dashboard
     */
    public function home() {
        requireUser();
        
        $user_id = getCurrentUserId();
        $user = $this->user_model->getUserById($user_id);
        $available_slots = $this->parking_model->getAvailableSlots();
        $user_reservations = $this->reservation_model->getUserReservations($user_id);
        $occupancy = $this->parking_model->getOccupancyStats();
        
        return compact('user', 'available_slots', 'user_reservations', 'occupancy');
    }
    
    /**
     * Load user profile page
     */
    public function profile() {
        requireUser();
        
        $user_id = getCurrentUserId();
        $user = $this->user_model->getUserById($user_id);
        
        return compact('user');
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($full_name, $phone) {
        requireUser();
        
        $user_id = getCurrentUserId();
        
        if ($this->user_model->updateUser($user_id, $full_name, $phone)) {
            $_SESSION['flash_message'] = ['success' => 'Profile updated successfully!'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to update profile'];
            return ['success' => false];
        }
    }
    
    /**
     * Load reservations page
     */
    public function reservations() {
        requireUser();
        
        $user_id = getCurrentUserId();
        $user = $this->user_model->getUserById($user_id);
        $reservations = $this->reservation_model->getUserReservations($user_id, 100);
        
        return compact('user', 'reservations');
    }
    
    /**
     * Cancel reservation
     */
    public function cancelReservation($reservation_id) {
        requireUser();
        
        $user_id = getCurrentUserId();
        $reservation = $this->reservation_model->getReservationById($reservation_id);
        
        // Verify user owns this reservation
        if ($reservation['user_id'] != $user_id) {
            $_SESSION['flash_message'] = ['error' => 'Unauthorized action'];
            return ['success' => false];
        }
        
        if ($this->reservation_model->cancelReservation($reservation_id)) {
            $_SESSION['flash_message'] = ['success' => 'Reservation cancelled'];
            return ['success' => true];
        } else {
            $_SESSION['flash_message'] = ['error' => 'Failed to cancel reservation'];
            return ['success' => false];
        }
    }
}
?>
