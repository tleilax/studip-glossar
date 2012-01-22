<?php
class CreateDb extends DBMigration {

    public function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `glossar` (
            `context` CHAR(32) NOT NULL DEFAULT 'global',
            `glossar_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `term` VARCHAR(128) NOT NULL,
            `description` TEXT NOT NULL,
            `chdate` INT(11) UNSIGNED NOT NULL,
            `chuserid` CHAR(32) NOT NULL,
            PRIMARY KEY (`context`, `glossar_id`)
        )");
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `glossar_categories` (
            `context` CHAR(32) NOT NULL DEFAULT 'global',
            `category_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `category` VARCHAR(128) NOT NULL,
            `description` TEXT NULL DEFAULT NULL,
            `chdate` INT(11) UNSIGNED NOT NULL,
            `chuserid` CHAR(32) NOT NULL,
            PRIMARY KEY (`context`, `category_id`)
        )");
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `glossar_lists` (
            `context` CHAR(32) NOT NULL DEFAULT 'global',
            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `glossar_id` INT(10) UNSIGNED NOT NULL,
            `category_id` INT(10) UNSIGNED NOT NULL,
            `chdate` INT(11) UNSIGNED NOT NULL,
            `chuserid` CHAR(32) NOT NULL,
            PRIMARY KEY (`context`, `id`),
            UNIQUE KEY `list_items` (`context`, `glossar_id`, `category_id`)
        )");
        DBManager::get()->exec("CREATE TABLE `glossar_context` (
            `context` CHAR(32) NOT NULL DEFAULT 'global',
            `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
            `collapsable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
            `open` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
            `public` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
            `chdate` INT(11) UNSIGNED NOT NULL,
            `chuserid` CHAR(32) NOT NULL,
            PRIMARY KEY (`context`)
        );")
    }

    function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `glossar`, `glossar_categories`, `glossar_lists`, `glossar_context`");
    }

}
