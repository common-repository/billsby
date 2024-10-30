<input type=hidden name='db_account_sync' id='db_account_sync' value='<?= esc_attr($user_config->account_sync) ?>'/>
<input type=hidden name='db_logout_url' id='db_logout_url' value='<?= esc_attr($user_config->logout_url) ?>'/>

<div class='billsby-container'>
  <div class='billsby-header-alt'>
    <div class='billsby-header-title-alt'> 
      Billsby for Wordpress.
    </div>
  </div>

  <div class='billsby-body-container'>
    <div class='billsby-description'>
      With account synchronization, when customers sign up for a Billsby subscription, their WordPress accounts will automatically be updated with their subscription details. This means that customers will have to be logged in to subscribe, and will be able to manage their account without having to separately login to Billsby using the subscribe and manage account buttons. It also means that you'll be able to see each users customer ID, subscription ID and other details in WordPress.
    </div>

    <div class='billsby-account-sync'>
      <input type="checkbox" class="billsby-checkbox" id='billsby-account-sync-box' />
      <span> Turn on account synchronization </span>
    </div>

    <div class='billsby-form-input-group'>
      <div class='billsby-form-input-label billsby-w-223px'>
       Logged out URL
      </div>

      <div class='billsby-form-input-field'>
        <input type='text' class='bfif-input-text bfif-log-out-url' value='/wp-login.php?action=register' placeholder='/wp-login.php?action=register'/>
        <div class='bfif-description'>
          If a logged out user tries to setup a subscription, we'll forward them to your registration URL or the URL specified above.
        </div>
        <button class='bfif-btn bfif-btn-primary' id='billsby-update-settings'>Update Settings</button>
      </div>
    </div>
  </div>
</div>