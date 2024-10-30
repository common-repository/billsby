<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.billsby.com
 * @since      1.0.0
 *
 * @package    Billsby
 * @subpackage Billsby/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Billsby
 * @subpackage Billsby/admin
 * @author     Billsby <hello@billsby.com>
 */
class Billsby_Admin
{

    // get billsby config table name
    private $config_config_table_name;

    // get usermeta table name
    private $umeta_config_table_name;
    

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     * @param      string    $version    The config table name
     * @param      string    $version    The umeta table name
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        require_once plugin_dir_path(__DIR__) . 'includes/class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $this->config_table_name = $activator->wp_billsby_table_config();

        $this->umeta_config_table_name = $activator->wp_usermeta_table();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Billsby_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Billsby_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/billsby-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Billsby_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Billsby_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.s
         */

        // wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/billsby-admin.js', array( 'jquery' ), $this->version, false );

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/billsby-admin.js', array( 'jquery' ), $this->version, true);
        
        wp_enqueue_script("billsby-usermeta-script", plugin_dir_url(__FILE__) . 'js/billsby-usermeta.js', array( 'jquery' ), $this->version, true);

        wp_enqueue_script("billsby-postmeta-script", plugin_dir_url(__FILE__) . 'js/billsby-postmeta.js', array( 'jquery' ), $this->version, true);
        
        wp_enqueue_script("billsby-settings-script", plugin_dir_url(__FILE__) . 'js/billsby-settings-script.js', array( 'jquery' ), $this->version, true);
        
        wp_localize_script($this->plugin_name, "billsby_requests", array(
            "ajaxurl" => admin_url("admin-ajax.php"),
        ));

