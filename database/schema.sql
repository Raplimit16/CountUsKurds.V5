-- ============================================
-- COUNT US KURDS - DATABASE SCHEMA
-- Version: 2.0
-- Run these queries in phpMyAdmin on Strato
-- ============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- ============================================
-- 1. ADMIN USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NULL,
    `password_hash` VARCHAR(255) NULL COMMENT 'bcrypt hash',
    `totp_secret` VARCHAR(64) NULL COMMENT 'Base32 encoded TOTP secret',
    `totp_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `role` ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `failed_attempts` INT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until` DATETIME NULL,
    `last_login` DATETIME NULL,
    `last_ip` VARCHAR(45) NULL,
    `password_changed_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_username` (`username`),
    UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. APPLICATIONS TABLE (Ansökningar)
-- ============================================
CREATE TABLE IF NOT EXISTS `applications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `application_type` ENUM('individual', 'organization') NOT NULL DEFAULT 'individual',
    `status` ENUM('pending', 'reviewed', 'approved', 'rejected', 'contacted') DEFAULT 'pending',
    
    -- Personal/Contact Info
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NULL,
    `country` VARCHAR(100) NOT NULL,
    `city` VARCHAR(100) NULL,
    `region` VARCHAR(100) NULL COMMENT 'Kurdish region if applicable',
    
    -- Individual specific
    `birth_year` INT NULL,
    `gender` ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL,
    `kurdish_dialect` VARCHAR(50) NULL COMMENT 'sorani, kurmanji, etc',
    `household_size` INT NULL COMMENT 'Number of Kurds in household',
    
    -- Organization specific
    `org_name` VARCHAR(255) NULL,
    `org_type` VARCHAR(100) NULL,
    `org_website` VARCHAR(255) NULL,
    `org_member_count` INT NULL,
    `org_kurdish_percentage` INT NULL COMMENT 'Percentage of Kurdish members',
    
    -- Consent & Meta
    `message` TEXT NULL,
    `gdpr_consent` TINYINT(1) NOT NULL DEFAULT 0,
    `newsletter_consent` TINYINT(1) NOT NULL DEFAULT 0,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `locale` VARCHAR(10) DEFAULT 'sv',
    `source` VARCHAR(100) NULL COMMENT 'How they found us',
    
    -- Admin fields
    `admin_notes` TEXT NULL,
    `reviewed_by` INT UNSIGNED NULL,
    `reviewed_at` DATETIME NULL,
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_type` (`application_type`),
    INDEX `idx_country` (`country`),
    INDEX `idx_created` (`created_at`),
    INDEX `idx_email` (`email`),
    FOREIGN KEY (`reviewed_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. POPULATION COUNTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `population_counts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `country` VARCHAR(100) NOT NULL,
    `city` VARCHAR(100) NULL,
    `region` VARCHAR(100) NULL,
    `count_type` ENUM('verified', 'estimated', 'self_reported') DEFAULT 'self_reported',
    `kurdish_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `year` INT NOT NULL,
    `source` VARCHAR(255) NULL COMMENT 'Source of data',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_country` (`country`),
    INDEX `idx_year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. SESSIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(128) NOT NULL,
    `user_id` INT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` TEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. ACTIVITY LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) NULL,
    `entity_id` INT UNSIGNED NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. EMAIL TEMPLATES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `email_templates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `locale` VARCHAR(10) NOT NULL DEFAULT 'en',
    `subject` VARCHAR(255) NOT NULL,
    `body_html` TEXT NOT NULL,
    `body_text` TEXT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_name_locale` (`name`, `locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. SETTINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT NULL,
    `type` ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    `description` VARCHAR(255) NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. RATE LIMITING TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(191) NOT NULL,
    `attempts` INT UNSIGNED NOT NULL DEFAULT 1,
    `expires_at` TIMESTAMP NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_key` (`key`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT DATA
-- ============================================

-- Insert default admin user (password: Admin123!@#)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `totp_secret`, `totp_enabled`, `role`) 
VALUES ('ceoadmin', 'admin@countuskurds.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4hKcEHgcVF1.KQPK', 'RP6IMPOYMAJD72P2', 1, 'super_admin')
ON DUPLICATE KEY UPDATE `totp_secret` = 'RP6IMPOYMAJD72P2', `totp_enabled` = 1;

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `type`, `description`) VALUES
('site_name', 'Count Us Kurds', 'string', 'Website name'),
('default_locale', 'sv', 'string', 'Default language'),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode'),
('registration_enabled', 'true', 'boolean', 'Allow new registrations'),
('email_notifications', 'true', 'boolean', 'Send email notifications')
ON DUPLICATE KEY UPDATE `key` = `key`;

-- ============================================
-- VERIFICATION QUERY
-- ============================================
-- Run this to verify all tables were created:
SHOW TABLES;
