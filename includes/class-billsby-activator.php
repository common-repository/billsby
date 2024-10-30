<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.billsby.com
 * @since      1.0.0
 *
 * @package    Billsby
 * @subpackage Billsby/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Billsby
 * @subpackage Billsby/includes
 * @author     Billsby <hello@billsby.com>
 */
class Billsby_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    
    public function activate()
    {
        global $wpdb;
        if ($wpdb->get_var("SHOW tables like '".$this->wp_billsby_table_config()."'") != $this->wp_billsby_table_config()) {
            // dynamic table generating code...
            $table_query = "CREATE TABLE `".$this->wp_billsby_table_config()."` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`secret_key` varchar(150) DEFAULT NULL,
							`company_id` varchar(150) DEFAULT NULL,
							`api_key` varchar(150) DEFAULT NULL,
							`webhook_status` int DEFAULT '0',
                            `subscribe_manage_account` int DEFAULT '0',
                            `setup_complete` int DEFAULT '0',
							`logout_url` varchar(150) DEFAULT NULL,
							`account_sync` int DEFAULT '0',
							`access_control` int DEFAULT '0',
							`restriction_message` varchar(500) DEFAULT NULL,
							PRIMARY KEY (`id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"; //table create query
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            dbDelta($table_query);

            $logs_table_query = "CREATE TABLE `".$this->wp_logs_table()."` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `title` varchar(150) DEFAULT NULL,
                                `details` varchar(500) DEFAULT NULL,
                                `date` varchar(150) DEFAULT NULL,
                                PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"; //table create query

            dbDelta($logs_table_query);

            // generate and insert secret key
            $secret_key = $this->generateKey();
            $insert_query = "INSERT INTO ".$this->wp_billsby_table_config()." (secret_key) VALUES ('".$secret_key."')";
            $wpdb->query($insert_query);

            // insert billsby data in usermeta table
            $insert_query = "INSERT INTO ".$this->wp_usermeta_table()." (user_id, meta_key,meta_value) VALUES (1, 'billsby_customer_id', null), (1, 'billsby_subscription_data', '[]')";
                      
            $wpdb->query($insert_query);
        }
    }

    public function wp_billsby_table_config()
    {
        global $wpdb;
        return $wpdb->prefix."billsby_table_config";
    }

    public function wp_usermeta_table()
    {
        global $wpdb;
        return $wpdb->prefix."usermeta";
    }

    public function wp_users_table()
    {
        global $wpdb;
        return $wpdb->prefix."users";
    }

    public function wp_logs_table()
    {
        global $wpdb;
        return $wpdb->prefix."billsby_webhook_logs";
    }

    private function generateKey()
    {
        $n = 20;
        $result = bin2hex(random_bytes($n));
        return $result;
    }
}
