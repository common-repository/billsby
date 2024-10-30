<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.billsby.com
 * @since             1.0.0
 * @package           Billsby
 *
 * @wordpress-plugin
 * Plugin Name:       Billsby
 * Plugin URI:        https://www.billsby.com/product/integrations/wordpress
 * Description:       Advanced subscription management powered by Billsby. User detail synchronization, granular access control for paywalls, dunning, coupons and more.
 * Version:           1.2.0
 * Author:            Billsby
 * Author URI:        https://www.billsby.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       billsby
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('BILLSBY_VERSION', '1.1.0');
define('BILLSBY_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-billsby-activator.php
 */
function activate_billsby()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-billsby-activator.php';
    $activator = new Billsby_Activator();
    $activator->activate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-billsby-deactivator.php
 */
function deactivate_billsby()
{
    require_once BILLSBY_PLUGIN_PATH . 'includes/class-billsby-activator.php';
    $activator = new Billsby_Activator();
    
    require_once BILLSBY_PLUGIN_PATH . 'includes/class-billsby-deactivator.php';
    $deactivator = new Billsby_Deactivator($activator);
    $deactivator->deactivate();
}

register_activation_hook(__FILE__, 'activate_billsby');
register_deactivation_hook(__FILE__, 'deactivate_billsby');



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-billsby.php';



/* -------------------------------------------------------------------------- */
/*                     Initialize custom endpoint webhook                     */
/* -------------------------------------------------------------------------- */

function billsby_custom_api()
{
    // Initialize table names
    require_once plugin_dir_path(__FILE__) . 'includes/class-billsby-activator.php';
    $activator = new Billsby_Activator();
    $billsby_config_table_name = $activator->wp_billsby_table_config();
    $users_table_name = $activator->wp_users_table();
    $usermeta_table_name = $activator->wp_usermeta_table();
    $logs_table = $activator->wp_logs_table();
    global $wpdb;

    // Require Billsby webhook Class
    require_once plugin_dir_path(__FILE__) . 'includes/class-billsby-webhook-functions.php';
    $webhook_functions = new Billsby_Webhooks();

    // Get all headers and turn it to an array of object
    $headers=array();
    foreach (getallheaders() as $name => $value) {
        $headers[strtolower($name)] = $value;
    }

    // Get user config
    $user_config = $wpdb->get_row("SELECT * FROM ".$billsby_config_table_name." WHERE id = 1");

    // Check if correct secret key was used
    if ($headers['secret'] == $user_config->secret_key) {
        $result = (object) ["status"=>"Secret Key Match! Authorized access"];

        // Process POST request
        $json = file_get_contents("php://input");
        $post_data = json_decode($json);

        if (!$post_data) {
            $result->data = array("Message"=>"Triggered Webhook for Checking only" , "Info"=> "No sync functions done.");
            $response = new WP_REST_Response($result, 200);
            $response->set_headers(wp_get_nocache_headers());
            return $response;
        } else {
            // Set webhook_status to "1" (Online/True)
            if ($user_config->webhook_status !== "1") {
                $update_query = "UPDATE ".$billsby_config_table_name." SET webhook_status = 1 WHERE id = 1";
                $query_result = $wpdb->query($update_query);
                $result->log = "Changed webhook status. Webhook is now working";
            }
    
            // Check if account sync is turned on
            if ($user_config->account_sync == 1) {
                
             
    
                // Get user data
                $user_data = $wpdb->get_row("SELECT * FROM ".$users_table_name." WHERE user_email = '".$post_data->Email."'");
    
                // Do condition based on the type
                if ($post_data->Type == 'CustomerCreated' || $post_data->Type == 'CustomerUpdated') {
                    // If email exists, continue
                    if ($user_data) {
                        // Add or Update Customer Id
                        $add_or_update_customer_id =  $webhook_functions->billsby_add_or_update_customer_id($user_data->ID, $post_data->CustomerUniqueId);
                        var_dump($add_or_update_customer_id);
    
                        // Add or Update Subscription Ids
                        $add_or_update_subscription_data = $webhook_functions->billsby_add_or_update_subscription_data($user_data->ID, $post_data->SubscriptionIds);
                        var_dump($add_or_update_subscription_data);
    
        
                        // Update name if different from billsby
                        $update_name = $webhook_functions->billsby_update_customer_name($post_data->FirstName, $post_data->LastName, $user_data->ID);
                        var_dump($update_name);
    
                        if ($post_data->Type == 'CustomerUpdated') {
                            // Get feature tags if customer updated
                            $subscriptionUniqueId = $post_data->SubscriptionIds[0];
    
                            $get_or_update_feature_tags = $webhook_functions->billsby_update_feature_tags($user_data->ID, $user_config->company_id, $subscriptionUniqueId, $user_config->api_key);

                            $update_subscription_details = $webhook_functions->billsby_get_subscription_details($user_data->ID, $user_config->company_id, $subscriptionUniqueId);
                            var_dump($update_subscription_details);
                        }
                    }
                    // If email does not exist
                    else {
                        // AND Type is CustomerUpdated
                        if ($post_data->Type == 'CustomerUpdated') {
                            // Update email, name and subscription IDs using Customer ID if found in database
                            $user_metadata = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_customer_id' AND meta_value = '".$post_data->CustomerUniqueId."'");
                            if ($user_metadata) {
                                // Update email
                                $update_email = $webhook_functions->billsby_update_email($post_data->Email, $user_metadata->user_id);
                                var_dump($update_email);
            
    
                                //Update name if different from billsby
                                $update_name = $webhook_functions->billsby_update_customer_name($post_data->FirstName, $post_data->LastName, $user_metadata->user_id);
                                var_dump($update_name);
                                
    
                                // Update Subscription Ids
                                $add_or_update_subscription_data = $webhook_functions->billsby_add_or_update_subscription_data($user_metadata->user_id, $post_data->SubscriptionIds);
                                var_dump($add_or_update_subscription_data);
    
                                // Get feature tags if customer updated
                                $subscriptionUniqueId = $post_data->SubscriptionIds[0];
    
                                $get_or_update_feature_tags = $webhook_functions->billsby_update_feature_tags($user_data->ID, $user_config->company_id, $subscriptionUniqueId, $user_config->api_key);
                            } else {
                                $result->info = "Did not update any information, Email and Customer Id does not exist in database";
                            }
                        } else {
                            $result->info = "Did not update any information, Email does not exist in database";
                        }
                    }
                } elseif ($post_data->Type == 'CustomerDeleted') {
                    if ($user_data) {
                        $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = null WHERE meta_key = 'billsby_customer_id' AND user_id = ".$user_data->ID;
                        $query_result = $wpdb->query($update_query);
    
                        // Delete Subscription Data
                        $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '[]' WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$user_data->ID;
                        $query_result = $wpdb->query($update_query);
    
                        $result->data = array("Message"=>"Received Customer Deleted Notification. Deleted Customer Id and Subscription Data");
                    }
                } elseif ($post_data->Type == 'SubscriptionCreated') {
                    // Check Customer ID if exist or match with POST data
                    $usermeta_billsby_customerId =  $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_customer_id' AND meta_value = '".$post_data->CustomerUniqueId."'");
    
                    $company_domain = $user_config->company_id;
    
                    // If customer id exist; Update subscription ID, product name, plan name and status as appropriate
                    if ($usermeta_billsby_customerId) {
                        // Run webhook function for getting subscription details
                        $get_subscription_function = $webhook_functions->billsby_get_subscription_details($usermeta_billsby_customerId->user_id, $company_domain, $post_data->SubscriptionUniqueId);
                    }
                    // If customer ID does not exist:
                    else {
                        // Get customer details https://support.billsby.com/reference#get-customer-details
                        $get_customer_details_url = "https://public.billsby.com/api/v1/rest/core/" . $company_domain . "/customers/" . $post_data->CustomerUniqueId;
    
                        // Run webhook function - Call Billsby API to get customer details
                        $run_get_request = $webhook_functions->billsby_http_get_request($get_customer_details_url, $user_config->api_key);
                        
                        /* ---------------------- Do the customer created flow ---------------------- */
    
                        // Get user data using email of customer
                        $x_user_data = $wpdb->get_row("SELECT * FROM ".$users_table_name." WHERE user_email = '".$run_get_request['email']."'");
    
                        // Do this if user exist
                        if ($x_user_data) {
                            // Add or Update Customer Id
                            $add_or_update_customer_id =  $webhook_functions->billsby_add_or_update_customer_id($x_user_data->ID, $post_data->CustomerUniqueId);
                            var_dump($add_or_update_customer_id);
        
                            // Add or Update Subscription Ids
                            $add_or_update_subscription_data = $webhook_functions->billsby_add_or_update_subscription_data($x_user_data->ID, array($post_data->SubscriptionUniqueId));
                            var_dump($add_or_update_subscription_data);
        
                            // Update name if different from billsby
                            $update_name = $webhook_functions->billsby_update_customer_name($run_get_request['firstName'], $run_get_request['lastName'], $x_user_data->ID);
                            var_dump($update_name);
    
                            /* ----------------- Do the subscription created flow again ----------------- */
                            $get_subscription_function = $webhook_functions->billsby_get_subscription_details($x_user_data->ID, $company_domain, $post_data->SubscriptionUniqueId);
                        }
                    }
                } elseif ($post_data->Type == 'SubscriptionUpdated') {
                    $update_subscription_status = $webhook_functions->billsby_subscription_update_status($post_data->CustomerUniqueId, $post_data->SubscriptionUniqueId, $post_data->SubscriptionStatus);
                    var_dump($update_subscription_status);
                } else {
                    return new WP_Error('Webhook Error', 'Invalid Type detected', array( 'status' => 404 ));
                }
            } else {
                $result->data = array("Message"=>"Triggered Webhook!" , "Info"=> "No sync functions done.");
            }
            $response = new WP_REST_Response($result, 200);
            $response->set_headers(wp_get_nocache_headers());
        
            return $response;
        }
    } else {
        $update_query = "UPDATE ".$billsby_config_table_name." SET webhook_status = 2 WHERE id = 1";
        $query_result = $wpdb->query($update_query);
        $header_info = json_encode($headers);
        $log_title = "Webhook Error: Invalid Secret Key";
        $log_details = "The header info is as follows: ".$header_info;

        // Use WP site settings timezone
        $log_datetime = current_time('Y-m-d h:i:sa', false);

        // Use UTC or default timezone setting
        // $log_datetime = current_time('Y-m-d h:i:sa', true);

        $insert_log = "INSERT INTO ".$logs_table." (title, details, date) VALUES ('".$log_title."', '".$log_details."', '".$log_datetime."')";

        $query_result = $wpdb->query($insert_log);

        return new WP_Error('Authorization Error', 'Invalid secret key', array( 'status' => 404, 'secret_sent' => $headers['secret'] ));
    }
}

/*
*   Not used anymore, but can't be deleted atm, for reference.
*/
function billsby_get_info()
{
    global $wpdb;
    require_once plugin_dir_path(__FILE__) . 'includes/class-billsby-activator.php';
    $activator = new Billsby_Activator();
    $usermeta_table_name = $activator->wp_usermeta_table();

    $param_user_id = isset($_GET['user_id']) ? sanitize_text_field($_GET['user_id']) : null;
    if ($param_user_id) {
        $billsby_customerId = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_customer_id' AND user_id =".$param_user_id);
        $billsby_subscriptionData = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_subscription_data' AND user_id =".$param_user_id);
        $billsby_featureTags = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_feature_tag' AND user_id =".$param_user_id);
        $subscription_data  = json_decode($billsby_subscriptionData->meta_value);
        $feature_tags  = json_decode($billsby_featureTags->meta_value);
        $result = array("billsby_customerId"=> $billsby_customerId->meta_value, "billsby_subscriptionData"=>$subscription_data, "billsby_featureTags" => $feature_tags);
        $response = new WP_REST_Response($result, 200);
    
        $response->set_headers(wp_get_nocache_headers());
    
        return $response;
    } else {
        return new WP_Error('GET Request Error', 'No queries found', array( 'status' => 404 ));
    }
}

function billsby_get_feature_tag()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-billsby-webhook-functions.php';

    $param_user_id = isset($_GET['user_id']) ? sanitize_text_field($_GET['user_id']) : null;
    $webhook_functions = new Billsby_Webhooks();
    $api_result = $webhook_functions->billsby_get_feature_tags($param_user_id);
    $response = new WP_REST_Response($api_result, 200);

    return $response;
}

