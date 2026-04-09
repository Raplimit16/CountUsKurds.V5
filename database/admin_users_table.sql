-- Admin Users Table for Count Us Kurds
-- Run this in phpMyAdmin on Strato database: dbs14910556

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Create admin_users table
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NULL COMMENT 'bcrypt hash for password login',
  `totp_secret` VARCHAR(64) NULL COMMENT 'Base32 encoded TOTP secret',
  `totp_enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = TOTP enabled',
  `failed_attempts` INT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  `last_login` DATETIME NULL,
  `password_changed_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert ceoadmin user with TOTP enabled
-- TOTP Secret: RP6IMPOYMAJD72P2
INSERT INTO `admin_users` (`username`, `password_hash`, `totp_secret`, `totp_enabled`, `failed_attempts`)
VALUES ('ceoadmin', NULL, 'RP6IMPOYMAJD72P2', 1, 0)
ON DUPLICATE KEY UPDATE 
  `totp_secret` = 'RP6IMPOYMAJD72P2',
  `totp_enabled` = 1,
  `failed_attempts` = 0,
  `locked_until` = NULL;

-- Verify the user was created
SELECT * FROM `admin_users` WHERE `username` = 'ceoadmin';