        // //Ajax url for getting
        // wp_localize_script($this->plugin_name, "billsby_requests", array(
        // 	"ajaxurl" => admin_url("admin-ajax.php"),
        // ));
    }

    
    

    // create menu methood
    public function billsy_plugin_menu()
    {
        global $wpdb;
        $user_config = $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");


        //add main menu
        add_menu_page("Billsby Settings", "Billsby", "manage_options", "billsby-plugin-settings", array($this, "billsby_plugin_settings"), plugins_url('billsby/admin/assets/Billsby_Logo_White.png'));

        // create sub menu - settings
        add_submenu_page("billsby-plugin-settings", "Settings", "Settings", "manage_options", "billsby-plugin-settings", array($this, "billsby_plugin_settings"));

        if ($user_config->setup_complete == 1) {
            // create sub menu - buttons
            add_submenu_page("billsby-plugin-settings", "Buttons", "Buttons", "manage_options", "billsby-plugin-buttons", array($this, "billsby_plugin_buttons"));
            
            // create sub menu - account synchronization
            add_submenu_page("billsby-plugin-settings", "Account Sync", "Account Sync", "manage_options", "billsby-plugin-account-synchronization", array($this, "billsby_plugin_account_synchronization"));
    
            // create sub menu - access control
            add_submenu_page("billsby-plugin-settings", "Access Control", "Access Control", "manage_options", "billsby-plugin-access-control", array($this, "billsby_plugin_access_control"));
        }
    }

    // menu callback function
    public function billsby_plugin_settings()
    {
        global $wpdb;
        $user_config = $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");
        $permalink_structure = get_option('permalink_structure');

        ob_start(); // start buffer

        include_once(BILLSBY_PLUGIN_PATH."admin/partials/template-settings.php"); // include template file

        $template = ob_get_contents(); // reading content
        ob_end_clean(); // closing and cleaning buffer

        echo $template;
    }

    public function billsby_plugin_buttons()
    {
        ob_start(); // start buffer

        include_once(BILLSBY_PLUGIN_PATH."admin/partials/template-buttons.php"); // include template file

        $template = ob_get_contents(); // reading content
        ob_end_clean(); // closing and cleaning buffer

        echo $template;
    }

    public function billsby_plugin_account_synchronization()
    {
        global $wpdb;
        $user_config = $wpdb->get_row("SELECT logout_url, account_sync FROM ".$this->config_table_name." WHERE id = 1");
        
        ob_start(); // start buffer

        include_once(BILLSBY_PLUGIN_PATH."admin/partials/template-account-sync.php"); // include template file

        $template = ob_get_contents(); // reading content
        ob_end_clean(); // closing and cleaning buffer

        echo $template;
    }

    public function billsby_plugin_access_control()
    {
        global $wpdb;
        $user_config = $wpdb->get_row("SELECT access_control, restriction_message FROM ".$this->config_table_name." WHERE id = 1");
        ob_start(); // start buffer

        include_once(BILLSBY_PLUGIN_PATH."admin/partials/template-access-control.php"); // include template file

        $template = ob_get_contents(); // reading content
        ob_end_clean(); // closing and cleaning buffer

        echo $template;
    }

    // public function inject_header_code()
    // {
    //     // Get meta
    //     $meta = get_option('billsby_insert_header');
    //     if (empty($meta)) {
    //         return;
    //     }
    //     if (trim($meta) == '') {
    //         return;
    //     }

    //     // Output
    //     echo wp_unslash($meta);
    // }

    // public function register_settings()
    // {
    //     register_setting($this->plugin_name, 'billsby_insert_header', 'trim');
    // }

    public $settings;

    /**
     * Get meta box settings and fields
     *
     * @return array
     */
    private function billsby_get_metabox()
    {
        $fields = array(
            'id'       => 'billsby_meta_box',
            'title'    => __('Billsby Access Control', 'billsby'),
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => array(
                array(
                    'name'    => __('Feature Tags', 'billsby'),
                    'id'      => 'billsby_feature_tags',
                    'type'    => 'text',
                    'desc'    => __('Restrict this entire page or post only to people with the following feature tags as part of their plan', 'billsby'),
                ),
                array(
                    'name' => __('Must have all tags?', 'billsby'),
                    'id'   => 'billsby_must_have_all_tags',
                    'type' => 'checkbox',
                    'desc' => __('The user must have all of the above feature tags to see the content', 'billsby'),
                )
            )
        );

        return apply_filters('billsby_metabox_fields', $fields);
    }

    /**
     * Render meta box
     *
     * @return void
     */
    public function billsby_show_meta_box()
    {
        global $post;

        $metabox = $this->billsby_get_metabox();

        echo '<div class="billsby-meta-box" id="js-billsby-meta-box">';

        echo '<div class="billsby-meta-box-title">'.$metabox['title'].'</div>';

        // Use nonce for verification
        echo '<input type="hidden" name="billsby_meta_nonce" value="' . esc_attr(wp_create_nonce(basename(__FILE__))) . '" />';
        echo '<div class="billsby-meta-box-content">';
        foreach ($metabox['fields'] as $field) {
            
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);

            // If there are no existing meta, convert result to empty array.
            if (!$meta) {
                $meta = [];
            }

            // hidden fields
            if ($field['type'] === 'text') {
                for ($i = 0; $i < count($meta); $i++) {
                    echo '<input type="hidden" class="js-feature-tagss" name="billsby_feature_tags[]" value="'.$meta[$i].'" />';
                }
            }

            echo '<div class="billsby-form-group">';
            switch ($field['type']) {
                case 'text':
                    echo '<div class="billsby-input-description">'.esc_html($field['desc']).'</div>';
                    echo '<div class="billsby-input-holder billsby-input-tag">';
                    echo '<input type="text" class="feature-tag-input" id="js-feature-tag-input" placeholder="featuretag" />';
                    echo '<button class="feature-tag-btn" id="js-add-tag-btn">Add feature tag</button>';
                    echo '</div>';

                    if (count($meta) > 0) {
                        $tag_wrapper_class = 'feature-tags-wrapper';
                    } else {
                        $tag_wrapper_class = '';
                    }
                    echo '<div class="' . esc_attr($tag_wrapper_clas). '" id="js-feature-tags-wrapper">';
                    for ($i = 0; $i < count($meta); $i++) {
                        echo '<div class="billsby-feature-tag"><span class="billsby-remove-tag">X</span><div>'.$meta[$i].'</div></div>';
                    }
                    echo '</div>';

                    break;
                case 'checkbox':
                    echo '<div class="billsby-input-holder">';
                    echo '<input type="checkbox" name="' . esc_attr($field['id']). '" id="' . esc_attr($field['id']) . '"' . checked('on', $meta, false) . ' />';
                    echo '<label for="'.esc_attr($field['id']).'">'.esc_html($field['desc']).'</label>';
                    echo '</div>';
                    break;
            }
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * Add meta box to supported post types
     *
     * @return void
     */
    public function billsby_add_meta_boxes()
    {
        global $wpdb;

        $billsby_config =  $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");

        // don't add meta boxes if access control is not on
        if (!$billsby_config->access_control) {
            return;
        }

        $metabox = $this->billsby_get_metabox();

        $post_types = get_post_types(array( 'public' => true, 'show_ui' => true ), 'objects');
        foreach ($post_types as $page) {
            $exclude = apply_filters('billsby_metabox_excluded_post_types', array(
                'forum',
                'topic',
                'reply',
                'product',
                'attachment'
            ));

            if (! in_array($page->name, $exclude)) {
                add_meta_box($metabox['id'], 'Billsby', array( $this, 'billsby_show_meta_box' ), $page->name, $metabox['context'], $metabox['priority']);
            }
        }
    }

    /**
     * Save meta box data
     *
     * @param int $post_id
     *
     * @return void
     */
    public function billsby_save_data($post_id)
    {
        if (empty($_POST['billsby_meta_nonce'])) {
            return;
        }

        // verify nonce
        if (! wp_verify_nonce($_POST['billsby_meta_nonce'], basename(__FILE__))) {
            return;
        }

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // check permissions
        if ('page' == $_POST['post_type']) {
            if (! current_user_can('edit_page', $post_id)) {
                return;
            }
        } elseif (! current_user_can('edit_post', $post_id)) {
            return;
        }

        $metabox = $this->billsby_get_metabox();

        foreach ($metabox['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
        
            if ($field['type'] === 'text') {
                // $new = isset( $_POST[ $field['id'] ] ) ? sanitize_text_field( $_POST[ $field['id'] ] ) : array();
                update_post_meta($post_id, $field['id'], array_map('sanitize_text_field', $_POST[ $field['id'] ]));
            }

            if ($field['type'] === 'checkbox') {
                $new = isset($_POST[ $field['id'] ]) ? sanitize_text_field($_POST[ $field['id'] ]) : '';
                if ($new && $new != $old) {
                    update_post_meta($post_id, $field['id'], $new);
                } elseif ('' == $new && $old) {
                    delete_post_meta($post_id, $field['id'], $old);
                }
            }
        }
    }

    public function handle_ajax_request_admin()
    {
        //handles all ajax request of admin

        global $wpdb;

        // echo $_REQUEST['data']['company_id'];
        $param = isset($_REQUEST['param']) ? sanitize_key($_REQUEST['param']) : "";
        
        if (!empty($param)) {
    
            // SETUP BILLSBY ACCOUNT
            if ($param == "complete_billsby_setup") {
                // Map requests to variables
                $company_id = $_REQUEST['data']['company_id'] != '' ? sanitize_text_field($_REQUEST['data']['company_id']) : null;
                $api_key = $_REQUEST['data']['api_key'] != '' ? sanitize_text_field($_REQUEST['data']['api_key']) : null;
                $account_syncronization = $_REQUEST['data']['checkbox_account_syncronization'] == 'true' ? 1 : 0;
                $access_control = $_REQUEST['data']['checkbox_access_control'] == 'true' ? 1 : 0;
                $subscribe_manage_account = $_REQUEST['data']['checkbox_subscribe_and_manage'] == 'true' ?  1 : 0;

                if ($company_id !== null && $api_key !== null) {
                    $update_query = "UPDATE ".$this->config_table_name." SET company_id ='".$company_id."', api_key='".$api_key."' , access_control=".$access_control.", account_sync=".$account_syncronization.",  subscribe_manage_account=".$subscribe_manage_account.", setup_complete = 1 WHERE id = 1";
    
                    $result = $wpdb->query($update_query);
    
                    if ($result > 0 || $result !== 0) {
    
                        // update option to reflect the new companyid on the header code
                        // update_option('billsby_insert_header', '<script src="https://checkoutlib.billsby.com/checkout.min.js" data-billsby-company="'.$company_id.'"></script>');
    
                        echo json_encode(array(
                            "status" => 'success',
                            "message" => "Updated plugin config.",
                            "data" => array($company_id, $api_key, $checkbox_access_control, $checkbox_account_syncronization, $checkbox_subscribe_and_manage)
                        ));
                    }
                } else {
                    echo json_encode(array(
                        "status" => 'error',
                        "message" => "Failed: Check company ID and API key if they're not null.",
                        "data" => array($company_id, $api_key, $checkbox_access_control, $checkbox_account_syncronization, $checkbox_subscribe_and_manage)
                    ));
                }
            }
            // UPDATE ACCOUNT SYNC OPTION
            elseif ($param == "update_billsby_account_settings") {
                $logout_url =  $_REQUEST['data']['logout_url'] != '' ? sanitize_text_field($_REQUEST['data']['logout_url']) : null;
                $account_sync = $_REQUEST['data']['account_sync'] == "true" ? 1 : 0;

                $table = $this->config_table_name;
                $data = array(
                    'logout_url'   => $logout_url,
                    'account_sync' => $account_sync
                );
                $where = array( 'id' => 1 );

                // $update_query = "UPDATE ".$this->config_table_name." SET logout_url ='".$logout_url."', account_sync='".$account_sync."' WHERE id = 1";
                $result = $wpdb->update($table, $data, $where);

                if ($result) {
                    $message = 'Updated Account Sync Settings';
                    if ($logout_url == null) {
                        $message = "Updated Account Sync Settings. But Logout URL is null";
                    }
                    echo json_encode(array(
                        "status" => 'success',
                        "message" => $message,
                        "data" => [
                            "logout_url" => $logout_url,
                            "account_sync" => $account_sync,
                            "result" => $result
                        ]
                    ));
                } else {
                    $message = 'Account Sync Settings Not Updated';
                    echo json_encode(array(
                        "status" => 'error',
                        "message" => $message,
                        "data" => [
                            "logout_url" => $logout_url,
                            "account_sync" => $account_sync,
                            "result" => $result
                        ]
                    ));
                }
            }
            // UPDATE ACCESS CONTROL OPTION
            // elseif ($param == "update_billsby_access_control") {
            //     $access_control = $_REQUEST['data']['access_control'] == "true" ? 1 : 0;
            //     $account_sync = $_REQUEST['data']['account_sync'] == "true" ? 1 : 0;

            //     $table = $this->config_table_name;
            //     $data = array('access_control' => $access_control);

            //     if ( $account_sync ) {
            //         $data['account_sync'] = 1;
            //     }

            //     $where = array( 'id' => 1 );

            //     // $update_query = "UPDATE ".$this->config_table_name." SET access_control=".$access_control." ".getQueryData()."  WHERE id = 1";
            //     $result = $wpdb->update( $table, $data, $where );

            //     if ($result !== false) {
            //         $message = $access_control == 1 ? "Access Control is turned ON" : "Access Control is turned OFF";

            //         echo json_encode(array(
            //             "status" => 'success',
            //             "message" => $message,
            //             "data" => [
            //                 "access_control" => $access_control
            //             ]
            //         ));
            //     }
            // }
            // Update Restriction Message
            // elseif ($param == "update_restriction_message") {
            //     $restriction_message = $_REQUEST['data']['restriction_message'];
            //     $update_query = "UPDATE ".$this->config_table_name." SET restriction_message=".json_encode($restriction_message)." WHERE id = 1";
            //     $result = $wpdb->query($update_query);
            //     if ($result === false) {
            //         echo json_encode(array(
            //         "status" => 'error',
            //         "message"=> "Error performing function"
            //     ));
            //     } else {
            //         $message = "Updated Restriction Message to: *".$restriction_message."*";

            //         echo json_encode(array(
            //         "status" => 'success',
            //         "message" => $message,
            //         "data" => [
            //             "restriction_message" => $restriction_message
            //         ]
            //     ));
            //     }
            // }
            elseif ($param == "update_billsby_access_settings") {
                $restriction_message =  $_REQUEST['data']['restriction_message'] != '' ? sanitize_text_field($_REQUEST['data']['restriction_message']) : null;
                $access_control = $_REQUEST['data']['access_control'] == "true" ? 1 : 0;
                $account_sync = $_REQUEST['data']['account_sync'] == "true" ? 1 : 0;

                $table = $this->config_table_name;
                $data = array(
                    'restriction_message' => $restriction_message,
                    'access_control'      => $access_control
                );
                if ($account_sync) {
                    $data['account_sync'] = 1;
                }
                $where = array( 'id' => 1 );

                $result = $wpdb->update($table, $data, $where);

                if ($result) {
                    $message = 'Updated Access Control Settings';
                    if ($restriction_message == null) {
                        $message = "Updated Access Control Settings. But restriction message is null";
                    }
                    echo json_encode(array(
                        "status" => 'success',
                        "message" => $message,
                        "data" => [
                            "restriction_message" => $restriction_message,
                            "access_control" => $access_control,
                            "result" => $result
                        ]
                    ));
                } else {
                    $message = 'Access Control Settings Not Updated';
                    echo json_encode(array(
                        "status" => 'error',
                        "message" => $message,
                        "data" => [
                            "restriction_message" => $restriction_message,
                            "access_control" => $access_control,
                            "result" => $result
                        ]
                    ));
                }
            }

            // Check Webhook status
            elseif ($param == "check_billsby_webhook_status") {
                $user_config = $wpdb->get_row("SELECT webhook_status FROM ".$this->config_table_name." WHERE id = 1");
                
                echo json_encode(array("webhook_status" => $user_config->webhook_status));
            }
            // Disconnect WP to billsby
            elseif ($param == "disconnect_to_billsby") {
                $update_query = "UPDATE ".$this->config_table_name." SET company_id = NULL, api_key = NULL , webhook_status = 0, logout_url = NULL,  access_control = 0, account_sync = 0,  restriction_message = NULL, subscribe_manage_account = 0, setup_complete = 0 WHERE id = 1";
                
                $result = $wpdb->query($update_query);

                if ($result > 0 || $result !== 0) {

                    // update option to reflect the new companyid on the header code
                    // update_option('billsby_insert_header', '<script src="https://checkoutlib.billsby.com/checkout.min.js" data-billsby-company="'.$company_id.'"></script>');

                    // Disconnect query
                    echo json_encode(array(
                        "status" => 'success',
                        "message" => "Disconnected to Billsby!",
                    ));
                } else {
                    echo json_encode(array(
                        "status" => 'error',
                        "message"=> "Can't disconnect to billsby."
                    ));
                }
            }
            // Get Billsby Metadata
            elseif ($param == "get_billsby_meta") {
                $user_id =  $_REQUEST['data']['user_id'] != '' ? sanitize_text_field($_REQUEST['data']['user_id']) : null;

                if ($user_id) {
                    $billsby_customerId = $wpdb->get_row("SELECT * FROM ".$this->umeta_config_table_name." WHERE meta_key = 'billsby_customer_id' AND user_id =".$user_id);
                    
                    $billsby_subscriptionData = $wpdb->get_row("SELECT * FROM ".$this->umeta_config_table_name." WHERE meta_key = 'billsby_subscription_data' AND user_id =".$user_id);
                    
                    $billsby_featureTags = $wpdb->get_row("SELECT * FROM ".$this->umeta_config_table_name." WHERE meta_key = 'billsby_feature_tag' AND user_id =".$user_id);
    
                    $subscription_data  = json_decode($billsby_subscriptionData->meta_value);

                    $feature_tags  = json_decode($billsby_featureTags->meta_value);
                 
                    
                    $result = array("billsby_customerId"=> $billsby_customerId->meta_value, "billsby_subscriptionData"=>$subscription_data, "billsby_featureTags" => $feature_tags);

                    echo json_encode($result);
                } else {
                    echo json_encode(array(
                        "status" => 'error',
                        "message"=> "Error performing function"
                    ));
                }
            } else {
                echo json_encode(array(
                    "status" => 'error',
                    "message"=> "Error performing function"
                ));
            }
        }

        // Get latest settings
        // $this->settings = array(
        //     'billsby_insert_header' => esc_html(wp_unslash(get_option('billsby_insert_header')))
        // );

        wp_die();
    }
}
