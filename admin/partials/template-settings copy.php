<input type=hidden name='db_secret_key' id='db_secret_key' value='<?= $user_config->secret_key ?>'/>
<input type=hidden name='db_company_id' id='db_company_id' value='<?= $user_config->company_id ?>'/>
<input type=hidden name='db_api_key' id='db_api_key' value='<?= $user_config->api_key ?>'/>
<input type=hidden name='db_webhook_status' id='db_webhook_status' value='<?= $user_config->webhook_status ?>'/>
<input type=hidden name='db_setup_complete' id='db_setup_complete' value='<?= $user_config->setup_complete ?>'/>

<div class='billsby-container' id='billsby-settings-page'>

  <!-- Start Screen 1 -->
  <div class="billsby-setup-screen-one">
    <div class='billsby-header'>
      <div class='billsby-header-title'>
        Welcome to Billsby for WordPress
      </div>
      <div  class='billsby-header-description'>
        Powering subscription billing for your WordPress powered websites.
      </div>

      <a href='https://app.billsby.com/registration' target='_blank' class='billsby-header-button'>
        Sign up for a Billsby account (if you haven't already)
      </a>
    </div>

    <div class='billsby-body-container'>
      <div class='billsby-description'>
        First, we need to connect your Billsby account to WordPress. To do this, we need your company ID and API key.
      </div>
      <div class='billsby-form'>
        <div class='billsby-form-input-group center-v'>
          <div class='billsby-form-input-label'>
            Company ID
          </div>
          <div class='billsby-form-input-field'>
            <input type='text' class='bfif-input-text' id='bfif-company-id' value='yourcompany'/> .billsby.com
          </div>
        </div>

        <div class='billsby-form-input-group'>
          <div class='billsby-form-input-label'>
            API key
          </div>
          <div class='billsby-form-input-field'>
            <input type='text' class='bfif-input-text bfif-api-key' id='bfif-api-key' value='yourcompany_3f4aff177214419fb0dac55405fbd03a'/>
            <div class='bfif-description'>
              Find this in Settings > Configuration > API Keys and Webhooks > API Keys
            </div>

            <div class="bfif-screenshot-holder">
              <img src="<?php echo plugins_url('billsby/admin/assets/billsby_api_key_screenshot.png'); ?>" alt="API Key">
            </div>
            <div id='billsby-combination-notifications'></div>

            <button class='bfif-btn bfif-btn-primary'>
              Next step
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Screen 1 -->


  <!-- Start Screen 2 -->
  <div class="billsby-setup-screen-two">
    <div class='billsby-header'>
      <div class='billsby-header-title'>
        Welcome to Billsby for WordPress
      </div>
      <div  class='billsby-header-description'>
        Powering subscription billing for your WordPress powered websites.
      </div>
    </div>

    <div class='billsby-body-container'>
      <div class='billsby-description'>
        Great! We've now connected <span class="billsby-fw-bold">yourcompany.billsby.com</span> to your WordPress website. Next, we need to ensure that this website can communicate back to Billsby.
      </div>
      <div class='billsby-description'>
        On the same page you just obtained the API key from, choose the webhooks tab. Then, set the details of the URL and secret key to the values provided below.
      </div>

      <div class='billsby-form-input-group'>
        <div class='billsby-form-input-label'>Webhook URL</div>
        <div class='billsby-form-input-field'>
          <div class='bfif-copy-text billsby-webhook-url'>[wordpress-root-url]/wp-json/billsby/endpoint</div>
        </div>
      </div>
      <div class='billsby-form-input-group'>
        <div class='billsby-form-input-label'>Secret key</div>
        <div class='billsby-form-input-field'>
          <div class='bfif-copy-text' id='billsby-secret-key'><?= $user_config->secret_key ?></div>
        </div>
      </div>
      
      <div class='billsby-description'>
        Then press the 'Update Webhook Details' button to save these changes. Finally, to test that you've set this up right, we're going to send a test webhook from Billsby to your Wordpress website. To do this, press the 'Test' button next to 'Customer created'.
      </div>
      
      <div class="bfif-screenshot-holder">
        <img src="<?php echo plugins_url('billsby/admin/assets/billsby_webhook_screenshot.png'); ?>" alt="Billsby Webhook">
      </div>
      
      <div id="billsby-connect-notifications">
        <div class='billsby-notification  billsby-notification-warning'>
          Waiting to receive test webhook. Please follow the steps above to send a test webhook so we can finish off the setup process.
        </div>
      </div>
    </div>
  </div>
  <!-- End Screen 2 -->

  <!-- Start Screen 3 -->
  <div class="billsby-setup-screen-three">
    <div class='billsby-header'>
      <div class='billsby-header-title'>
        Welcome to Billsby for WordPress
      </div>
      <div  class='billsby-header-description'>
        Powering subscription billing for your WordPress powered websites.
      </div>
    </div>

    <div class='billsby-body-container'>
      <div class='billsby-description'>
        Finally, let's decide which parts of Billsby for WordPress we want to use. You can turn these on later if you'd prefer, and you'll be able to customise specific settings for each of the features you turn on in their individual pages after setup is complete.
      </div>
  
      <div class="billsby-checkbox-list-item">
        <input type="checkbox" class="billsby-checkbox" checked/>
        <div class="billsby-checkbox-label-holder">
          <div class="billsby-checkbox-title">Subscribe and Manage Account Buttons</div>
          <div class="billsby-checkbox-subtitle">Embed Billsby Subscribe and Manage Account buttons into any page of your website using a shortcode (this feature is always on)</div>
        </div>
      </div>

      <div class="billsby-checkbox-list-item">
        <input type="checkbox" class="billsby-checkbox" checked/>
        <div class="billsby-checkbox-label-holder">
          <div class="billsby-checkbox-title">Account Synchronization</div>
          <div class="billsby-checkbox-subtitle">Sync Wordpress and Billsby account details. Your customers will be required to be logged in to a Wordpress account to create or manage a subscription.</div>
        </div>
      </div>

      <div class="billsby-checkbox-list-item">
        <input type="checkbox" class="billsby-checkbox" checked/>
        <div class="billsby-checkbox-label-holder">
          <div class="billsby-checkbox-title">Access Control</div>
          <div class="billsby-checkbox-subtitle">Limit access to certain content on your site to customers with subscriptions, including limiting content based on feature tags.</div>
        </div>
      </div>

      <button class='bfif-btn bfif-btn-primary'>
        Complete Billsby setup
      </button>
    </div>
  </div>
  <!-- End Screen 3 -->


  <!-- Start Screen 4 -->
  <div class="billsby-setup-screen-four">
    <div class='billsby-header'>
      <div class='billsby-header-title'>
        Billsby for WordPress is now setup.
      </div>
      <div  class='billsby-header-description'>
        You can now start using Billsby to manage your subscription business.
      </div>

      <a href='https://support.billsby.com/docs/wordpress' target='_blank' class='billsby-header-button'>
        Get more help and support on the Billsby support site
      </a>
    </div>

    <div class='billsby-body-container'>
      <div class='billsby-notification billsby-notification-success'>
        This website is connected with&nbsp;<span class="billsby-fw-bold">yourcompany.billsby.com</span>
        <button class='bfif-btn bfif-btn-primary'>Disconnect</button>
      </div>

      <div class='billsby-description'>
        Here are some next steps you can take to configure your subscription billing services.
      </div>

      <div class="billsby-button-list-item">
        <div class="billsby-button-label-holder">
          <div class="billsby-button-title">Subscribe and Manage Account Buttons</div>
          <div class="billsby-button-subtitle">Put subscribe and manage account buttons on the appropriate pages of your website</div>
        </div>
        <button class='bfif-btn bfif-btn-primary'>Buttons</button>
      </div>

      <div class="billsby-button-list-item">
        <div class="billsby-button-label-holder">
          <div class="billsby-button-title">Account Synchronization</div>
          <div class="billsby-button-subtitle">Change your account synchronization settings</div>
        </div>
        <button class='bfif-btn bfif-btn-primary'>Account Sync</button>
      </div>

      <div class="billsby-button-list-item">
        <div class="billsby-button-label-holder">
          <div class="billsby-button-title">Access Control</div>
          <div class="billsby-button-subtitle">Learn how to use access control as part of your Billsby for WordPress installation</div>
        </div>
        <button class='bfif-btn bfif-btn-primary'>Access Control</button>
      </div>

      <div class="billsby-button-list-item">
        <div class="billsby-button-label-holder">
          <div class="billsby-button-title">Products, Plans and Cycles</div>
          <div class="billsby-button-subtitle">Create new products, plans and cycles on the Billsby website</div>
        </div>
        <button class='bfif-btn bfif-btn-primary'>Login to Billsby</button>
      </div>
    </div> 
  </div>  
  <!-- End Screen 4 -->

</div>