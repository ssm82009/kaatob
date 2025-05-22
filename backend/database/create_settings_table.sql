-- Create the settings table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `description`, `created_at`, `updated_at`)
VALUES
('gpt_api_key', NULL, 'OpenAI API Key for GPT integration', NOW(), NOW()),
('gpt_model', 'gpt-4', 'GPT Model to use for poem generation', NOW(), NOW()),
('gpt_temperature', '0.7', 'Temperature setting for GPT (controls randomness)', NOW(), NOW()),
('gpt_max_tokens', '1000', 'Maximum tokens per request', NOW(), NOW());
