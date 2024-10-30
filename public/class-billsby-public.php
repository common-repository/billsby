<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.billsby.com
 * @since      1.0.0
 *
 * @package    Billsby
 * @subpackage Billsby/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Billsby
 * @subpackage Billsby/public
 * @author     Billsby <hello@billsby.com>
 */
class Billsby_Public
{

    // get billsby config table name
    private $config_table_name;
    
    
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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
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
     * Register the stylesheets for the public-facing side of the site.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/billsby-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/billsby-public.js', array( 'jquery' ), $this->version, false);
    }


    private function get_subscribe_btn_content($wp_a)
    {
        ob_start(); ?>
	
		<a href="javascript:void(0)"
			data-billsby-type="checkout"

			<?php if (!empty($wp_a['product'])) : ?>
			data-billsby-product="<?php echo esc_attr($wp_a['product']); ?>"
			<?php endif; ?>
			
			<?php if (!empty($wp_a['plan'])) : ?>
			data-billsby-plan="<?php echo esc_attr($wp_a['plan']); ?>"
			<?php endif; ?>
			
			<?php if (!empty($wp_a['cycle'])) : ?>
			data-billsby-cycle="<?php echo esc_attr($wp_a['cycle']); ?>"
			<?php endif; ?>

			class="billsby-btn"
			<?php if ((strcmp($wp_a['background-color'], 'default') !== 0) || strcmp($wp_a['text-color'], 'default') !== 0) : ?>
			style="
				<?php if (strcmp($wp_a['background-color'], 'default') !== 0) : ?>
				background-color: #<?php echo esc_attr($wp_a['background-color']); ?>;
				<?php endif; ?>
				
				<?php if (strcmp($wp_a['text-color'], 'default') !== 0) : ?>
				color: #<?php echo esc_attr($wp_a['text-color']); ?>;
				<?php endif; ?>
			"
			<?php endif; ?>
		>
			<?php echo esc_html($wp_a['text']); ?>
		</a>

	<?php
        return ob_get_clean();
    }

    private function get_manage_acc_btn_content($wp_a)
    {
        $user_logged_in = is_user_logged_in();
        $billsby_user_meta = $this->get_user_billsby_meta();

        ob_start(); ?>
	
		<a href="javascript:void(0)"
			data-billsby-type="account"

			<?php if ($user_logged_in) : ?>
			data-billsby-customer="<?php echo esc_attr($billsby_user_meta['billsby_customer_id']); ?>"
			<?php endif; ?>

			class="billsby-btn"
			<?php if ((strcmp($wp_a['background-color'], 'default') !== 0) || strcmp($wp_a['text-color'], 'default') !== 0) : ?>
			style="
				<?php if (strcmp($wp_a['background-color'], 'default') !== 0) : ?>
				background-color: #<?php echo esc_attr($wp_a['background-color']); ?>;
				<?php endif; ?>
				
				<?php if (strcmp($wp_a['text-color'], 'default') !== 0) : ?>
				color: #<?php echo esc_attr($wp_a['text-color']); ?>;
				<?php endif; ?>
			"
			<?php endif; ?>
		>
			<?php echo esc_html($wp_a['text']); ?>
		</a>

	<?php
        return ob_get_clean();
    }

    private function get_user_billsby_meta()
    {
        global $current_user;
        get_currentuserinfo();

        $user_id = $current_user->ID;
        
        if ($current_user) {
            $billsby_user_meta = array(
                'billsby_customer_id'       => get_user_meta($user_id, 'billsby_customer_id', true),
                'billsby_subscription_data' => get_user_meta($user_id, 'billsby_subscription_data', true),
                'first_name'                => get_user_meta($user_id, 'first_name', true),
                'last_name'       			=> get_user_meta($user_id, 'last_name', true),
                'billsby_feature_tag'       => get_user_meta($user_id, 'billsby_feature_tag', true),
            );
        }

        return $billsby_user_meta;
    }

    /**
     * Callback function for subscribe button shortcode for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function load_subscribe_button($atts)
    {
        // normalize attribute keys, lowercase
        $a = array_change_key_case((array) $atts, CASE_LOWER);

        // override default attributes with user attributes
        $wp_a = shortcode_atts(
            array(
                    'background-color' => 'default',
                    'text-color'       => 'default',
                    'product'          => '',
                    'plan'             => '',
                    'cycle'            => '',
                    'text'             => 'Subscribe',
            ),
            $atts
        );

        global $wpdb;

        $billsby_config = $wpdb->get_results("SELECT logout_url, account_sync FROM ".$this->config_table_name." WHERE id = 1");
        $account_sync = $billsby_config[0]->account_sync;
        $logout_url = $billsby_config[0]->logout_url ? $billsby_config[0]->logout_url : '/wp-login.php?action=register';
        $user_logged_in = is_user_logged_in();
        $billsby_user_meta = $this->get_user_billsby_meta();
        $has_subscription = false;
        $subscription_data = (
            $billsby_user_meta['billsby_subscription_data'] != null &&
            !empty($billsby_user_meta['billsby_subscription_data'])
        ) ? json_decode($billsby_user_meta['billsby_subscription_data']) : array();

        ob_start();
        
        if ($account_sync) :
            if ($user_logged_in) :
                if (count($subscription_data) != 0) :
                    $has_subscription = true;
        endif;
        // For logged in users, subscribe buttons are hidden if they already have a subscription ID associated with their Wordpress profile
        echo ($has_subscription) ? '' : $this->get_subscribe_btn_content($wp_a); else : ?>
				<a href="<?php echo $logout_url; ?>"
					class="billsby-btn"
					<?php if ((strcmp($wp_a['background-color'], 'default') !== 0) || strcmp($wp_a['text-color'], 'default') !== 0) : ?>
					style="
						<?php if (strcmp($wp_a['background-color'], 'default') !== 0) : ?>
						background-color: #<?php echo $wp_a['background-color'] ?>;
						<?php endif; ?>
						
						<?php if (strcmp($wp_a['text-color'], 'default') !== 0) : ?>
						color: #<?php echo $wp_a['text-color'] ?>;
						<?php endif; ?>
					"
					<?php endif; ?>
				>
					<?php echo esc_html($wp_a['text']); ?>
				</a>
			<?php
            endif; else :
            echo $this->get_subscribe_btn_content($wp_a);
        endif; ?>

		<?php
        return ob_get_clean();
    }


    /**
     * Callback function for manage account button shortcode for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function load_manage_account_button($atts)
    {
        // normalize attribute keys, lowercase
        $a = array_change_key_case((array) $atts, CASE_LOWER);

        // override default attributes with user attributes
        $wp_a = shortcode_atts(
            array(
                    'background-color' => 'default',
                    'text-color'       => 'default',
                    'text'             => 'Manage Subscription',
            ),
            $atts
        );
        
        global $wpdb;

        // billsby config
        $billsby_config =  $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");
        $account_sync = $billsby_config->account_sync;
        $user_logged_in = is_user_logged_in();
        $billsby_user_meta = $this->get_user_billsby_meta();
        $has_subscription = false;
        $subscription_data = (
            $billsby_user_meta['billsby_subscription_data'] != null &&
            !empty($billsby_user_meta['billsby_subscription_data'])
        ) ? json_decode($billsby_user_meta['billsby_subscription_data']) : array();

        ob_start();
        
        if ($account_sync) :
            if ($user_logged_in) :
                if (count($subscription_data) != 0) :
                    $has_subscription = true;
        endif;
        // For logged in users, manage account buttons are hidden if they do NOT have a subscription ID associated with their Wordpress profile
        echo ($has_subscription) ? $this->get_manage_acc_btn_content($wp_a, $billsby_user_meta['billsby_customer_id']) : ''; else : ?>
				<a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>"
					class="billsby-btn"
					<?php if ((strcmp($wp_a['background-color'], 'default') !== 0) || strcmp($wp_a['text-color'], 'default') !== 0) : ?>
					style="
						<?php if (strcmp($wp_a['background-color'], 'default') !== 0) : ?>
						background-color: #<?php echo $wp_a['background-color'] ?>;
						<?php endif; ?>
						
						<?php if (strcmp($wp_a['text-color'], 'default') !== 0) : ?>
						color: #<?php echo $wp_a['text-color'] ?>;
						<?php endif; ?>
					"
					<?php endif; ?>
				>
					<?php echo $wp_a['text'] ?>
				</a>
			<?php
            endif; else :
            echo $this->get_manage_acc_btn_content($wp_a);
        endif; ?>

		<?php
        return ob_get_clean();
    }


    private function get_sharing_header_code_data()
    {
        global $current_user;
        get_currentuserinfo();

        $user_logged_in = is_user_logged_in();
        $billsby_user_meta = $this->get_user_billsby_meta();
        $first_name = $billsby_user_meta['first_name'];
        $last_name = $billsby_user_meta['last_name'];
        $data = '';

        if ($user_logged_in)  {
            if (empty($billsby_user_meta['billsby_customer_id'])) {
                $data = 'window.billsbyData = {';
                
                if ( !empty($first_name) ) {
                    $data .= 'firstName: "'.$first_name.'",';
                }

                if ( !empty($last_name) ) {
                    $data .= 'lastName: "'.$last_name.'",';
                }  
                $data .= 'email: "'.$current_user->user_email.'",';
                $data .= '};';
            } else {
                $data = 'window.billsbyData = {
                    cid: "'.$billsby_user_meta['billsby_customer_id'].'",
                };';
            }
        }

        return $data;
    }

    public function enqueue_billsby_header_script() {
        wp_enqueue_script("billsby-header-script", 'https://checkoutlib.billsby.com/checkout.min.js', array( 'jquery' ), $this->version, false);

        $data = $this->get_sharing_header_code_data();

        // if user is loggedin. inject inline script
        if ( ! empty( $data ) ) {
            wp_add_inline_script( 'billsby-header-script', $data );
        }
    }

    public function add_attributes_to_script( $tag, $handle, $src ) {
        if ( 'billsby-header-script' !== $handle ) {
            return $tag;
        }

        global $wpdb;
        $billsby_config = $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");
        
        if ( ! empty( $billsby_config->company_id ) ) {
            return str_replace( ' src', ' data-billsby-company="'.esc_attr( $billsby_config->company_id ).'" src', $tag );
        }

        return $tag;

        // if ( 'billsby-header-script' === $handle ) {
        //     global $wpdb;
        //     $billsby_config = $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");

        //     if ( ! empty( $billsby_config->company_id ) ) {
        //         $tag = '<script src="'.esc_url( $src ).'" data-billsby-company="'.esc_attr( $billsby_config->company_id ).'"></script>';
        //     }

        //     return $tag;
        // }
    }

    /**
     * Restrict content
     *
     * @param string $content
     *
     * @return string $content
     */
    public function billsby_restrict_content($content)
    {
        global $post;
        global $wpdb;

        // billsby config
        $billsby_config =  $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");
        $billsby_restriction_message = $billsby_config->restriction_message;
        $billsby_access_control = $billsby_config->access_control;

        if (!$billsby_access_control) {
            return $content;
        }

		// post meta
        $billsby_feature_tags = get_post_meta($post->ID, 'billsby_feature_tags', true);
        $billsby_must_have_all_tags = get_post_meta($post->ID, 'billsby_must_have_all_tags', true);

        // user meta
        $billsby_user_meta = $this->get_user_billsby_meta();
        $billsby_user_feature_tags = (
			$billsby_user_meta['billsby_feature_tag'] != null &&
			!empty($billsby_user_meta['billsby_feature_tag'])
		) ? json_decode($billsby_user_meta['billsby_feature_tag']) : array();

		$content_restricted = true;

		// If there are no existing user meta, convert result to empty array.
		if ( !$billsby_user_feature_tags || !count( $billsby_user_feature_tags ) ) {
			$billsby_user_feature_tags = [];
		}

		// If there are no existing post meta, convert result to empty array.
		if (!$billsby_feature_tags) {
			$billsby_feature_tags = [];
		}

		// if must have all tags is not set, set it to false by default, else true.
		if (!$billsby_must_have_all_tags) {
			$billsby_must_have_all_tags = false;
		} else {
			$billsby_must_have_all_tags = true;
		}

		$intersected_tags = array_intersect($billsby_user_feature_tags, $billsby_feature_tags);
		
		sort( $intersected_tags );
		sort( $billsby_feature_tags );

		if ( count( $intersected_tags ) ) {
			$content_restricted = false;
		}

		// check if must have all tags
		if ($billsby_must_have_all_tags) {
			if ( $intersected_tags == $billsby_feature_tags ) {
				$content_restricted = false;
			} else {
				$content_restricted = true;
			}
		}


		// if no feature tags given, don't restrict content even must have all tags is true
		if (empty($billsby_feature_tags)) {
			$content_restricted = false;
		}
	
		// check first if were are on a page
		if (is_single() || is_page()) {
            if ($content_restricted) {
                return '<p>'.esc_html__($billsby_restriction_message, 'billsby').'</p>';
            } else {
                return $content;
            }
        } else {
            return $content;
        }
    }

    private function get_restriction_message($restriction_message)
    {
        ob_start();

        echo '<div class="billsby-restriction-message">'.do_shortcode($restriction_message).'</div>';
        
        return ob_get_clean();
    }


    /**
     * Restrict content shortcode
     */
    public function billsby_restrict_content_sc($atts, $content = null)
    {
        global $wpdb;

        // normalize attribute keys, lowercase
        $a = array_change_key_case((array) $atts, CASE_LOWER);

        // override default attributes with user attributes
        $wp_a = shortcode_atts(
            array(
                'featuretag' 	=> '',
                'tagmode'       => '',
                'display-mode'  => '',
                'tag-mode'      => '',
            ),
            $atts
        );
        
        // billsby config
        $billsby_config =  $wpdb->get_row("SELECT * FROM ".$this->config_table_name." WHERE id = 1");
        $billsby_access_control = $billsby_config->access_control;
        $billsby_restriction_message = $billsby_config->restriction_message;

        // billsby user meta
        $billsby_user_meta = $this->get_user_billsby_meta();
        $subscription_data = (
            $billsby_user_meta['billsby_subscription_data'] != null &&
            !empty($billsby_user_meta['billsby_subscription_data'])
        ) ? json_decode($billsby_user_meta['billsby_subscription_data']) : array();
        $billsby_user_feature_tags = (
            $billsby_user_meta['billsby_feature_tag'] != null &&
            !empty($billsby_user_meta['billsby_feature_tag'])
        ) ? json_decode($billsby_user_meta['billsby_feature_tag']) : array();

        // get all subscription status of user's subscription
        $subscription_statuses = array_column($subscription_data, 'SubscriptionStatus');
        // get key of Active subscriptions
        $active_subscription_found_key = array_search('Active', $subscription_statuses);

        // convert featuretag to array.
        if ( $wp_a['featuretag'] ) {
			$feature_tags = array_map('trim', explode(',', $wp_a['featuretag']));	
		}
        // get all user feature tags found in featuretag attr
        $intersected_tags = array_intersect($billsby_user_feature_tags, $feature_tags);

        $content_restricted = true;
		
		sort( $intersected_tags );
		sort( $feature_tags );
		
		if ( $wp_a['display-mode'] != "inverse" ) {

			// user has an active susbcription
			if ( $active_subscription_found_key !== false ) {
				$content_restricted = false;
			}
			
			// if tag is provided
			if ( count( $feature_tags ) ) {
				$content_restricted = false;
				
				// user must have all required tags
				if ( $wp_a['tagmode'] == "alltags" ) {
					// restrict if user DOES NOT HAVE ALL the featured tags
					if ( $intersected_tags != $feature_tags ) {
						$content_restricted = true;
					}
				} else {
					// restrict if user DOES NOT HAVE one/more of the feature tags
					if ( !count( $intersected_tags ) ) {
						$content_restricted = true;
					}
				}
			}

		} else {
			// user DOES NOT HAVE an active susbcription
			if ( $active_subscription_found_key === false ) {
				$content_restricted = false; 
			}
			
			// if tag is provided
			if ( count( $feature_tags ) ) {
				$content_restricted = false;
				
				// user must NOT have all required tags
				if ( $wp_a['tagmode'] == "alltags" ) {
					// restrict if user HAVE ALL the featured tags
					if ( $intersected_tags == $feature_tags ) {
						$content_restricted = true;
					}
				} else {
					// restrict if user HAVE one/more of the feature tags
					if ( count( $intersected_tags ) ) {
						$content_restricted = true;
					}
				}
			}

		}

//         // check if users have an active subscription
//         if ($active_subscription_found_key !== false) {
//             $content_restricted = false;
//         } else {
//             $content_restricted = true;
//         }

//         // check if featuretag is present
//         // and user have one of the feature tags from the featuretag list
//         if (count($feature_tags) > 0) {
//             if ($intersected_tags) {
//                 $content_restricted = false;
//             } else {
//                 $content_restricted = true;
//             }
//         }

//         // check if featuretag is present and tag-mode is equal to 'alltags',
//         // and user have ALL of the feature tags from the featuretag list
//         if ((count($feature_tags) > 0) && ($wp_a['tag-mode'] == "alltags")) {
//             if ($intersected_tags == $feature_tags) {
//                 $content_restricted = false;
//             } else {
//                 $content_restricted = true;
//             }
//         }

//         // check if display-mode is equal to 'inverse'
//         // and user does not have an active subscription
//         if ($wp_a['display-mode'] == "inverse") {
//             if ($active_subscription_found_key === false) {
//                 $content_restricted = false;
//             } else {
//                 $content_restricted = true;
//             }
//         }

//         // check if display-mode is equal to 'inverse' and featuretag is present,
//         // and user do NOT have one of the feature tags from the featuretag list
//         if (($wp_a['display-mode'] == "inverse") && (count($feature_tags) > 0)) {
//             if (!$intersected_tags) {
//                 $content_restricted = false;
//             } else {
//                 $content_restricted = true;
//             }
//         }

//         // check if display-mode is equal to 'inverse' and featuretag is present and tag-mode is equal to 'alltags',
//         // and user do NOT have ALL of the feature tags from the featuretag list
//         if (($wp_a['display-mode'] == "inverse") && (count($feature_tags) > 0) && ($wp_a['tag-mode'] == "alltags")) {
//             if ($intersected_tags != $feature_tags) {
//                 $content_restricted = false;
//             } else {
//                 $content_restricted = true;
//             }
//         }

        // return content
        if ($billsby_access_control) {
            if ($content_restricted) {
                return $this->get_restriction_message($billsby_restriction_message);
            } else {
                return "<div class='billsby-restricted-content'>".do_shortcode($content)."</div>";
            }
        } else {
            // return the actual content if access control is not activated
            return "<div class='billsby-restricted-content'>".do_shortcode($content)."</div>";
        }
    }
}
