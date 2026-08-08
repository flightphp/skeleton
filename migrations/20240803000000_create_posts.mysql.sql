-- MySQL sample migration: posts table
-- Applied only when database.driver / DB_DRIVER is mysql.
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `posts` (`title`, `content`, `created_at`, `updated_at`) VALUES
    ('Hello Flight', 'Your first post from the skeleton migration.', NOW(), NOW()),
    ('Build something great', 'Inject SimplePdo, use ActiveRecord, render with Twig.', NOW(), NOW());
