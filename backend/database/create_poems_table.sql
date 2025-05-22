-- Create the poems table
CREATE TABLE IF NOT EXISTS `poems` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poem_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `poem_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'classical',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `keywords` json DEFAULT NULL,
  `generated_with_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `likes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