/* ----------------------- WP hook for billsby webhook ---------------------- */
add_action('rest_api_init', function () {
    register_rest_route('billsby/', 'endpoint', [
        'methods'=> 'POST',
        'callback'=> 'billsby_custom_api'
        ]);
});


/* ----------- WP hook for getting billsby info using API request ----------- */
add_action('rest_api_init', function () {
    register_rest_route('billsby/', 'get-billsby-info', [
        'methods'=> 'GET',
        'callback'=> 'billsby_get_info'
        ]);
});

/* ----------- WP hook for getting billsby feature tag using API request ----------- */
add_action('rest_api_init', function () {
    register_rest_route('billsby/', 'get-feature-tag', [
        'methods'=> 'GET',
        'callback'=> 'billsby_get_feature_tag'
        ]);
});


/* -------------------------------------------------------------------------- */
/*                             Update Profile Hook                            */
/* -------------------------------------------------------------------------- */

function my_profile_update($user_id)
{
    global $wpdb;
    require_once plugin_dir_path(__FILE__) . 'includes/class-billsby-activator.php';
    $activator = new Billsby_Activator();

    // Initialize the table names
    $billsby_table_config = $activator->wp_billsby_table_config();
    $usermeta_table_name = $activator->wp_usermeta_table();
    $user_table_name = $activator->wp_users_table();
    
    if (current_user_can('edit_user', $user_id)) {
        // Get Company Id and customer Id
        $billsby_config =  $wpdb->get_row("SELECT * FROM ".$billsby_table_config." WHERE id = 1");
        $usermeta_customer_id =  $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_customer_id' AND user_id = ".$user_id);

        // The billsby URL
        $billsby_customer_url = "https://public.billsby.com/api/v1/rest/core/" . $billsby_config->company_id . "/customers/" . $usermeta_customer_id->meta_value;

        // Run Sync if account sync is turned on
        if ($billsby_config->account_sync == 1) {
            // Get old info of user
            $billsby_response = wp_remote_get($billsby_customer_url, array(
                'headers' => array(
                    'apikey' =>  $billsby_config->api_key
                )
            ));
            $billsby_customer_result = json_decode(wp_remote_retrieve_body($billsby_response), true);
    
            // Get current First, Last Name and Email
            $usermeta_firstname =  $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'first_name' AND user_id = ".$user_id);
            $usermeta_lastname =  $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'last_name' AND user_id = ".$user_id);
            $usermeta =  $wpdb->get_row("SELECT * FROM ".$user_table_name." WHERE ID = ".$user_id);
    
            // Change First name, Last name, and Email to New update
            $billsby_customer_result['firstName'] = $billsby_customer_result['firstName'] != $usermeta_firstname->meta_value ? $usermeta_firstname->meta_value : $billsby_customer_result['firstName'];
            $billsby_customer_result['lastName'] = $billsby_customer_result['lastName'] != $usermeta_lastname->meta_value ? $usermeta_lastname->meta_value : $billsby_customer_result['lastName'];
            $billsby_customer_result['email'] = $billsby_customer_result['email'] != $usermeta->user_email ? $usermeta->user_email : $billsby_customer_result['email'];
    
            $billsby_customer_result = json_encode($billsby_customer_result);
    
            // Run Put Request to save changes to billsby
            $wp_args = array(
                'method' => 'PUT',
                'headers' => array(
                    'ApiKey' => $billsby_config->api_key,
                    'Content-Type' => 'application/json'
                ),
                'body' =>  $billsby_customer_result
            );
            $billsby_update_customer = wp_remote_request($billsby_customer_url, $wp_args);
    
            // Uncomment code below if you want to do something about the result of the PUT request
            
            // if (!is_wp_error($billsby_update_customer) && ($billsby_update_customer['response']['code'] == 200 || $billsby_update_customer['response']['code'] == 201)) {
            //     $update_result = json_decode(wp_remote_retrieve_body($billsby_update_customer), true);
            //     $update_result = json_encode($update_result);
            // } else {
            //     $update_result = json_encode($wp_args);
            // }
        }
    }
}

/* ---------------------- WP hook when profile updates ---------------------- */
add_action('profile_update', 'my_profile_update');


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_billsby()
{
    $plugin = new Billsby();
    $plugin->run();
}
run_billsby();
