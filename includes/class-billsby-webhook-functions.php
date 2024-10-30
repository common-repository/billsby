<?php

class Billsby_Webhooks
{
    public function billsby_add_or_update_subscription_data($user_id, $subscription_ids)
    {
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $usermeta_table_name = $activator->wp_usermeta_table();
        global $wpdb;

        // Create data list structure
        $subscription_array = array();
        foreach ($subscription_ids as $value) {
            array_push($subscription_array, array(
            'SubscriptionUniqueId' => $value,
            'SubscriptionStatus'=> 'NA',
            'BillsbyProductId'=> null,
            'BillsbyPlanId'=> null,
            'BillsbyProductName'=> null,
            'BillsbyPlanName'=> null
        ));
        }
        $subscription_list = json_encode($subscription_array);

        // Check if user meta data already exist
        $check_usermeta = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$user_id);

        if ($check_usermeta) {
            if ($check_usermeta->meta_value !=  $subscription_list) {
                $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '".$subscription_list."' WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$user_id;
                $query_result = $wpdb->query($update_query);
                return "Info: Updated Subscriptions";
            } else {
                return "Info: No Updated Subscriptions";
            }
        } else {
            $insert_query = "INSERT INTO ".$usermeta_table_name." (user_id, meta_key, meta_value) VALUES (".$user_id.", 'billsby_subscription_data', '".$subscription_list."')";
            $query_result = $wpdb->query($insert_query);
            return "Info: Added Subscriptions";
        }
    }

    public function billsby_update_subscription_data($user_id, $subscription_data)
    {
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $usermeta_table_name = $activator->wp_usermeta_table();
        global $wpdb;

        // Create data list structure
        $subscription_list = json_encode($subscription_data);

        // Check if user meta data already exist
        $check_usermeta = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$user_id);

        if ($check_usermeta) {
            if ($check_usermeta->meta_value !=  $subscription_list) {
                $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '".$subscription_list."' WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$user_id;
                $query_result = $wpdb->query($update_query);
                return "Info: Updated Subscriptions";
            } else {
                return "Info: No Updated Subscriptions";
            }
        } else {
            $insert_query = "INSERT INTO ".$usermeta_table_name." (user_id, meta_key, meta_value) VALUES (".$user_id.", 'billsby_subscription_data', '".$subscription_list."')";
            $query_result = $wpdb->query($insert_query);
            return "Info: Updated Subscription Details";
        }
    }


    public function billsby_add_or_update_customer_id($user_id, $customer_id)
    {
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $usermeta_table_name = $activator->wp_usermeta_table();
        global $wpdb;


        $check_usermeta = $wpdb->get_row("SELECT * FROM " . $usermeta_table_name . " WHERE meta_key = 'billsby_customer_id' AND user_id = " . $user_id);

        if ($check_usermeta) {
            if ($check_usermeta->meta_value !=  $customer_id) {
                $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '".$customer_id."' WHERE meta_key = 'billsby_customer_id' AND user_id = ".$user_id;
                $query_result = $wpdb->query($update_query);
                return "Info: Updated Customer Id";
            } else {
                return "Info: No Updated Customer Id";
            }
        } else {
            $insert_query = "INSERT INTO ".$usermeta_table_name." (user_id, meta_key, meta_value) VALUES (".$user_id.", 'billsby_customer_id', '".$customer_id."')";
            $query_result = $wpdb->query($insert_query);
            return "Info: Added Customer Id";
        }
    }

    public function billsby_update_customer_name($post_firstname, $post_lastname, $user_id)
    {
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $usermeta_table_name = $activator->wp_usermeta_table();
        global $wpdb;

        // Get current First and Last Name
        $usermeta_firstname =  $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'first_name' AND user_id = ".$user_id);
        $usermeta_lastname =  $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'last_name' AND user_id = ".$user_id);

        $info = [];

        if ($usermeta_firstname->meta_value != $post_firstname) {
            $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '".$post_firstname."' WHERE meta_key = 'first_name' AND user_id = ".$user_id;
            $query_result = $wpdb->query($update_query);
            $info += ["FirstNameUpdated" => true];
        }
        if ($usermeta_lastname->meta_value != $post_lastname) {
            $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '".$post_lastname."' WHERE meta_key = 'last_name' AND user_id = ".$user_id;
            $query_result = $wpdb->query($update_query);
            $info += ["LastNameUpdated" => true];
        }

        if (count($info) == 0) {
            return "Info: Name did not update";
        }

        return $info;
    }

    public function billsby_update_email($email, $user_id)
    {
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $users_table_name = $activator->wp_users_table();
        global $wpdb;

        $update_query = "UPDATE ".$users_table_name." SET user_email = '".$email."' WHERE ID = ".$user_id;
        $query_result = $wpdb->query($update_query);
        // $info = ["EmailUpdated" => true];
        $info = "Info: Email Updated";

        return $info;
    }

    public function billsby_subscription_update_status($customer_id, $subscription_unique_id, $subscription_status)
    {
        // Initialize table names
        global $wpdb;
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $usermeta_table_name = $activator->wp_usermeta_table();
        $billsby_config_table = $activator->wp_billsby_table_config();

        // Find user id using Customer Id
        $usermeta_customer_id = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_customer_id' AND meta_value = '".$customer_id."'");

        if ($usermeta_customer_id) {
            // Find Subscription List
            $usermeta_subscriptions =  $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$usermeta_customer_id->user_id);
    
            $message = "";
            $json = json_decode($usermeta_subscriptions->meta_value);
    
    
            if ($subscription_status == "Suspended" || $subscription_status == "Active") {
                foreach ($json as $value) {
                    if ($value->SubscriptionUniqueId == $subscription_unique_id) {
                        if ($value->SubscriptionStatus != $subscription_status) {
                            $value->SubscriptionStatus = $subscription_status;
                            $subscription_list = json_encode($json);
    
                            $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '".$subscription_list."' WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$usermeta_customer_id->user_id;
                
                            $query_result = $wpdb->query($update_query);
                            
                            $message = "Info: Updated Subscription Status";
                        } else {
                            $message = "Info: No Updated Subscription Status";
                        }
                    }
                    if ($value->BillsbyProductName == null || $value->BillsbyPlanName == null) {
                        // Get Domain
                        $billsby_config = $wpdb->get_row("SELECT * FROM ".$billsby_config_table." WHERE id = 1");

                        $update_subscription_details = $this->billsby_get_subscription_details($usermeta_customer_id->user_id, $billsby_config->company_id, $subscription_unique_id);
                    }
                }
            }
    
            if ($subscription_status == "Cancelled") {
                $initial_count = count($json);
                for ($i=0; $i<count($json); $i++) {
                    if ($json[$i]->SubscriptionUniqueId == $subscription_unique_id) {
                        unset($json[$i]);
                    }
                }
                $new_count = count($json);
                $difference = $initial_count - $new_count;
                if ($difference > 0) {
                    $subscription_list = json_encode($json);
                    $update_query = "UPDATE ".$usermeta_table_name." SET meta_value = '".$subscription_list."' WHERE meta_key = 'billsby_subscription_data' AND user_id = ".$usermeta_customer_id->user_id;
                    
                    $query_result = $wpdb->query($update_query);
                    
                    $message =  "Info: ".$difference." Subscription deleted";
                } else {
                    $message =  "Info: No Subscription deleted";
                }
            }
    
            /* --------------------------- Update Feature tags -------------------------- */
    
            // Get Config Data
            $billsby_config = $wpdb->get_row("SELECT * FROM ".$billsby_config_table." WHERE id = 1");
            $subscription_unique_id =  $json[0]->SubscriptionUniqueId;
    
            // Insert or update feature tags
            $update_tags = $this->billsby_update_feature_tags($usermeta_customer_id->user_id, $billsby_config->company_id, $subscription_unique_id, $billsby_config->api_key);
            var_dump($update_tags);
        } else {
            $message = " Info: Can't find the user with the given customer id";
        }

        return $message;
    }

    public function billsby_http_get_request($url, $api_key)
    {
        $api_response = wp_remote_get($url, array(
            'headers' => array(
                'apikey' =>  $api_key
            )
        ));
        $api_result = json_decode(wp_remote_retrieve_body($api_response), true);

        return $api_result;
    }

    
    public function billsby_http_post_request($url, $api_key)
    {
        $api_response = wp_remote_get($url, array(
            'headers' => array(
                'apikey' =>  $api_key
            )
        ));
        $api_result = json_decode(wp_remote_retrieve_body($api_response), true);

        return $api_result;
    }


    public function billsby_get_subscription_details($user_id, $domain, $subscription_unique_id)
    {
        global $wpdb;
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $billsby_config_table = $activator->wp_billsby_table_config();


        // Get subscription details https://support.billsby.com/reference#get-subscription-details
        $get_subscription_details_url = "https://public.billsby.com/api/v1/rest/core/" . $domain . "/subscriptions/" . $subscription_unique_id;

        // Get Config Data
        $billsby_config = $wpdb->get_row("SELECT * FROM " . $billsby_config_table . " WHERE id = 1");

        // Call Billsby API to get subscription details
        $subscription_details = $this->billsby_http_get_request($get_subscription_details_url, $billsby_config->api_key);

        if ($subscription_details) {
            // Structure new subscription data
            $subscription_data = array(array(
                'SubscriptionUniqueId' => $subscription_details['subscriptionUniqueId'],
                'SubscriptionStatus'=> $subscription_details['status'],
                'BillsbyProductId'=> $subscription_details['productId'],
                'BillsbyPlanId'=> $subscription_details['planId'],
                'BillsbyProductName'=> $subscription_details['productName'],
                'BillsbyPlanName'=> $subscription_details['planName']
            ));

            // Update subscription ID, product name, plan name and status as appropriate
            $save_subscription_data = $this->billsby_update_subscription_data($user_id, $subscription_data);
    
            var_dump($save_subscription_data);

            return "Done";
        } else {
            var_dump("INFO: No Subscription details update happened. Can't find subscription ID details");

            return "Nothing Happened";
        }
    }

    public function billsby_get_feature_tags($user_id)
    {
        global $wpdb;
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $usermeta_table_name = $activator->wp_usermeta_table();
        $billsby_config_table = $activator->wp_billsby_table_config();
        $users_table_name = $activator->wp_users_table();

        $check_user = $wpdb->get_row("SELECT * FROM ".$users_table_name." WHERE ID = ".$user_id);

        if ($check_user) {
            $billsby_featureTag = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_feature_tag' AND user_id =".$user_id);
    
            // Return feature tags if found
            if ($billsby_featureTag) {
                $feature_tags = json_decode($billsby_featureTag->meta_value);
                return $feature_tags;
            }
            // Get feature tags if not found in DB
            else {
                // Get Subscription Data
                $billsby_subscriptionData = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_subscription_data' AND user_id =".$user_id);
                $json_data = json_decode($billsby_subscriptionData->meta_value);
                $subscription_id =  $json_data[0]->SubscriptionUniqueId;
        
                // Get Config Data
                $billsby_config = $wpdb->get_row("SELECT * FROM ".$billsby_config_table." WHERE id = 1");
        
                // Insert or update feature tags
                $result_tags = $this->billsby_update_feature_tags($user_id, $billsby_config->company_id, $subscription_id, $billsby_config->api_key);
                $feature_tags = json_decode($result_tags);
                
                return $feature_tags;
            }
        } else {
            return "No user with this ID is found in the database";
        }
    }

    public function billsby_update_feature_tags($user_id, $company_id, $subscription_id, $api_key)
    {
        global $wpdb;
        require_once plugin_dir_path(__FILE__) . 'class-billsby-activator.php';
        $activator = new Billsby_Activator();
        $usermeta_table_name = $activator->wp_usermeta_table();
        
        // Create the api link
        $url = "https://public.billsby.com/api/v1/rest/core/".$company_id."/subscriptions/".$subscription_id."/tags/split";
    

        /** Uncomment this when there is a working subscription id in billsby. */
        // Get data using link
        $api_response = wp_remote_get($url, array(
                'headers' => array(
                    'apikey' =>  $api_key
                )
            ));
        $api_result = json_decode(wp_remote_retrieve_body($api_response), true);
    
        // Sample api_result -> ["pro_plan"]
        // $api_result = array(
        //     'pro_plan'
        //     );
    
        // Save API result to user's meta data
        $tags = array();
        if ($api_result) {
            /**
            * NOTE: Sample Result
            * {
            *   "planFeatureTags":[
            *   "test"
            *   "testplan"
            *   ]
            *   "subscriptionFeatureTags":[
            *   "customtag"
            *   ]
            * }
            * NOTE: Collect tags into an array then JSON encode them
            */
            foreach ($api_result as $key => $value) {
                foreach ($value as $tag) {
                    array_push($tags, $tag);
                }
            }
        }

        $feature_tags = json_encode($tags);
        // Check Existing tags
        $billsby_featureTag = $wpdb->get_row("SELECT * FROM ".$usermeta_table_name." WHERE meta_key = 'billsby_feature_tag' AND user_id =".$user_id);
    
        // Update if existing
        if ($billsby_featureTag) {
            $update_query = "UPDATE ".$usermeta_table_name." SET meta_value='".$feature_tags."' WHERE user_id = ".$user_id." AND meta_key = 'billsby_feature_tag'";
            $query_result = $wpdb->query($update_query);
        }
        // Insert if not existing
        else {
            $insert_query = "INSERT INTO ".$usermeta_table_name." (user_id, meta_key, meta_value) VALUES (".$user_id.", 'billsby_feature_tag', '".$feature_tags."')";
            $query_result = $wpdb->query($insert_query);
        }
        return $feature_tags;
    }
}
