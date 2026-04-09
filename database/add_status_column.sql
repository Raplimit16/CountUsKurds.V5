-- Add status tracking to applications
-- Run this in phpMyAdmin or MySQL CLI

ALTER TABLE `grundteam_applications`
ADD COLUMN `status` ENUM('new', 'reviewed', 'replied', 'accepted', 'rejected') DEFAULT 'new' AFTER `gdpr_consent`,
ADD COLUMN `admin_notes` TEXT NULL AFTER `status`,
ADD COLUMN `replied_at` TIMESTAMP NULL AFTER `admin_notes`,
ADD INDEX `idx_status` (`status`);

-- Update existing applications to 'new' status
UPDATE `grundteam_applications` SET `status` = 'new' WHERE `status` IS NULL;
