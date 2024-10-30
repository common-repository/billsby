<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.billsby.com
 * @since      1.0.0
 *
 * @package    Billsby
 * @subpackage Billsby/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Billsby
 * @subpackage Billsby/includes
 * @author     Billsby <hello@billsby.com>
 */
class Billsby
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Billsby_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The table name of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $config_table_name;

    /**
     * The table name of the WP user meta table.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $umeta_table_name;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('BILLSBY_VERSION')) {
            $this->version = BILLSBY_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'billsby';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Billsby_Loader. Orchestrates the hooks of the plugin.
     * - Billsby_i18n. Defines internationalization functionality.
     * - Billsby_Admin. Defines all hooks for the admin area.
     * - Billsby_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-billsby-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-billsby-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-billsby-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-billsby-public.php';

        $this->loader = new Billsby_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Billsby_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Billsby_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Billsby_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // action hook for admin menu
        $this->loader->add_action('admin_menu', $plugin_admin, 'billsy_plugin_menu');

        // action hook for admin init
        // $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');

        // action hook for header code
        // $this->loader->add_action('wp_head', $plugin_admin, 'inject_header_code');
        
        // action hook for ajax
        $this->loader->add_action("wp_ajax_admin_ajax_request", $plugin_admin, 'handle_ajax_request_admin');

        // action hook for billsby meta box
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'billsby_add_meta_boxes');

        $this->loader->add_action('save_post', $plugin_admin, 'billsby_save_data');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        global $wpdb;

        $plugin_public = new Billsby_Public($this->get_plugin_name(), $this->get_version());
        $activator = new Billsby_Activator();
        $table_config = $activator->wp_billsby_table_config();
        // billsby config
        $billsby_config =  $wpdb->get_row("SELECT * FROM ".$table_config." WHERE id = 1");
        $is_setup_complete = $billsby_config->setup_complete;

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        if ($is_setup_complete) {
            // add shortcodes to the list of shortcodes registered to WordPress
            $this->loader->add_shortcode('billsby-subscribe', $plugin_public, 'load_subscribe_button');
            $this->loader->add_shortcode('billsby-account', $plugin_public, 'load_manage_account_button');
            $this->loader->add_shortcode('billsby-restrict', $plugin_public, 'billsby_restrict_content_sc');

            // action hook for header script
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_billsby_header_script');

            // filter hook to update company ID on header script
            $this->loader->add_filter('script_loader_tag', $plugin_public, 'add_attributes_to_script', 10, 3);

            // action for data sharing header code
            // $this->loader->add_action('wp_head', $plugin_public, 'inject_data_sharing_header_code');

            // action for data sharing header code
            $this->loader->add_filter('the_content', $plugin_public, 'billsby_restrict_content', -1);
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Billsby_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    // /**
    //  * Retrieve the table name of the plugin.
    //  *
    //  * @since     1.0.0
    //  * @return    string    The version number of the plugin.
    //  */
    // public function get_config_table_name()
    // {
    //     return $this->config_table_name;
    // }


    // /**
    // * Retrieve the table name of the WP user meta.
    // *
    // * @since     1.0.0
    // * @return    string    The version number of the plugin.
    // */
    // public function get_umeta_table_name()
    // {
    //     return $this->umeta_table_name;
    // }
}
