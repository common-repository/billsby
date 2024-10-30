<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.billsby.com
 * @since      1.0.0
 *
 * @package    Billsby
 * @subpackage Billsby/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Billsby
 * @subpackage Billsby/includes
 * @author     Billsby <hello@billsby.com>
 */
class Billsby_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */

    private $table_activator;

    public function __construct($activator)
    {
        $this->table_activator = $activator;
    }
    public function deactivate()
    {
        global $wpdb;

        // dropping table upon plugin uninstall
        $wpdb->query("DROP TABLE IF EXISTS ".$this->table_activator->wp_billsby_table_config());
        $wpdb->query("DROP TABLE IF EXISTS ".$this->table_activator->wp_logs_table());
        
        // remove added option from wp_options table
        delete_option('billsby_insert_header');
        
        // Delete billsby details in usermeta table
        // $wpdb->query("DELETE FROM ".$this->table_activator->wp_usermeta_table()." WHERE meta_key IN ('billsby_customer_id', 'billsby_subscription_id', 'billsby_subscription_product_name', 'billsby_subscription_plan_name', 'billsby_subscription_status' )");
        $wpdb->query("DELETE FROM ".$this->table_activator->wp_usermeta_table()." WHERE meta_key IN ('billsby_customer_id', 'billsby_subscription_data')");

        // delete post meta
        $table = $wpdb->prefix.'postmeta';
        $wpdb->delete($table, array('meta_key' => 'billsby_must_have_all_tags'));
        $wpdb->delete($table, array('meta_key' => 'billsby_feature_tags'));
    }
}
