/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `agent_carrier_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agent_carrier_commissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `insurance_carrier_id` bigint unsigned NOT NULL,
  `commission_percentage` decimal(5,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_carrier_unique` (`user_id`,`insurance_carrier_id`),
  KEY `agent_carrier_commissions_user_id_index` (`user_id`),
  KEY `agent_carrier_commissions_insurance_carrier_id_index` (`insurance_carrier_id`),
  KEY `agent_carrier_commissions_partner_id_foreign` (`partner_id`),
  CONSTRAINT `agent_carrier_commissions_insurance_carrier_id_foreign` FOREIGN KEY (`insurance_carrier_id`) REFERENCES `insurance_carriers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agent_carrier_commissions_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agent_carrier_commissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `agent_carrier_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agent_carrier_states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `insurance_carrier_id` bigint unsigned NOT NULL,
  `state` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settlement_level_pct` decimal(5,2) DEFAULT NULL,
  `settlement_graded_pct` decimal(5,2) DEFAULT NULL,
  `settlement_gi_pct` decimal(5,2) DEFAULT NULL,
  `settlement_modified_pct` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_carrier_state_unique` (`user_id`,`insurance_carrier_id`,`state`),
  KEY `agent_carrier_states_user_id_index` (`user_id`),
  KEY `agent_carrier_states_insurance_carrier_id_index` (`insurance_carrier_id`),
  KEY `agent_carrier_states_state_index` (`state`),
  KEY `agent_carrier_states_partner_id_index` (`partner_id`),
  CONSTRAINT `agent_carrier_states_insurance_carrier_id_foreign` FOREIGN KEY (`insurance_carrier_id`) REFERENCES `insurance_carriers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agent_carrier_states_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agent_carrier_states_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `allowed_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allowed_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `device_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_by` bigint unsigned DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `last_seen_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `allowed_devices_device_token_unique` (`device_token`),
  KEY `allowed_devices_added_by_foreign` (`added_by`),
  CONSTRAINT `allowed_devices_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `animation` enum('slide','fade','bounce','wave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'slide',
  `background_color` enum('red','yellow','blue','green','purple','orange') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blue',
  `icon` enum('warning','info','important','star','check','alert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `auto_dismiss` enum('never','5s','10s','30s') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'never',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_created_by_foreign` (`created_by`),
  KEY `announcements_is_active_index` (`is_active`),
  KEY `announcements_published_at_index` (`published_at`),
  CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `ip_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `working_hours` int NOT NULL DEFAULT '0',
  `punctuality_bonus_count` int NOT NULL DEFAULT '0',
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `expected_login_time` time NOT NULL DEFAULT '09:00:00',
  `late_minutes` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `device_fingerprint` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_user_id_date_unique` (`user_id`,`date`),
  KEY `attendances_is_late_index` (`is_late`),
  KEY `idx_attendances_user_id` (`user_id`),
  KEY `idx_attendances_date` (`date`),
  KEY `idx_attendances_user_date` (`user_id`,`date`),
  CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `changes` json DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `device_fingerprint` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_model_index` (`model`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bad_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bad_leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned DEFAULT NULL,
  `disposed_by` bigint unsigned NOT NULL,
  `disposition` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `lead_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_ssn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bad_leads_lead_id_foreign` (`lead_id`),
  KEY `bad_leads_disposition_created_at_index` (`disposition`,`created_at`),
  KEY `bad_leads_disposed_by_index` (`disposed_by`),
  CONSTRAINT `bad_leads_disposed_by_foreign` FOREIGN KEY (`disposed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `bad_leads_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `call_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `caller_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_data` json DEFAULT NULL,
  `webhook_data` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `event_time` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `call_events_lead_id_foreign` (`lead_id`),
  KEY `call_events_user_id_is_read_status_index` (`user_id`,`is_read`,`status`),
  CONSTRAINT `call_events_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `call_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `call_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `zoom_call_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_id` bigint unsigned NOT NULL,
  `agent_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `phone_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `call_type` enum('inbound','outbound') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'outbound',
  `call_status` enum('completed','missed','rejected','busy','no_answer','voicemail','connected','ringing') COLLATE utf8mb4_unicode_ci DEFAULT 'completed',
  `call_start_time` datetime NOT NULL,
  `call_end_time` datetime DEFAULT NULL,
  `duration_seconds` int NOT NULL DEFAULT '0',
  `outcome` enum('interested','not_interested','callback_requested','information_sent','sale_made','no_answer','wrong_number','do_not_call') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recording_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `follow_up_date` datetime DEFAULT NULL,
  `needs_follow_up` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `call_logs_lead_id_index` (`lead_id`),
  KEY `call_logs_agent_id_index` (`agent_id`),
  KEY `call_logs_created_by_index` (`created_by`),
  KEY `call_logs_call_start_time_index` (`call_start_time`),
  KEY `call_logs_call_status_index` (`call_status`),
  KEY `call_logs_outcome_index` (`outcome`),
  KEY `call_logs_needs_follow_up_index` (`needs_follow_up`),
  KEY `call_logs_agent_id_call_start_time_index` (`agent_id`,`call_start_time`),
  KEY `call_logs_lead_id_call_start_time_index` (`lead_id`,`call_start_time`),
  KEY `idx_call_logs_lead_id` (`lead_id`),
  KEY `idx_call_logs_agent_id` (`agent_id`),
  KEY `idx_call_logs_created_at` (`created_at`),
  KEY `idx_call_logs_status` (`call_status`),
  KEY `call_logs_zoom_call_id_index` (`zoom_call_id`),
  CONSTRAINT `call_logs_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `call_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `call_logs_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carrier_commission_brackets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrier_commission_brackets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `insurance_carrier_id` bigint unsigned NOT NULL,
  `age_min` int NOT NULL,
  `age_max` int NOT NULL,
  `commission_percentage` decimal(5,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_commission_idx` (`insurance_carrier_id`,`age_min`,`age_max`),
  CONSTRAINT `carrier_commission_brackets_insurance_carrier_id_foreign` FOREIGN KEY (`insurance_carrier_id`) REFERENCES `insurance_carriers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `premium_amount` decimal(10,2) DEFAULT NULL,
  `coverage_amount` decimal(12,2) DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','accepted','rejected','underwritten','forwarded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `forwarded_by` bigint unsigned DEFAULT NULL,
  `managed_by` bigint unsigned DEFAULT NULL,
  `sale_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carriers_lead_id_index` (`lead_id`),
  KEY `carriers_forwarded_by_index` (`forwarded_by`),
  KEY `carriers_managed_by_index` (`managed_by`),
  KEY `carriers_status_index` (`status`),
  KEY `idx_carriers_lead_id` (`lead_id`),
  KEY `idx_carriers_status` (`status`),
  CONSTRAINT `carriers_forwarded_by_foreign` FOREIGN KEY (`forwarded_by`) REFERENCES `users` (`id`),
  CONSTRAINT `carriers_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carriers_managed_by_foreign` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chart_of_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_category` enum('Current Asset','Fixed Asset','Current Liability','Long-term Liability','Owner Equity','Operating Revenue','Non-operating Revenue','Operating Expense','Non-operating Expense','Cost of Goods Sold') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_account_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chart_of_accounts_account_code_unique` (`account_code`),
  KEY `chart_of_accounts_parent_account_id_foreign` (`parent_account_id`),
  CONSTRAINT `chart_of_accounts_parent_account_id_foreign` FOREIGN KEY (`parent_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint unsigned NOT NULL,
  `file_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `mime_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_attachments_message_id_index` (`message_id`),
  CONSTRAINT `chat_attachments_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('direct','group') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'direct',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `community_id` bigint unsigned DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_conversations_created_by_foreign` (`created_by`),
  KEY `chat_conversations_type_index` (`type`),
  KEY `chat_conversations_created_at_index` (`created_at`),
  KEY `chat_conversations_community_id_foreign` (`community_id`),
  CONSTRAINT `chat_conversations_community_id_foreign` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chat_conversations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_message_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_message_reads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `read_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_message_reads_message_id_user_id_unique` (`message_id`,`user_id`),
  KEY `chat_message_reads_user_id_index` (`user_id`),
  KEY `chat_message_reads_read_at_index` (`read_at`),
  CONSTRAINT `chat_message_reads_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_message_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('text','image','file','audio') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `is_edited` tinyint(1) NOT NULL DEFAULT '0',
  `forwarded_from_message_id` bigint unsigned DEFAULT NULL,
  `forwarded_from_user_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_messages_conversation_id_index` (`conversation_id`),
  KEY `chat_messages_user_id_index` (`user_id`),
  KEY `chat_messages_created_at_index` (`created_at`),
  KEY `chat_messages_conversation_id_created_at_index` (`conversation_id`,`created_at`),
  CONSTRAINT `chat_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_notification_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_notification_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `notify_on_message` tinyint(1) NOT NULL DEFAULT '1',
  `notify_on_mention` tinyint(1) NOT NULL DEFAULT '1',
  `notify_sound_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `notify_desktop` tinyint(1) NOT NULL DEFAULT '1',
  `quiet_hours_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `quiet_hours_start` time NOT NULL DEFAULT '22:00:00',
  `quiet_hours_end` time NOT NULL DEFAULT '08:00:00',
  `push_subscription` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_notification_preferences_user_id_unique` (`user_id`),
  CONSTRAINT `chat_notification_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `is_muted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_participants_conversation_id_user_id_unique` (`conversation_id`,`user_id`),
  KEY `chat_participants_user_id_index` (`user_id`),
  KEY `chat_participants_last_read_at_index` (`last_read_at`),
  CONSTRAINT `chat_participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `communities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `communities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'users',
  `color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blue',
  `posting_restricted` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communities_created_by_index` (`created_by`),
  CONSTRAINT `communities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_announcement_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_announcement_reads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `announcement_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `community_announcement_reads_announcement_id_user_id_unique` (`announcement_id`,`user_id`),
  KEY `community_announcement_reads_user_id_foreign` (`user_id`),
  CONSTRAINT `community_announcement_reads_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `community_announcements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `community_announcement_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `community_id` bigint unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('info','normal','warning','urgent') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `created_by` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_banner` tinyint(1) NOT NULL DEFAULT '1',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `community_announcements_created_by_foreign` (`created_by`),
  KEY `community_announcements_community_id_is_active_index` (`community_id`,`is_active`),
  KEY `community_announcements_expires_at_index` (`expires_at`),
  CONSTRAINT `community_announcements_community_id_foreign` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `community_announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `community_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `community_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `added_by` bigint unsigned DEFAULT NULL,
  `can_post` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `community_members_community_id_user_id_unique` (`community_id`,`user_id`),
  KEY `community_members_user_id_foreign` (`user_id`),
  KEY `community_members_added_by_foreign` (`added_by`),
  CONSTRAINT `community_members_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `community_members_community_id_foreign` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `community_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dock_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dock_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `docked_by` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dock_date` date NOT NULL,
  `dock_month` tinyint NOT NULL,
  `dock_year` int NOT NULL,
  `status` enum('active','cancelled','applied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dock_records_user_id_dock_month_dock_year_index` (`user_id`,`dock_month`,`dock_year`),
  KEY `dock_records_status_index` (`status`),
  KEY `dock_records_docked_by_foreign` (`docked_by`),
  CONSTRAINT `dock_records_docked_by_foreign` FOREIGN KEY (`docked_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dock_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_info` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_contact` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnic` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_of_residence` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mis` enum('Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Yes',
  `date_of_termination` date DEFAULT NULL,
  `passport_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_strip_photo` tinyint(1) NOT NULL DEFAULT '1',
  `account_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_ai_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_ai_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned DEFAULT NULL,
  `generated_by` bigint unsigned NOT NULL,
  `prompt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` longtext COLLATE utf8mb4_unicode_ci,
  `plan_data` json DEFAULT NULL,
  `status` enum('generating','completed','failed','applied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generating',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_ai_plans_generated_by_foreign` (`generated_by`),
  KEY `epms_ai_plans_project_id_index` (`project_id`),
  CONSTRAINT `epms_ai_plans_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`),
  CONSTRAINT `epms_ai_plans_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `task_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_comments_task_id_foreign` (`task_id`),
  KEY `epms_comments_project_id_task_id_index` (`project_id`,`task_id`),
  KEY `epms_comments_user_id_index` (`user_id`),
  CONSTRAINT `epms_comments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epms_comments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `epms_tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epms_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `task_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int NOT NULL DEFAULT '0',
  `version` int NOT NULL DEFAULT '1',
  `uploaded_by` bigint unsigned NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `epms_documents_project_id_index` (`project_id`),
  KEY `epms_documents_task_id_index` (`task_id`),
  CONSTRAINT `epms_documents_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epms_documents_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `epms_tasks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `epms_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_external_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_external_costs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cost_type` enum('asset','api','subcontractor','software','hardware','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `amount` decimal(15,2) NOT NULL,
  `currency` enum('USD','PKR') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `incurred_date` date DEFAULT NULL,
  `vendor_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurring_period` enum('monthly','quarterly','yearly') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_external_costs_project_id_index` (`project_id`),
  KEY `epms_external_costs_cost_type_index` (`cost_type`),
  CONSTRAINT `epms_external_costs_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_milestones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `due_date` date NOT NULL,
  `completed_at` date DEFAULT NULL,
  `status` enum('pending','completed','missed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_milestones_project_id_index` (`project_id`),
  KEY `epms_milestones_due_date_index` (`due_date`),
  KEY `epms_milestones_status_index` (`status`),
  CONSTRAINT `epms_milestones_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_project_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_project_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `raci_role` enum('responsible','accountable','consulted','informed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'responsible',
  `project_role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_lead` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `epms_project_members_project_id_user_id_raci_role_unique` (`project_id`,`user_id`,`raci_role`),
  KEY `epms_project_members_project_id_index` (`project_id`),
  KEY `epms_project_members_user_id_index` (`user_id`),
  CONSTRAINT `epms_project_members_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epms_project_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` enum('US','PK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'US',
  `currency` enum('USD','PKR') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `contract_value` decimal(15,2) NOT NULL,
  `external_costs` decimal(15,2) NOT NULL DEFAULT '0.00',
  `gross_profit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `margin_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `estimated_completion_date` date DEFAULT NULL,
  `status` enum('planning','in-progress','on-hold','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planning',
  `methodology` enum('agile','waterfall','hybrid','kanban') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agile',
  `priority` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget` decimal(15,2) NOT NULL DEFAULT '0.00',
  `budget_spent` decimal(15,2) NOT NULL DEFAULT '0.00',
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `tags` json DEFAULT NULL,
  `ai_plan` json DEFAULT NULL,
  `ai_prompt` text COLLATE utf8mb4_unicode_ci,
  `repository_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tech_stack` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `health_score` enum('green','yellow','red') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'green',
  `project_velocity` decimal(8,2) NOT NULL DEFAULT '0.00',
  `scope_creep_count` int NOT NULL DEFAULT '0',
  `total_tasks` int NOT NULL DEFAULT '0',
  `completed_tasks` int NOT NULL DEFAULT '0',
  `revision_tasks` int NOT NULL DEFAULT '0',
  `created_by` bigint unsigned NOT NULL,
  `project_manager_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_projects_project_manager_id_foreign` (`project_manager_id`),
  KEY `epms_projects_status_index` (`status`),
  KEY `epms_projects_region_index` (`region`),
  KEY `epms_projects_health_score_index` (`health_score`),
  KEY `epms_projects_deadline_index` (`deadline`),
  KEY `epms_projects_created_by_index` (`created_by`),
  CONSTRAINT `epms_projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `epms_projects_project_manager_id_foreign` FOREIGN KEY (`project_manager_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_risks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_risks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `probability` enum('very_low','low','medium','high','very_high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `impact` enum('very_low','low','medium','high','very_high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `severity_score` int NOT NULL DEFAULT '0',
  `mitigation_plan` text COLLATE utf8mb4_unicode_ci,
  `contingency_plan` text COLLATE utf8mb4_unicode_ci,
  `owner_id` bigint unsigned DEFAULT NULL,
  `status` enum('identified','analyzing','mitigating','resolved','accepted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'identified',
  `category` enum('technical','schedule','budget','resource','scope','quality','external') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'technical',
  `identified_date` date DEFAULT NULL,
  `resolved_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_risks_owner_id_foreign` (`owner_id`),
  KEY `epms_risks_project_id_index` (`project_id`),
  KEY `epms_risks_status_index` (`status`),
  KEY `epms_risks_severity_score_index` (`severity_score`),
  CONSTRAINT `epms_risks_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `epms_risks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_sprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_sprints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `goal` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('planning','active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planning',
  `capacity_points` int NOT NULL DEFAULT '0',
  `completed_points` int NOT NULL DEFAULT '0',
  `sprint_number` int NOT NULL DEFAULT '1',
  `retrospective_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_sprints_project_id_index` (`project_id`),
  KEY `epms_sprints_status_index` (`status`),
  CONSTRAINT `epms_sprints_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_task_dependencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_task_dependencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `depends_on_task_id` bigint unsigned NOT NULL,
  `dependency_type` enum('finish-to-start','start-to-start','finish-to-finish','start-to-finish') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'finish-to-start',
  `lag_days` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `epms_task_dependencies_task_id_depends_on_task_id_unique` (`task_id`,`depends_on_task_id`),
  KEY `epms_task_dependencies_task_id_index` (`task_id`),
  KEY `epms_task_dependencies_depends_on_task_id_index` (`depends_on_task_id`),
  CONSTRAINT `epms_task_dependencies_depends_on_task_id_foreign` FOREIGN KEY (`depends_on_task_id`) REFERENCES `epms_tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epms_task_dependencies_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `epms_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `milestone_id` bigint unsigned DEFAULT NULL,
  `sprint_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('todo','in-progress','review','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todo',
  `kanban_column` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'backlog',
  `label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `task_type` enum('standard','revision') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `completed_at` date DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `progress` int NOT NULL DEFAULT '0',
  `estimated_hours` int NOT NULL DEFAULT '0',
  `story_points` int NOT NULL DEFAULT '0',
  `actual_hours` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `kanban_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_tasks_project_id_index` (`project_id`),
  KEY `epms_tasks_milestone_id_index` (`milestone_id`),
  KEY `epms_tasks_status_index` (`status`),
  KEY `epms_tasks_assigned_to_index` (`assigned_to`),
  KEY `epms_tasks_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `epms_tasks_sprint_id_foreign` (`sprint_id`),
  CONSTRAINT `epms_tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  CONSTRAINT `epms_tasks_milestone_id_foreign` FOREIGN KEY (`milestone_id`) REFERENCES `epms_milestones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `epms_tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epms_tasks_sprint_id_foreign` FOREIGN KEY (`sprint_id`) REFERENCES `epms_sprints` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epms_wbs_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epms_wbs_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `level` enum('phase','deliverable','work_package','activity') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'work_package',
  `estimated_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `estimated_hours` int NOT NULL DEFAULT '0',
  `actual_hours` int NOT NULL DEFAULT '0',
  `progress` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epms_wbs_items_project_id_index` (`project_id`),
  KEY `epms_wbs_items_parent_id_index` (`parent_id`),
  KEY `epms_wbs_items_code_index` (`code`),
  CONSTRAINT `epms_wbs_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `epms_wbs_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epms_wbs_items_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `epms_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `holidays_date_unique` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `insurance_carriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `insurance_carriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_module` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'on_draft',
  `base_commission_percentage` decimal(5,2) DEFAULT NULL,
  `age_min` int DEFAULT NULL,
  `age_max` int DEFAULT NULL,
  `plan_types` text COLLATE utf8mb4_unicode_ci,
  `calculation_notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `insurance_carriers_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_dials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_dials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `dialed_at` timestamp NOT NULL,
  `outcome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dialed',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_dials_lead_id_index` (`lead_id`),
  KEY `lead_dials_user_id_dialed_at_index` (`user_id`,`dialed_at`),
  KEY `lead_dials_lead_user_idx` (`lead_id`,`user_id`),
  CONSTRAINT `lead_dials_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_dials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_field_highlights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_field_highlights` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `field_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by_id` bigint unsigned NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_field_highlights_lead_id_field_name_unique` (`lead_id`,`field_name`),
  KEY `lead_field_highlights_updated_by_id_foreign` (`updated_by_id`),
  CONSTRAINT `lead_field_highlights_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_field_highlights_updated_by_id_foreign` FOREIGN KEY (`updated_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_locks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `locked_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_locks_lead_id_unique` (`lead_id`),
  KEY `lead_locks_user_id_foreign` (`user_id`),
  CONSTRAINT `lead_locks_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_locks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_phone_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cn_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height_weight` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_place` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_issue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `medications` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `doctor_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_address` text COLLATE utf8mb4_unicode_ci,
  `ssn` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `driving_license_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_carrier_id` bigint unsigned DEFAULT NULL,
  `coverage_amount` decimal(15,2) DEFAULT NULL,
  `monthly_premium` decimal(10,2) DEFAULT NULL,
  `settlement_type` enum('level','graded','gi','modified') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `beneficiary` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `beneficiary_dob` date DEFAULT NULL,
  `beneficiaries` json DEFAULT NULL,
  `emergency_contact` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smoker` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driving_license` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `initial_draft_date` date DEFAULT NULL,
  `future_draft_date` date DEFAULT NULL,
  `bank_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `routing_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acc_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_verified_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_balance` decimal(15,2) DEFAULT NULL,
  `ss_amount` decimal(10,2) DEFAULT NULL,
  `ss_date` date DEFAULT NULL,
  `card_number` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cvv` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expiry_date` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `closer_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `closer_id` bigint unsigned DEFAULT NULL,
  `assigned_partner` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preset_line` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT 'When closer sent to validator',
  `retention_status` enum('pending','retained','lost') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `retained_at` timestamp NULL DEFAULT NULL,
  `chargeback_marked_date` timestamp NULL DEFAULT NULL,
  `chargeback_marked_by_id` bigint unsigned DEFAULT NULL,
  `chargeback_paid_at` timestamp NULL DEFAULT NULL,
  `chargeback_paid_by_id` bigint unsigned DEFAULT NULL,
  `cb_sent_to_retention_at` timestamp NULL DEFAULT NULL,
  `cb_sent_to_retention_by_id` bigint unsigned DEFAULT NULL,
  `is_rewrite` tinyint(1) NOT NULL DEFAULT '0',
  `retention_notes` text COLLATE utf8mb4_unicode_ci,
  `ret_action_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'pending|in_progress|waiting_on_cx|fixed|cancelled|recalled',
  `ret_action_updated_at` timestamp NULL DEFAULT NULL,
  `ret_action_updated_by` bigint unsigned DEFAULT NULL,
  `retention_disposition` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `retention_officer_id` bigint unsigned DEFAULT NULL,
  `qa_status` enum('Pending','Good','Avg','Bad','In Review') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_reason` text COLLATE utf8mb4_unicode_ci,
  `qa_user_id` bigint unsigned DEFAULT NULL,
  `qa_reviewed_at` timestamp NULL DEFAULT NULL,
  `submission_status` enum('pending','approved','declined','underwriting','chargeback') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `submission_reason` text COLLATE utf8mb4_unicode_ci,
  `submission_by` bigint unsigned DEFAULT NULL,
  `submission_at` timestamp NULL DEFAULT NULL,
  `ravens_validated_at` timestamp NULL DEFAULT NULL,
  `ravens_validated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ravens_validation_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'valid or not_valid — set by Ravens Validator',
  `recall_requested_at` timestamp NULL DEFAULT NULL,
  `recall_requested_by` bigint unsigned DEFAULT NULL,
  `recall_note` text COLLATE utf8mb4_unicode_ci,
  `decline_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `staff_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `closer_qna` json DEFAULT NULL,
  `manager_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `forwarded_by` bigint unsigned DEFAULT NULL,
  `managed_by` bigint unsigned DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'When verifier submitted the lead',
  `validated_by` bigint unsigned DEFAULT NULL,
  `sale_at` timestamp NULL DEFAULT NULL,
  `disposed_at` timestamp NULL DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `resale_count` smallint unsigned NOT NULL DEFAULT '0',
  `resale_log` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `assigned_validator_id` bigint unsigned DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL COMMENT 'When validator processed (approved/returned/declined)',
  `returned_at` timestamp NULL DEFAULT NULL COMMENT 'When validator returned to closer',
  `declined_at` timestamp NULL DEFAULT NULL COMMENT 'When lead was declined',
  `transferred_at` timestamp NULL DEFAULT NULL COMMENT 'When lead was transferred to closer',
  `pending_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_info` text COLLATE utf8mb4_unicode_ci,
  `disposed_by` bigint unsigned DEFAULT NULL,
  `disposition_reason` enum('no_answer','wrong_number','wrong_details') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback_note` text COLLATE utf8mb4_unicode_ci,
  `callback_note_updated_at` timestamp NULL DEFAULT NULL,
  `issuance_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issuance_date` timestamp NULL DEFAULT NULL,
  `pending_contract_at` timestamp NULL DEFAULT NULL,
  `issued_policy_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_agent_id` bigint unsigned DEFAULT NULL,
  `policy_number_set_at` timestamp NULL DEFAULT NULL,
  `assigned_agent_set_at` timestamp NULL DEFAULT NULL,
  `partner_id` bigint unsigned DEFAULT NULL,
  `partner_set_at` timestamp NULL DEFAULT NULL,
  `commission_paid_to_partner` tinyint(1) NOT NULL DEFAULT '0',
  `commission_paid_at` timestamp NULL DEFAULT NULL,
  `agent_commission` decimal(15,2) DEFAULT NULL COMMENT 'Calculated commission: Monthly Premium × 9 × Settlement %',
  `agent_revenue` decimal(15,2) DEFAULT NULL COMMENT 'Final revenue after calculations',
  `settlement_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Settlement % used for commission calculation',
  `commission_calculation_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Details about how commission was calculated',
  `commission_calculated_at` timestamp NULL DEFAULT NULL COMMENT 'When commission was calculated',
  `issuance_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_followup_person` bigint unsigned DEFAULT NULL,
  `followup_assigned_by` bigint unsigned DEFAULT NULL,
  `followup_assigned_at` timestamp NULL DEFAULT NULL,
  `followup_required` tinyint(1) NOT NULL DEFAULT '0',
  `followup_scheduled_at` datetime DEFAULT NULL,
  `assigned_bank_verifier` bigint unsigned DEFAULT NULL,
  `bank_verifier_assigned_by` bigint unsigned DEFAULT NULL,
  `bank_verifier_assigned_at` timestamp NULL DEFAULT NULL,
  `followup_status` enum('Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No',
  `issued_by` bigint unsigned DEFAULT NULL,
  `issuance_disposition` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Via Portal, Via Email, By Carrier, By Bank',
  `issuance_disposition_date` timestamp NULL DEFAULT NULL,
  `disposition_officer_id` bigint unsigned DEFAULT NULL,
  `has_other_insurances` tinyint(1) DEFAULT NULL,
  `bank_verification_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_verification_date` timestamp NULL DEFAULT NULL,
  `bank_verification_notes` text COLLATE utf8mb4_unicode_ci,
  `bank_verified_by` bigint unsigned DEFAULT NULL,
  `bank_verification_comment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pending_contract_by_id` bigint unsigned DEFAULT NULL,
  `not_issued_disposition` enum('email_missing','ssn_missing','postal_mail_missing','beneficiary_incomplete','doctor_info_missing','underwriting_by_law','cancelled_by_customer','other_reason') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `not_issued_comment` text COLLATE utf8mb4_unicode_ci,
  `not_issued_at` timestamp NULL DEFAULT NULL,
  `not_issued_by_id` bigint unsigned DEFAULT NULL,
  `not_issued_resolved_by_id` bigint unsigned DEFAULT NULL,
  `not_issued_resolved_at` timestamp NULL DEFAULT NULL,
  `followup_done_at` timestamp NULL DEFAULT NULL,
  `followup_done_by_id` bigint unsigned DEFAULT NULL,
  `pending_draft_at` timestamp NULL DEFAULT NULL,
  `pending_draft_by_id` bigint unsigned DEFAULT NULL,
  `not_paid_fdfp_type` enum('unstable_to_locate','insufficient_fund','unauthorized_payments','manual_action') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `not_paid_manual_disposition` enum('email_missing','ssn_missing','postal_mail_missing','beneficiary_incomplete','doctor_info_missing','underwriting_by_law','cancelled_by_customer') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `not_paid_at` timestamp NULL DEFAULT NULL,
  `not_paid_by_id` bigint unsigned DEFAULT NULL,
  `not_paid_comment` text COLLATE utf8mb4_unicode_ci,
  `ledger_journal_entry_id` bigint unsigned DEFAULT NULL,
  `ledger_sales_return_entry_id` bigint unsigned DEFAULT NULL COMMENT 'FK to the sales-return journal entry posted when this lead is chargebacked',
  `ledger_chargeback_paid_entry_id` bigint unsigned DEFAULT NULL COMMENT 'Journal entry ID for chargeback recovery (Dr 1200 AR / Cr 4100 Sales)',
  `paid_at` timestamp NULL DEFAULT NULL,
  `paid_by_id` bigint unsigned DEFAULT NULL,
  `policy_died_reason` enum('chargeback_failed_payment','chargeback_cancellation') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_died_at` timestamp NULL DEFAULT NULL,
  `policy_died_by_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leads_forwarded_by_index` (`forwarded_by`),
  KEY `leads_managed_by_index` (`managed_by`),
  KEY `leads_status_index` (`status`),
  KEY `leads_phone_number_index` (`phone_number`),
  KEY `leads_created_at_index` (`created_at`),
  KEY `idx_leads_status` (`status`),
  KEY `idx_leads_created_at` (`created_at`),
  KEY `idx_leads_phone` (`phone_number`),
  KEY `idx_leads_forwarded_by` (`forwarded_by`),
  KEY `idx_leads_managed_by` (`managed_by`),
  KEY `idx_leads_status_created` (`status`,`created_at`),
  KEY `leads_team_index` (`team`),
  KEY `leads_assigned_validator_id_foreign` (`assigned_validator_id`),
  KEY `leads_validated_by_foreign` (`validated_by`),
  KEY `leads_insurance_carrier_id_foreign` (`insurance_carrier_id`),
  KEY `leads_retention_officer_id_foreign` (`retention_officer_id`),
  KEY `leads_qa_user_id_foreign` (`qa_user_id`),
  KEY `leads_manager_user_id_foreign` (`submission_by`),
  KEY `leads_disposed_by_foreign` (`disposed_by`),
  KEY `leads_assigned_agent_id_foreign` (`assigned_agent_id`),
  KEY `leads_assigned_followup_person_foreign` (`assigned_followup_person`),
  KEY `leads_partner_id_index` (`partner_id`),
  KEY `leads_assigned_bank_verifier_foreign` (`assigned_bank_verifier`),
  KEY `leads_source_type_index` (`source_type`),
  KEY `idx_leads_closer_name` (`closer_name`),
  KEY `idx_leads_sale_at` (`sale_at`),
  KEY `idx_leads_sale_date` (`sale_date`),
  KEY `idx_leads_manager_status` (`submission_status`),
  KEY `idx_leads_issuance_status` (`issuance_status`),
  KEY `idx_leads_bank_verification_status` (`bank_verification_status`),
  KEY `idx_leads_retention_status` (`retention_status`),
  KEY `idx_leads_qa_status` (`qa_status`),
  KEY `idx_leads_verified_by` (`verified_by`),
  KEY `idx_leads_closer_sale_at` (`closer_name`,`sale_at`),
  KEY `idx_leads_status_manager` (`status`,`submission_status`),
  KEY `idx_leads_status_retention` (`status`,`retention_status`),
  KEY `leads_bank_verified_by_foreign` (`bank_verified_by`),
  KEY `leads_followup_assigned_by_foreign` (`followup_assigned_by`),
  KEY `leads_bank_verifier_assigned_by_foreign` (`bank_verifier_assigned_by`),
  KEY `idx_leads_closer_id` (`closer_id`),
  KEY `leads_pending_contract_by_id_foreign` (`pending_contract_by_id`),
  KEY `leads_not_issued_by_id_foreign` (`not_issued_by_id`),
  KEY `leads_not_issued_resolved_by_id_foreign` (`not_issued_resolved_by_id`),
  KEY `leads_followup_done_by_id_foreign` (`followup_done_by_id`),
  KEY `leads_pending_draft_by_id_foreign` (`pending_draft_by_id`),
  KEY `leads_not_paid_by_id_foreign` (`not_paid_by_id`),
  KEY `leads_paid_by_id_foreign` (`paid_by_id`),
  KEY `leads_policy_died_by_id_foreign` (`policy_died_by_id`),
  KEY `leads_pending_contract_at_idx` (`pending_contract_at`),
  KEY `leads_not_issued_at_idx` (`not_issued_at`),
  KEY `leads_followup_done_at_idx` (`followup_done_at`),
  KEY `leads_pending_draft_at_idx` (`pending_draft_at`),
  KEY `leads_not_paid_at_idx` (`not_paid_at`),
  KEY `leads_paid_at_idx` (`paid_at`),
  KEY `leads_policy_died_at_idx` (`policy_died_at`),
  KEY `leads_ravens_validation_status_index` (`ravens_validation_status`),
  KEY `leads_recall_requested_by_foreign` (`recall_requested_by`),
  KEY `leads_recall_requested_at_index` (`recall_requested_at`),
  KEY `leads_app_id_idx` (`app_id`),
  KEY `leads_ret_action_updated_by_foreign` (`ret_action_updated_by`),
  KEY `leads_ledger_journal_entry_id_index` (`ledger_journal_entry_id`),
  KEY `leads_ledger_sales_return_entry_id_foreign` (`ledger_sales_return_entry_id`),
  KEY `leads_chargeback_marked_by_id_foreign` (`chargeback_marked_by_id`),
  CONSTRAINT `leads_assigned_agent_id_foreign` FOREIGN KEY (`assigned_agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_assigned_bank_verifier_foreign` FOREIGN KEY (`assigned_bank_verifier`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_assigned_followup_person_foreign` FOREIGN KEY (`assigned_followup_person`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_assigned_validator_id_foreign` FOREIGN KEY (`assigned_validator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_bank_verified_by_foreign` FOREIGN KEY (`bank_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_bank_verifier_assigned_by_foreign` FOREIGN KEY (`bank_verifier_assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_chargeback_marked_by_id_foreign` FOREIGN KEY (`chargeback_marked_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_disposed_by_foreign` FOREIGN KEY (`disposed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_followup_assigned_by_foreign` FOREIGN KEY (`followup_assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_followup_done_by_id_foreign` FOREIGN KEY (`followup_done_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_forwarded_by_foreign` FOREIGN KEY (`forwarded_by`) REFERENCES `users` (`id`),
  CONSTRAINT `leads_insurance_carrier_id_foreign` FOREIGN KEY (`insurance_carrier_id`) REFERENCES `insurance_carriers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_ledger_journal_entry_id_foreign` FOREIGN KEY (`ledger_journal_entry_id`) REFERENCES `ledger_journal_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_ledger_sales_return_entry_id_foreign` FOREIGN KEY (`ledger_sales_return_entry_id`) REFERENCES `ledger_journal_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_managed_by_foreign` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `leads_manager_user_id_foreign` FOREIGN KEY (`submission_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_not_issued_by_id_foreign` FOREIGN KEY (`not_issued_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_not_issued_resolved_by_id_foreign` FOREIGN KEY (`not_issued_resolved_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_not_paid_by_id_foreign` FOREIGN KEY (`not_paid_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_paid_by_id_foreign` FOREIGN KEY (`paid_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_pending_contract_by_id_foreign` FOREIGN KEY (`pending_contract_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_pending_draft_by_id_foreign` FOREIGN KEY (`pending_draft_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_policy_died_by_id_foreign` FOREIGN KEY (`policy_died_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_qa_user_id_foreign` FOREIGN KEY (`qa_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_recall_requested_by_foreign` FOREIGN KEY (`recall_requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_ret_action_updated_by_foreign` FOREIGN KEY (`ret_action_updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_retention_officer_id_foreign` FOREIGN KEY (`retention_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ledger_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledger_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `lead_id` bigint unsigned DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `type` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ledger_entries_user_id_foreign` (`user_id`),
  KEY `ledger_entries_lead_id_foreign` (`lead_id`),
  KEY `ledger_entries_vendor_id_index` (`vendor_id`),
  KEY `ledger_entries_transaction_date_index` (`transaction_date`),
  KEY `ledger_entries_type_index` (`type`),
  KEY `ledger_entries_category_index` (`category`),
  CONSTRAINT `ledger_entries_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `ledger_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ledger_entries_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ledger_journal_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledger_journal_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `entry_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entry_date` date NOT NULL,
  `type` enum('sale','payment_received','opening_balance','general','chargeback','sales_return','chargeback_recovery') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `insured_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '1',
  `total_debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(15,2) DEFAULT NULL,
  `our_share_percentage` decimal(6,4) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `lead_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ledger_journal_entries_entry_number_unique` (`entry_number`),
  KEY `ledger_journal_entries_entry_date_index` (`entry_date`),
  KEY `ledger_journal_entries_type_index` (`type`),
  KEY `ledger_journal_entries_created_by_index` (`created_by`),
  KEY `ledger_journal_entries_lead_id_index` (`lead_id`),
  CONSTRAINT `ledger_journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ledger_journal_entries_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ledger_journal_entry_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledger_journal_entry_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `partner_id` bigint unsigned DEFAULT NULL,
  `insurance_carrier_id` bigint unsigned DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ledger_journal_entry_lines_journal_entry_id_index` (`journal_entry_id`),
  KEY `ledger_journal_entry_lines_account_id_index` (`account_id`),
  KEY `ledger_journal_entry_lines_partner_id_index` (`partner_id`),
  KEY `ledger_journal_entry_lines_insurance_carrier_id_foreign` (`insurance_carrier_id`),
  CONSTRAINT `ledger_journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ledger_journal_entry_lines_insurance_carrier_id_foreign` FOREIGN KEY (`insurance_carrier_id`) REFERENCES `insurance_carriers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ledger_journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `ledger_journal_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ledger_journal_entry_lines_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `manual_payroll_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manual_payroll_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `join_date` date DEFAULT NULL,
  `payroll_month` int NOT NULL,
  `payroll_year` int NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `punctuality_bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `full_days` int NOT NULL DEFAULT '0',
  `half_days` int NOT NULL DEFAULT '0',
  `late_days` int NOT NULL DEFAULT '0',
  `is_qualified` tinyint(1) NOT NULL DEFAULT '0',
  `dock_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `other_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `other_allowances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `salary_advance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notepad_note_shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notepad_note_shares` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `note_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notepad_note_shares_note_id_user_id_unique` (`note_id`,`user_id`),
  KEY `notepad_note_shares_user_id_foreign` (`user_id`),
  CONSTRAINT `notepad_note_shares_note_id_foreign` FOREIGN KEY (`note_id`) REFERENCES `notepad_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notepad_note_shares_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notepad_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notepad_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_shared` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notepad_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `notepad_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `icon` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `data` json DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_important` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `notifications_user_id_read_at_index` (`user_id`,`read_at`),
  KEY `idx_notifications_user_id` (`user_id`),
  KEY `idx_notifications_read_at` (`read_at`),
  KEY `idx_notifications_user_read` (`user_id`,`read_at`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pabs_project_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pabs_project_approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned NOT NULL,
  `action` enum('APPROVED','REJECTED','CLARIFICATION NEEDED') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `approved_budget` decimal(12,2) DEFAULT NULL,
  `target_deadline` date DEFAULT NULL,
  `priority` enum('HIGH','MEDIUM','LOW') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pabs_project_approvals_project_id_index` (`project_id`),
  KEY `pabs_project_approvals_approved_by_index` (`approved_by`),
  KEY `pabs_project_approvals_approved_at_index` (`approved_at`),
  CONSTRAINT `pabs_project_approvals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pabs_project_approvals_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pabs_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pabs_project_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pabs_project_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pabs_project_comments_project_id_index` (`project_id`),
  KEY `pabs_project_comments_user_id_index` (`user_id`),
  KEY `pabs_project_comments_created_at_index` (`created_at`),
  CONSTRAINT `pabs_project_comments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pabs_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pabs_project_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pabs_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pabs_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` tinyint unsigned NOT NULL,
  `status` enum('DRAFT','SCOPING','QUOTING','PENDING APPROVAL','BUDGET ALLOCATED','IN PROGRESS','COMPLETED','ARCHIVED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `scoping_document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scoping_completed_at` timestamp NULL DEFAULT NULL,
  `vendor_a_quote` decimal(12,2) DEFAULT NULL,
  `vendor_a_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_b_quote` decimal(12,2) DEFAULT NULL,
  `vendor_b_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_c_quote` decimal(12,2) DEFAULT NULL,
  `vendor_c_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_status` enum('APPROVED','REJECTED','CLARIFICATION NEEDED') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('HIGH','MEDIUM','LOW') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_budget` decimal(12,2) DEFAULT NULL,
  `target_deadline` date DEFAULT NULL,
  `approval_notes` text COLLATE utf8mb4_unicode_ci,
  `actual_cost` decimal(12,2) DEFAULT NULL,
  `total_budget` decimal(12,2) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `variance_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `variance_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `scoping_lead_id` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `allocated_by` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `allocated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pabs_projects_project_code_unique` (`project_code`),
  KEY `pabs_projects_scoping_lead_id_foreign` (`scoping_lead_id`),
  KEY `pabs_projects_allocated_by_foreign` (`allocated_by`),
  KEY `pabs_projects_assigned_to_foreign` (`assigned_to`),
  KEY `pabs_projects_project_code_index` (`project_code`),
  KEY `pabs_projects_section_id_index` (`section_id`),
  KEY `pabs_projects_status_index` (`status`),
  KEY `pabs_projects_created_by_index` (`created_by`),
  KEY `pabs_projects_approved_by_index` (`approved_by`),
  CONSTRAINT `pabs_projects_allocated_by_foreign` FOREIGN KEY (`allocated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pabs_projects_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pabs_projects_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pabs_projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pabs_projects_scoping_lead_id_foreign` FOREIGN KEY (`scoping_lead_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pabs_ticket_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pabs_ticket_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pabs_ticket_comments_ticket_id_index` (`ticket_id`),
  KEY `pabs_ticket_comments_user_id_index` (`user_id`),
  CONSTRAINT `pabs_ticket_comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `pabs_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pabs_ticket_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pabs_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pabs_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` tinyint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `total_cost` decimal(12,2) DEFAULT NULL,
  `quote_amount` decimal(12,2) DEFAULT NULL,
  `approval_status` enum('PENDING','APPROVED','REJECTED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('OPEN','IN PROGRESS','ON HOLD','RESOLVED','CLOSED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPEN',
  `priority` enum('HIGH','MEDIUM','LOW') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MEDIUM',
  `created_by` bigint unsigned NOT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pabs_tickets_ticket_code_unique` (`ticket_code`),
  KEY `pabs_tickets_ticket_code_index` (`ticket_code`),
  KEY `pabs_tickets_section_id_index` (`section_id`),
  KEY `pabs_tickets_project_id_index` (`project_id`),
  KEY `pabs_tickets_status_index` (`status`),
  KEY `pabs_tickets_created_by_index` (`created_by`),
  KEY `pabs_tickets_assigned_to_index` (`assigned_to`),
  CONSTRAINT `pabs_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pabs_tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pabs_tickets_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pabs_projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ssn_last4` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `our_commission_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partners_name_unique` (`name`),
  UNIQUE KEY `partners_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payroll_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_settings_setting_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `petty_cash_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `petty_cash_ledgers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `serial_number` bigint NOT NULL,
  `date` date NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `head` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Category/Head name',
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `petty_cash_ledgers_serial_number_deleted_at_unique` (`serial_number`,`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `public_holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `public_holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_holidays_date_unique` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qa_calls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_calls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `zoom_call_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `call_log_id` bigint unsigned DEFAULT NULL,
  `agent_user_id` bigint unsigned DEFAULT NULL,
  `lead_id` bigint unsigned DEFAULT NULL,
  `agent_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_call_log_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Zoom-side call_log_id (UUID) from recording webhook — used for file_url download fallback',
  `caller_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_seconds` int NOT NULL DEFAULT '0',
  `call_start_time` timestamp NULL DEFAULT NULL,
  `recording_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_transcript_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_recording_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transcript_plain` text COLLATE utf8mb4_unicode_ci,
  `transcript_diarized` longtext COLLATE utf8mb4_unicode_ci,
  `transcript_source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'zoom or whisper — null means not yet transcribed',
  `assemblyai_transcript_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assemblyai_transcript_id_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_parts` json DEFAULT NULL,
  `assemblyai_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_file_path_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_original_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_original_name_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processing_status` enum('pending','downloading','transcribing','scoring','completed','failed','skipped') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `failure_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scored_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gemini' COMMENT 'gemini or claude',
  `retry_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qa_calls_zoom_call_id_unique` (`zoom_call_id`),
  KEY `qa_calls_call_log_id_index` (`call_log_id`),
  KEY `qa_calls_agent_user_id_index` (`agent_user_id`),
  KEY `qa_calls_zoom_user_id_index` (`zoom_user_id`),
  KEY `qa_calls_processing_status_index` (`processing_status`),
  KEY `qa_calls_assemblyai_transcript_id_index` (`assemblyai_transcript_id`),
  KEY `qa_calls_lead_id_index` (`lead_id`),
  CONSTRAINT `qa_calls_agent_user_id_foreign` FOREIGN KEY (`agent_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qa_calls_call_log_id_foreign` FOREIGN KEY (`call_log_id`) REFERENCES `call_logs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qa_calls_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qa_compliance_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_compliance_flags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `qa_call_id` bigint unsigned NOT NULL,
  `qa_result_id` bigint unsigned NOT NULL,
  `agent_user_id` bigint unsigned DEFAULT NULL,
  `check_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. C1, C2, C3...',
  `check_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. recording_disclosure',
  `check_label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Human readable label',
  `ai_reasoning` text COLLATE utf8mb4_unicode_ci,
  `flagged_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qa_compliance_flags_qa_call_id_index` (`qa_call_id`),
  KEY `qa_compliance_flags_qa_result_id_index` (`qa_result_id`),
  KEY `qa_compliance_flags_agent_user_id_index` (`agent_user_id`),
  KEY `qa_compliance_flags_check_code_index` (`check_code`),
  CONSTRAINT `qa_compliance_flags_agent_user_id_foreign` FOREIGN KEY (`agent_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qa_compliance_flags_qa_call_id_foreign` FOREIGN KEY (`qa_call_id`) REFERENCES `qa_calls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qa_compliance_flags_qa_result_id_foreign` FOREIGN KEY (`qa_result_id`) REFERENCES `qa_results` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qa_daily_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_daily_stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_user_id` bigint unsigned NOT NULL,
  `stat_date` date NOT NULL,
  `calls_scored` int NOT NULL DEFAULT '0',
  `avg_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `min_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `max_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `compliance_fails` int NOT NULL DEFAULT '0',
  `void_risks` int NOT NULL DEFAULT '0',
  `excellent_count` int NOT NULL DEFAULT '0',
  `exceptional_count` int NOT NULL DEFAULT '0',
  `good_count` int NOT NULL DEFAULT '0',
  `average_count` int NOT NULL DEFAULT '0',
  `poor_count` int NOT NULL DEFAULT '0',
  `avg_opening` decimal(4,2) NOT NULL DEFAULT '0.00',
  `avg_discovery` decimal(4,2) NOT NULL DEFAULT '0.00',
  `avg_presentation` decimal(4,2) NOT NULL DEFAULT '0.00',
  `avg_objection_handling` decimal(4,2) NOT NULL DEFAULT '0.00',
  `avg_closing` decimal(4,2) NOT NULL DEFAULT '0.00',
  `avg_soft_skills` decimal(4,2) NOT NULL DEFAULT '0.00',
  `avg_call_control` decimal(4,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qa_daily_stats_agent_user_id_stat_date_unique` (`agent_user_id`,`stat_date`),
  KEY `qa_daily_stats_agent_user_id_index` (`agent_user_id`),
  KEY `qa_daily_stats_stat_date_index` (`stat_date`),
  CONSTRAINT `qa_daily_stats_agent_user_id_foreign` FOREIGN KEY (`agent_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qa_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `qa_call_id` bigint unsigned NOT NULL,
  `disposition` enum('COMPLIANCE_FAIL','VOID_RISK','EXCELLENT','GOOD','AVERAGE','POOR') COLLATE utf8mb4_unicode_ci NOT NULL,
  `score_disposition` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `compliance_pass` tinyint(1) NOT NULL DEFAULT '0',
  `c1_closer_consent` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c2_agent_identity` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c3_carrier_named` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c4_product_type_stated` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c5_health_questions_complete` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c6_proper_quote` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c7_coverage_amount` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c8_draft_date_confirmed` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c9_end_of_call_consent` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c10_waiting_period` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c11_application_info_collected` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c12_customer_not_on_dnc` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c13_customer_not_aggressive` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c14_customer_not_disinterested` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c15_no_pushy_sale` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c16_appropriate_language` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `c17_customer_not_abusive` enum('pass','fail','na') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na',
  `score_opening` tinyint unsigned NOT NULL DEFAULT '0',
  `score_discovery` tinyint unsigned NOT NULL DEFAULT '0',
  `score_presentation` tinyint unsigned NOT NULL DEFAULT '0',
  `score_objection_handling` tinyint unsigned NOT NULL DEFAULT '0',
  `score_closing` tinyint unsigned NOT NULL DEFAULT '0',
  `score_soft_skills` tinyint unsigned NOT NULL DEFAULT '0',
  `score_call_control` tinyint unsigned NOT NULL DEFAULT '0',
  `coaching_notes` text COLLATE utf8mb4_unicode_ci,
  `top_issue` text COLLATE utf8mb4_unicode_ci,
  `strengths` json DEFAULT NULL,
  `improvements` json DEFAULT NULL,
  `void_risk_reason` text COLLATE utf8mb4_unicode_ci,
  `dnc_risk_level` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'DNC Judge standalone risk level: HIGH | MEDIUM | LOW | NONE',
  `dnc_judge_verdict` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'DNC Judge verdict: Litigator | DNC Risk | Aggressive Opt-Out | Clean',
  `dnc_judge_reasoning` text COLLATE utf8mb4_unicode_ci COMMENT 'AI explanation of which signals triggered the DNC Judge verdict',
  `compliance_failures` json DEFAULT NULL,
  `raw_ai_response` json DEFAULT NULL,
  `call_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer name extracted from transcript by AI',
  `closer_name_extracted` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Closer name extracted from transcript by AI (cross-reference with qa_calls.agent_name)',
  `is_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether a sale was made during this call',
  `sale_amount` decimal(10,2) DEFAULT NULL COMMENT 'Coverage/death benefit amount if sale was made',
  `monthly_premium` decimal(8,2) DEFAULT NULL COMMENT 'Monthly premium amount if sale was made',
  `carrier_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Insurance carrier name mentioned in call',
  `policy_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g., Whole Life, Term, Graded, Modified',
  `customer_state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer state if mentioned',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qa_results_qa_call_id_unique` (`qa_call_id`),
  KEY `qa_results_disposition_index` (`disposition`),
  KEY `qa_results_total_score_index` (`total_score`),
  KEY `qa_results_compliance_pass_index` (`compliance_pass`),
  KEY `qa_results_is_sale_index` (`is_sale`),
  CONSTRAINT `qa_results_qa_call_id_foreign` FOREIGN KEY (`qa_call_id`) REFERENCES `qa_calls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_module_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_module_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `module_id` bigint unsigned NOT NULL,
  `permission_level` enum('none','view','edit','full') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_module_permissions_role_id_module_id_unique` (`role_id`,`module_id`),
  KEY `role_module_permissions_role_id_index` (`role_id`),
  KEY `role_module_permissions_module_id_index` (`module_id`),
  KEY `role_module_permissions_permission_level_index` (`permission_level`),
  CONSTRAINT `role_module_permissions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_module_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `salary_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_components` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `salary_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_deductions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `salary_record_id` bigint unsigned NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `is_percentage` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_deductions_salary_record_id_foreign` (`salary_record_id`),
  CONSTRAINT `salary_deductions_salary_record_id_foreign` FOREIGN KEY (`salary_record_id`) REFERENCES `salary_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `salary_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `salary_year` year NOT NULL,
  `salary_month` tinyint NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `target_sales` int NOT NULL,
  `actual_sales` int NOT NULL DEFAULT '0',
  `chargeback_count` int NOT NULL DEFAULT '0',
  `net_approved_sales` int NOT NULL DEFAULT '0',
  `next_month_target_adjustment` int NOT NULL DEFAULT '0',
  `extra_sales` int NOT NULL DEFAULT '0',
  `bonus_per_extra_sale` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total_bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gross_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `working_days` int NOT NULL DEFAULT '22',
  `present_days` int NOT NULL DEFAULT '0',
  `leave_days` int NOT NULL DEFAULT '0',
  `late_days` int NOT NULL DEFAULT '0',
  `half_days` int NOT NULL DEFAULT '0',
  `daily_salary` decimal(8,2) NOT NULL DEFAULT '0.00',
  `attendance_bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `attendance_deduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dock_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dock_details` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','calculated','approved','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `calculated_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `salary_records_user_id_salary_year_salary_month_unique` (`user_id`,`salary_year`,`salary_month`),
  KEY `idx_salary_records_user_id` (`user_id`),
  CONSTRAINT `salary_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sticky_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sticky_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffd700',
  `position_x` int NOT NULL DEFAULT '50',
  `position_y` int NOT NULL DEFAULT '50',
  `z_index` int NOT NULL DEFAULT '1000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sticky_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `sticky_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_family_hash_index` (`family_hash`),
  KEY `telescope_entries_created_at_index` (`created_at`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plain_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Plaintext password for reference',
  `ssn_last4` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_states` json DEFAULT NULL,
  `carriers` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_module_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_module_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `module_id` bigint unsigned NOT NULL,
  `permission_level` enum('none','view','edit','full') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_module_permissions_user_id_module_id_unique` (`user_id`,`module_id`),
  KEY `user_module_permissions_user_id_index` (`user_id`),
  KEY `user_module_permissions_module_id_index` (`module_id`),
  KEY `user_module_permissions_permission_level_index` (`permission_level`),
  CONSTRAINT `user_module_permissions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_module_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_partner` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `salary_start_date` date DEFAULT NULL,
  `salary_end_date` date DEFAULT NULL,
  `payday_date` tinyint NOT NULL DEFAULT '5' COMMENT 'Day of month for salary payment (1-31)',
  `bonus_payday_date` tinyint unsigned NOT NULL DEFAULT '20',
  `salary_advance` decimal(10,2) DEFAULT '0.00',
  `tax_deduction` decimal(10,2) DEFAULT '0.00',
  `other_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `other_allowances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payroll_notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `employee_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','intern') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full_time',
  `employment_status` enum('active','inactive','on_leave','terminated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `agent_type` enum('employee','us_agent','vendor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'employee',
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zoom_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Zoom user ID (e.g. Jr14svAdSXGsMrUywRSFsA) for webhook agent matching',
  `zoom_extension` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Zoom Phone extension number (e.g. 805)',
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `target_sales` int NOT NULL DEFAULT '0',
  `bonus_per_extra_sale` decimal(8,2) NOT NULL DEFAULT '0.00',
  `punctuality_bonus` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_qualified_for_punctuality` tinyint(1) NOT NULL DEFAULT '1',
  `working_days_monthly` int NOT NULL DEFAULT '22',
  `full_days` int NOT NULL DEFAULT '0' COMMENT 'Number of full working days attended',
  `half_days` int NOT NULL DEFAULT '0' COMMENT 'Number of half working days attended (2 half days = 1 absent for punctuality)',
  `late_days` int NOT NULL DEFAULT '0' COMMENT 'Number of late arrivals (counted from full days, 4+ late = no punctuality bonus)',
  `override_punctuality_bonus` decimal(8,2) NOT NULL DEFAULT '0.00',
  `fine_per_absence` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Fine amount per absence/leave',
  `fine_per_late` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Fine amount per late arrival',
  `is_sales_employee` tinyint(1) NOT NULL DEFAULT '1',
  `commission_rate` decimal(5,2) DEFAULT NULL,
  `bank_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `routing_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_holder_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `vendor_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USA',
  `status` enum('active','inactive','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_session_ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_in` timestamp NULL DEFAULT NULL,
  `time_out` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  KEY `users_employee_id_index` (`employee_id`),
  KEY `users_employment_status_index` (`employment_status`),
  KEY `users_department_index` (`department`),
  KEY `users_joining_date_index` (`joining_date`),
  KEY `users_agent_type_index` (`agent_type`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_created_at` (`created_at`),
  KEY `users_status_index` (`status`),
  KEY `users_last_login_at_index` (`last_login_at`),
  KEY `users_is_partner_index` (`is_partner`),
  KEY `users_zoom_user_id_index` (`zoom_user_id`),
  KEY `users_zoom_extension_index` (`zoom_extension`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vendor_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `adjustments` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(15,2) GENERATED ALWAYS AS (((`amount` - `deductions`) + `adjustments`)) STORED,
  `status` enum('pending','approved','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendor_transactions_invoice_number_unique` (`invoice_number`),
  KEY `vendor_transactions_agent_id_index` (`agent_id`),
  KEY `vendor_transactions_invoice_number_index` (`invoice_number`),
  KEY `vendor_transactions_status_index` (`status`),
  KEY `vendor_transactions_payment_date_index` (`payment_date`),
  CONSTRAINT `vendor_transactions_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USA',
  `type` enum('US Agent','Vendor','Supplier') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Vendor',
  `status` enum('active','inactive','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `commission_rate` decimal(5,2) DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendors_email_unique` (`email`),
  KEY `vendors_email_index` (`email`),
  KEY `vendors_type_index` (`type`),
  KEY `vendors_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zoom_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zoom_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `account_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_extension` smallint unsigned DEFAULT NULL,
  `access_token` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh_token` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expires_at` timestamp NULL DEFAULT NULL,
  `token_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bearer',
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `auth_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'server_to_server',
  `app_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zoom_tokens_account_id_unique` (`account_id`),
  KEY `zoom_tokens_account_id_index` (`account_id`),
  KEY `zoom_tokens_expires_at_index` (`expires_at`),
  KEY `zoom_tokens_user_id_index` (`user_id`),
  KEY `zoom_tokens_app_type_index` (`app_type`),
  CONSTRAINT `zoom_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zoom_webhook_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zoom_webhook_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zoom_call_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caller_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caller_did_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caller_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caller_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caller_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caller_extension` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_did_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callee_extension` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_type` enum('inbound','outbound','internal') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_result` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_start_time` timestamp NULL DEFAULT NULL,
  `call_end_time` timestamp NULL DEFAULT NULL,
  `duration_seconds` int NOT NULL DEFAULT '0',
  `answer_time` timestamp NULL DEFAULT NULL,
  `ringing_start_time` timestamp NULL DEFAULT NULL,
  `recording_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recording_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recording_file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recording_file_size` bigint DEFAULT NULL,
  `recording_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recording_start_time` timestamp NULL DEFAULT NULL,
  `recording_end_time` timestamp NULL DEFAULT NULL,
  `transcript_text` text COLLATE utf8mb4_unicode_ci,
  `transcript_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transcript_file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_cost` decimal(10,4) DEFAULT NULL,
  `mos` float DEFAULT NULL,
  `call_rate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_id` bigint unsigned DEFAULT NULL,
  `agent_id` bigint unsigned DEFAULT NULL,
  `matched_call_log_id` bigint unsigned DEFAULT NULL,
  `raw_payload` json DEFAULT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT '0',
  `processing_notes` text COLLATE utf8mb4_unicode_ci,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `zoom_webhook_logs_matched_call_log_id_foreign` (`matched_call_log_id`),
  KEY `zoom_webhook_logs_caller_number_call_start_time_index` (`caller_number`,`call_start_time`),
  KEY `zoom_webhook_logs_callee_number_call_start_time_index` (`callee_number`,`call_start_time`),
  KEY `zoom_webhook_logs_event_type_created_at_index` (`event_type`,`created_at`),
  KEY `zoom_webhook_logs_lead_id_call_start_time_index` (`lead_id`,`call_start_time`),
  KEY `zoom_webhook_logs_agent_id_call_start_time_index` (`agent_id`,`call_start_time`),
  KEY `zoom_webhook_logs_event_type_index` (`event_type`),
  KEY `zoom_webhook_logs_zoom_call_id_index` (`zoom_call_id`),
  KEY `zoom_webhook_logs_caller_number_index` (`caller_number`),
  KEY `zoom_webhook_logs_callee_number_index` (`callee_number`),
  KEY `zoom_webhook_logs_call_start_time_index` (`call_start_time`),
  KEY `zoom_webhook_logs_is_processed_index` (`is_processed`),
  CONSTRAINT `zoom_webhook_logs_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `zoom_webhook_logs_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `zoom_webhook_logs_matched_call_log_id_foreign` FOREIGN KEY (`matched_call_log_id`) REFERENCES `call_logs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2018_08_08_100000_create_telescope_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_04_23_213306_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_04_23_220822_create_user_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_04_23_222846_create_leads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_05_24_041616_create_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_05_24_095729_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_06_01_005217_create_salary_records_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_06_01_005321_create_salary_deductions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_08_23_213930_create_carriers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_09_16_023326_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_10_01_000001_create_vendors_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_10_01_000002_create_ledger_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_10_01_193954_create_call_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_10_01_194032_add_employee_fields_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_10_01_210117_add_agent_type_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_10_01_210118_create_vendor_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_10_01_210156_add_attendance_enhancements',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_10_01_210156_create_salary_components_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_10_07_015620_add_optional_fields_to_leads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_10_07_140456_add_missing_fields_to_leads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_10_07_140849_add_remaining_optional_fields_to_leads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_10_07_141032_increase_encrypted_fields_length_in_leads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_10_07_172516_create_call_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_10_19_184521_create_sessions_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_10_19_201122_add_performance_indexes_to_tables',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_10_19_201512_create_chat_system_tables',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_10_21_235239_add_chargeback_status_to_leads_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_11_10_235424_add_status_tracking_to_users_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_11_10_235517_create_audit_logs_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_11_13_000001_create_zoom_tokens_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_11_28_234608_create_jobs_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_11_29_000001_add_team_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_12_01_222919_add_role_to_user_details_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_12_02_170646_add_verified_by_and_fields_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_12_02_233601_add_validator_and_pending_fields_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_12_02_add_failure_reason_to_leads',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_12_02_add_transferred_to_leads_status',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_12_02_add_validated_by_to_leads',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_12_03_214642_modify_leads_status_column_to_string',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_12_04_175605_rename_failure_reason_to_decline_reason_in_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_12_09_030100_create_insurance_carriers_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_12_09_030320_add_comprehensive_fields_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_12_09_030327_add_retention_fields_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_12_09_032524_add_missing_fields_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_12_09_035303_create_carrier_commission_brackets_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_12_10_000523_add_retained_at_and_fix_retention_status_in_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_12_13_030030_add_qa_fields_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_12_13_031001_add_manager_fields_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_12_17_000001_add_salary_settings_to_users_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_12_17_000002_add_chargeback_tracking_to_salary_records',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_12_17_000003_add_salary_period_and_fines_to_users',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_12_17_000004_create_dock_records_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_12_17_000006_create_agent_carrier_commissions_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_12_17_000007_add_ssn_last4_to_user_details',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_01_01_214856_create_holidays_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_01_01_222503_create_public_holidays_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_01_02_021649_add_beneficiaries_json_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_01_05_163955_update_attendance_times_to_datetime',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_01_05_231636_set_last_read_at_for_existing_participants',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_01_06_013733_add_secondary_phone_to_leads_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_01_06_025009_add_account_title_and_policy_number_to_leads',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_01_06_225720_add_device_fingerprint_to_attendances_and_audit_logs',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_01_07_025622_increase_expiry_date_column_length',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_01_09_024924_add_zoom_call_id_to_call_logs_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_01_13_000001_create_bad_leads_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_01_13_043318_add_disposition_fields_to_leads_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_01_21_051206_create_chart_of_accounts_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_01_21_221340_create_petty_cash_ledgers_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_01_22_000000_create_employees_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_01_23_000001_modify_employees_mis_column',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_01_23_000002_create_communities_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2026_01_23_000003_add_community_id_to_announcements',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2026_01_23_000004_add_community_id_to_chat_conversations',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2026_01_23_020433_fix_petty_cash_unique_constraint_for_soft_deletes',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_01_23_034335_add_avatar_to_users_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_01_24_000001_create_ceo_role',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_01_24_000002_create_community_announcements_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_01_24_022753_fix_attendance_cascade_delete',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_01_24_022816_fix_all_user_cascade_deletes',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_01_24_024959_create_announcements_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_01_24_081546_create_community_members_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_01_24_081548_create_community_announcement_reads_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_01_26_231849_add_bonus_payday_date_to_users_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_01_26_235804_add_tax_and_advance_to_users_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_01_27_042049_add_total_cost_to_pabs_tickets_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_01_27_042730_update_pabs_tickets_add_approval_and_quote',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_01_27_221946_add_issuance_disposition_fields_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_01_27_230000_add_bank_verification_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_01_28_000001_create_pabs_projects_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_01_28_000002_create_pabs_project_approvals_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2026_01_28_000003_create_pabs_project_comments_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2026_01_28_000004_create_pabs_tickets_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2026_01_28_000005_create_pabs_ticket_comments_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2026_01_28_002730_add_dock_fields_to_salary_records_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2026_01_28_044041_add_doctor_fields_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2026_01_28_create_chat_notification_preferences_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2026_01_29_000001_create_agent_carrier_states_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2026_01_29_000002_add_settlement_type_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2026_01_29_041914_add_assigned_partner_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2026_01_29_042103_add_policy_number_and_assigned_agent_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2026_01_29_042108_add_policy_number_and_assigned_agent_to_issuance_tracking',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2026_01_30_005808_update_qa_status_enum_values',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2026_01_30_014659_create_partners_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2026_01_30_015048_make_user_id_nullable_in_agent_carrier_states_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2026_01_30_044322_add_revenue_and_commission_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2026_01_30_051958_add_followup_fields_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2026_01_31_003435_add_partner_id_to_agent_carrier_commissions_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2026_01_31_004237_add_partner_id_to_leads_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2026_01_31_005604_add_phone_and_ssn_to_partners_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2026_01_31_012019_add_password_to_partners_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2026_01_31_035031_add_our_commission_percentage_to_partners_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2026_01_31_045247_add_can_message_to_community_members_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2026_02_02_000001_create_epms_projects_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2026_02_02_000002_create_epms_milestones_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2026_02_02_000003_create_epms_tasks_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2026_02_02_000004_create_epms_task_dependencies_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2026_02_02_000005_create_epms_external_costs_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2026_02_02_115043_add_avatar_to_communities_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2026_02_02_123323_add_avatar_to_chat_conversations_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2026_02_02_222836_add_bank_verification_assignment_columns_to_leads_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2026_02_02_224929_update_bank_verification_columns_in_leads_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2026_02_02_233447_add_is_partner_flag_to_users_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2026_02_03_034900_add_payroll_fields_to_users_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2026_02_03_043223_remove_can_message_from_community_members_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2026_02_03_215516_add_full_days_half_days_to_users_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2026_02_03_220113_create_payroll_settings_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2026_02_03_223101_add_audio_type_to_chat_messages_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2026_02_03_230044_add_stage_specific_timestamps_to_leads_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2026_02_03_240000_add_password_field_to_leads_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2026_02_03_240001_create_sticky_notes_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2026_02_03_250000_add_plain_password_to_user_details_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2026_02_03_260000_remove_password_from_leads_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2026_02_04_022718_fix_pabs_tickets_add_missing_columns',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2026_02_04_000002_add_late_days_to_users_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2026_02_04_000001_add_half_days_to_salary_records_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2026_02_05_043818_create_manual_payroll_entries_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2026_02_05_000000_add_followup_schedule_to_leads_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2026_02_06_022709_add_posting_permissions_to_communities',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2026_02_06_023130_add_can_post_to_community_members',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2026_02_10_015500_fix_community_announcements_constraints',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2026_02_11_000000_add_lead_source_type_column',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2026_02_11_000001_add_user_id_to_zoom_tokens_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2026_02_12_230732_add_callback_note_fields_to_leads_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2026_02_12_100000_create_lead_dials_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2026_02_13_000001_transform_epms_to_internal_pms',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2026_02_16_add_date_of_termination_to_employees_table',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2026_02_16_000001_create_modules_table',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2026_02_16_000002_create_role_module_permissions_table',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2026_02_16_000003_create_user_module_permissions_table',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2026_02_17_225645_add_connected_ringing_to_call_status_enum',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2026_02_18_000310_add_performance_indexes_to_leads_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2026_02_19_000001_add_granular_peregrine_ravens_modules',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2026_02_20_003114_add_parent_modules_for_hr_finance',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2026_02_22_112206_add_audit_timestamps_to_leads_table',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (160,'2026_02_22_114436_add_assignment_tracking_to_leads_table',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (161,'2026_02_23_072359_add_closer_id_to_leads_table',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2026_02_23_100000_drop_lead_dials_unique_constraint',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2026_02_23_000001_add_missing_modules_chat_reports',41);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2026_02_23_000002_remove_dead_holidays_notifications_modules',42);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2026_02_23_102500_add_soft_deletes_to_sticky_notes_table',43);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2026_02_23_110000_add_commission_paid_to_leads_table',44);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2026_02_23_224540_add_themes_module_to_modules_table',45);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2026_03_03_000001_create_qa_calls_table',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2026_03_03_000002_create_qa_results_table',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2026_03_03_000003_create_qa_compliance_flags_table',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2026_03_03_000004_create_qa_daily_stats_table',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2026_03_03_000001_create_zoom_webhook_logs_table',47);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2026_03_03_120000_add_sale_fields_to_qa_results_table',47);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2026_03_04_004502_add_zoom_call_log_id_to_qa_calls_table',48);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2026_03_04_005736_add_zoom_user_id_and_zoom_extension_to_users_table',49);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2026_03_04_022835_alter_qa_results_top_issue_to_text',50);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2026_03_04_080335_update_qa_results_compliance_columns_v2',51);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (179,'2026_03_05_033922_add_zoom_identity_to_zoom_tokens_table',52);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (180,'2026_03_05_200000_add_app_type_to_zoom_tokens_table',53);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2026_03_07_214532_add_zoom_transcript_url_to_qa_calls_table',54);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (182,'2026_03_07_000001_create_allowed_devices_table',55);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2026_03_07_142151_update_attendance_settings_for_mountain_time',56);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2026_03_07_220000_add_closer_qna_to_leads_table',57);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2026_03_07_000002_add_name_and_ip_to_allowed_devices',58);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2026_03_07_000003_replace_is_active_with_status_on_allowed_devices',59);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2026_03_07_150230_convert_attendance_times_pkt_to_mt',60);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2026_03_07_150230_convert_attendance_times_pkt_to_mt',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2026_03_08_000000_create_lead_locks_table',62);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2026_03_08_000001_backfill_team_column_on_leads_table',63);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2026_03_10_000001_create_ledger_journal_entries_table',64);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2026_03_10_000002_create_ledger_journal_entry_lines_table',64);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (193,'2026_03_10_141938_add_insurance_carrier_id_to_ledger_journal_entry_lines_table',65);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2026_03_10_151733_add_gross_amount_and_share_to_ledger_journal_entries',66);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2026_03_10_154806_add_insured_name_and_chargeback_accounts',67);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (196,'2026_03_10_162621_add_chargeback_to_journal_type_enum',68);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (197,'2026_03_10_162621_add_chargeback_to_journal_type_enum',69);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2026_03_11_120000_convert_attendance_times_mt_to_pt',70);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2026_03_11_105008_add_mos_to_zoom_webhook_logs_table',71);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2026_03_13_080808_add_show_strip_photo_to_employees_table',72);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (201,'2026_03_13_122438_add_resale_fields_to_leads_table',73);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2026_03_17_000001_add_ravens_validation_fields_to_leads_table',74);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (203,'2026_03_17_200000_add_sales_pipeline_stages_to_leads_table',75);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (204,'2026_03_17_200001_seed_new_sales_pipeline_modules',76);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2026_03_20_000001_add_permission_manager_module',77);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (206,'2026_03_24_103413_create_notepad_notes_table',78);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2026_03_24_113418_add_is_shared_to_notepad_notes_table',79);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2026_03_24_114702_create_notepad_note_shares_table',80);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2026_03_24_115535_create_notepad_note_shares_table',81);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2026_03_24_115535_create_notepad_note_shares_table',80);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (211,'2026_03_24_130828_add_deleted_at_to_notepad_notes_table',82);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (212,'2026_03_24_143027_add_ravens_validation_status_to_leads_table',83);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (213,'2026_03_25_000001_add_recall_fields_to_leads_table',84);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (214,'2026_03_25_110000_add_app_id_to_leads_table',85);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (215,'2026_03_25_133358_add_edit_and_forward_to_chat_messages_table',86);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (216,'2026_03_25_140307_auto_submit_approved_validated_leads_to_pending_contracts',87);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2026_03_25_234039_rename_manager_fields_to_submission_fields_in_leads_table',88);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2026_03_30_144151_add_cancelled_by_customer_to_not_issued_disposition_enum',89);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (219,'2026_03_30_151023_add_ret_action_status_to_leads_table',90);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2026_03_30_162017_add_payment_module_to_insurance_carriers_table',91);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (221,'2026_03_31_172116_add_call_type_to_qa_results',92);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2026_04_01_000001_add_assemblyai_fields_to_qa_calls_table',93);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (223,'2026_04_01_095059_add_exceptional_count_to_qa_daily_stats',94);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (224,'2026_04_01_100050_add_score_disposition_to_qa_results',95);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2026_04_02_000001_add_lead_id_to_qa_calls_table',96);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2026_04_02_100000_add_multi_part_audio_to_qa_calls',97);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (227,'2026_04_01_162402_add_in_review_to_leads_qa_status_enum',98);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (228,'2026_04_06_094758_add_not_paid_comment_to_leads_table',99);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (229,'2026_04_06_200000_add_ledger_link_to_leads_and_journal_entries',100);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (230,'2026_04_06_000001_add_extra_parts_to_qa_calls',101);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (231,'2026_04_06_210000_add_expense_accounts_to_chart_of_accounts',102);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (232,'2026_04_06_210001_add_sales_return_type_and_lead_return_link',102);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (233,'2026_04_07_000001_add_other_reason_to_not_issued_and_comment_column',103);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (234,'2026_04_07_000001_add_dnc_judge_to_qa_results',104);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (235,'2026_04_07_000000_add_other_reason_to_not_issued_disposition_and_comment_column',105);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (236,'2026_04_08_000001_add_retention_disposition_to_leads_table',105);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (237,'2026_04_08_000002_create_lead_field_highlights_table',105);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (238,'2026_04_07_162648_add_ledger_chargeback_paid_entry_id_to_leads_table',106);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (239,'2026_04_07_170551_add_chargeback_audit_fields_to_leads_table',107);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (240,'2026_04_08_102838_add_cb_retention_audit_to_leads_table',108);
