<?php
if(!is_dir('./db'))
    mkdir('./db');
if(!defined('db_file')) define('db_file','./db/cashiering_db.db');
function my_udf_md5($string) {
    return md5($string);
}
date_default_timezone_set('UTC');
Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
         $this->open(db_file);
         $this->createFunction('md5', 'my_udf_md5');
         $this->exec("PRAGMA foreign_keys = ON;");
         $this->exec("CREATE TABLE IF NOT EXISTS `user_list` (
            `user_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` INTEGER NOT NULL,
            `status` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 
         $this->exec("CREATE TABLE IF NOT EXISTS product_list (
            `product_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `product_code` TEXT NOT NULL,
            `name` TEXT NOT NULL,
            `unit` TEXT NOT NULL,
			`quantity` INTEGER NOT NULL,
            `description` INTEGER NOT NULL,
			`expiry_date` TEXT NOT NULL,
            `price` REAL NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1,
            `date_created` TEXT NOT NULL,
            `date_updated` TIMESTAMP NULL
        )");
        // Product Comment---------------exxpiry date
        // Status: 1 = for Active Products and 2 = for In Active Products

        $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_prod AFTER UPDATE on `product_list`
        BEGIN
            UPDATE `product_list` SET date_updated = CURRENT_TIMESTAMP where product_id = product_id;
        END
        ");

        $this->exec("CREATE TABLE IF NOT EXISTS transaction_list (
            `transaction_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `or_number` TEXT NOT NULL,
            `customer_name` TEXT NOT NULL,
            `total_amount` REAL NOT NULL DEFAULT 0,
            `amount_tendered` REAL NOT NULL DEFAULT 0,
            `amount_change` REAL NOT NULL DEFAULT 0,
            `discount_percentage` REAL NOT NULL DEFAULT 0,
            `discount_amount` REAL NOT NULL DEFAULT 0,
            `date_created` TEXT NOT NULL,
            `date_updated` TIMESTAMP NULL,
            `user_id` INTEGER NOT NULL,
            FOREIGN KEY(`user_id`) REFERENCES `user_list`(`user_id`)
        )");

        $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_tans AFTER UPDATE on `transaction_list`
        BEGIN
            UPDATE `transaction_list` SET date_updated = CURRENT_TIMESTAMP where transaction_id = transaction_id;
        END
        ");

        $this->exec("CREATE TABLE IF NOT EXISTS `items` (
            `transaction_id` INTEGER NOT NULL,
            `product_id` INTEGER NOT NULL,
            `unit` TEXT NOT NULL,
            `quantity` TEXT NOT NULL,
            `price` TEXT NOT NULL,
            `date_created` TEXT NOT NULL,
            FOREIGN KEY(`transaction_id`) REFERENCES `transaction_list`(`transaction_id`) ON DELETE CASCADE,
            FOREIGN KEY(`product_id`) REFERENCES `product_list`(`product_id`) ON DELETE CASCADE
        )");  
         
       
        $this->exec("INSERT or IGNORE INTO `user_list` VALUES (1,'Administrator','admin',md5('admin123'),1,1, CURRENT_TIMESTAMP)");
        
    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();