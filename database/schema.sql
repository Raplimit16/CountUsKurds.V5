-- Count Us Kurds Database Schema
-- För MySQL 5.7+ / MariaDB 10.3+
-- Character Set: UTF-8 (utf8mb4)

-- Sätt korrekt teckenuppsättning
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Skapa grundteam_applications tabell
CREATE TABLE IF NOT EXISTS `grundteam_applications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_type` VARCHAR(20) NOT NULL DEFAULT 'individual' COMMENT 'individual eller group',
  `name` VARCHAR(255) NOT NULL COMMENT 'Fullständigt namn eller kontaktperson',
  `email` VARCHAR(255) NOT NULL COMMENT 'Email-adress',
  `region` VARCHAR(100) NOT NULL COMMENT 'Region eller diaspora',
  
  -- Individuell ansökan
  `individual_contribution` TEXT NULL COMMENT 'Beskriving av individuellt bidrag',
  
  -- Grupp/organisation ansökan
  `org_name` VARCHAR(255) NULL COMMENT 'Organisationsnamn',
  `org_contribution` TEXT NULL COMMENT 'Organisationens bidrag',
  `org_motive` TEXT NULL COMMENT 'Organisationens motivation',
  
  -- GDPR samtycke
  `gdpr_consent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = samtycke givet',
  
  -- Tidsstämplar
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Skapad datum',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Uppdaterad datum',
  
  -- Primary key
  PRIMARY KEY (`id`),
  
  -- Unik email (förhindrar duplicerade ansökningar)
  UNIQUE KEY `unique_email` (`email`),
  
  -- Index för snabbare sökningar
  KEY `idx_application_type` (`application_type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_region` (`region`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Foundation Team Applications';

-- Verifiera att tabellen skapades
SHOW TABLES LIKE 'grundteam_applications';

-- Visa tabellstruktur
DESCRIBE grundteam_applications;