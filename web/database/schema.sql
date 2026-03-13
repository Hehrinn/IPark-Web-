-- iPark Database Schema
-- Created: March 2026
-- Database Name: u847001018_citialerts (shared with other apps)
-- Paste into phpMyAdmin (select `u847001018_citialerts`, then SQL tab)

USE `u847001018_citialerts`;

-- Prefixed tables (prefix = ipark_)
CREATE TABLE IF NOT EXISTS `ipark_users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `verification_token` VARCHAR(255) DEFAULT NULL,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_email` (`email`),
    UNIQUE INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins Table
CREATE TABLE IF NOT EXISTS `ipark_admins` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `role` ENUM('super_admin', 'admin', 'operator') NOT NULL DEFAULT 'admin',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_email` (`email`),
    UNIQUE INDEX `idx_username` (`username`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Parking Slots Table
CREATE TABLE IF NOT EXISTS `ipark_parking_slots` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slot_number` VARCHAR(50) NOT NULL UNIQUE,
    `floor_level` INT NOT NULL,
    `parking_lot` VARCHAR(100) NOT NULL,
    `vehicle_type` ENUM('car', 'motorcycle', 'ev_charging') NOT NULL DEFAULT 'car',
    `status` ENUM('available', 'occupied', 'reserved', 'maintenance') NOT NULL DEFAULT 'available',
    `hourly_rate` DECIMAL(8, 2) NOT NULL DEFAULT 5.00,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_slot_number` (`slot_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_lot` (`parking_lot`),
    INDEX `idx_floor` (`floor_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reservations Table
CREATE TABLE IF NOT EXISTS `ipark_reservations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `parking_slot_id` INT UNSIGNED NOT NULL,
    `vehicle_number` VARCHAR(50) DEFAULT NULL,
    `vehicle_type` VARCHAR(50) DEFAULT NULL,
    `check_in_time` TIMESTAMP NULL DEFAULT NULL,
    `check_out_time` TIMESTAMP NULL DEFAULT NULL,
    `duration_hours` INT DEFAULT NULL,
    `total_amount` DECIMAL(10, 2) DEFAULT NULL,
    `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
    `reservation_status` ENUM('pending_approval', 'approved', 'rejected', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending_approval',
    `admin_approved_by` INT UNSIGNED DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_slot` (`parking_slot_id`),
    INDEX `idx_status` (`reservation_status`),
    CONSTRAINT `fk_ipark_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `ipark_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ipark_reservations_slot` FOREIGN KEY (`parking_slot_id`) REFERENCES `ipark_parking_slots`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ipark_reservations_admin` FOREIGN KEY (`admin_approved_by`) REFERENCES `ipark_admins`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages Table
CREATE TABLE IF NOT EXISTS `ipark_messages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `admin_id` INT UNSIGNED DEFAULT NULL,
    `sender_type` ENUM('user', 'admin') NOT NULL,
    `message` TEXT NOT NULL,
    `attachment_url` VARCHAR(255) DEFAULT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_admin` (`admin_id`),
    INDEX `idx_created` (`created_at`),
    CONSTRAINT `fk_ipark_messages_user` FOREIGN KEY (`user_id`) REFERENCES `ipark_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ipark_messages_admin` FOREIGN KEY (`admin_id`) REFERENCES `ipark_admins`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Staff Approvals Table
CREATE TABLE IF NOT EXISTS `ipark_staff_approvals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `reservation_id` INT UNSIGNED NOT NULL,
    `staff_id` INT UNSIGNED NOT NULL,
    `approval_status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`approval_status`),
    INDEX `idx_reservation` (`reservation_id`),
    CONSTRAINT `fk_ipark_staff_approvals_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `ipark_reservations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ipark_staff_approvals_staff` FOREIGN KEY (`staff_id`) REFERENCES `ipark_admins`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Log Table
CREATE TABLE IF NOT EXISTS `ipark_audit_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `admin_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) DEFAULT NULL,
    `table_name` VARCHAR(100) DEFAULT NULL,
    `record_id` INT DEFAULT NULL,
    `old_value` JSON DEFAULT NULL,
    `new_value` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_admin` (`admin_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data Insertion (already exists, commented out to avoid duplicates)
-- INSERT INTO `ipark_parking_slots` (`slot_number`, `floor_level`, `parking_lot`, `vehicle_type`, `status`, `hourly_rate`) VALUES
-- ('A-01', 1, 'Downtown Central', 'car', 'available', 5.00),
-- ('A-02', 1, 'Downtown Central', 'car', 'occupied', 5.00),
-- ('A-03', 1, 'Downtown Central', 'car', 'available', 5.00),
-- ('A-04', 1, 'Downtown Central', 'car', 'available', 6.50),
-- ('A-05', 1, 'Downtown Central', 'car', 'occupied', 5.00),
-- ('EV-01', 1, 'Downtown Central', 'ev_charging', 'available', 8.00),
-- ('A-07', 1, 'Downtown Central', 'car', 'available', 5.00),
-- ('A-08', 1, 'Downtown Central', 'car', 'occupied', 5.00);

-- End of iPark prefixed schema
