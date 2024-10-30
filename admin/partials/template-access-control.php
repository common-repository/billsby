<input type=hidden name='db_access_control' id='db_access_control' value='<?= esc_attr($user_config->access_control) ?>'/>
<input type=hidden name='db_restriction_message' id='db_restriction_message' value='<?= esc_attr($user_config->restriction_message) ?>'/>

<div class='billsby-container billsby-access-control-page'>
  <div class='billsby-header-alt'>
    <div class='billsby-header-title-alt'> 
      Billsby for Wordpress.
    </div>
  </div>

  <div class='billsby-body-container'>
    <div class='billsby-description'>
      With access control, you can limit access to specific posts, pages or sections of pages based on the feature tags that users have as part of their plan. There's more information on the <a href="https://support.billsby.com/docs/wordpress" target="_blank">Billsby support site</a> about adding feature tags to your plans.
    </div>

    <div class='billsby-access-control'>
      <input type="checkbox" class="billsby-checkbox" id='billsby-access-control-box' />
      <span>Turn on access control (this will also turn on account synchronization if it's currently off)</span>
    </div>

    <div class="billsby-restriction-msg">
      <div class='billsby-form-input-label'>
        Restriction message
      </div>
      <div class='billsby-description'>
        This message will be displayed instead of the content if a user tries to access content their subscription does not include. You can include shortcodes.
      </div>

      <textarea class="billsby-textarea" id="billsby-restriction-message" rows="4"></textarea>
      <button class='bfif-btn bfif-btn-primary' id='billsby-ac-update-settings'>Update Settings</button>
    </div>

    <div class='billsby-section billsby-access-control-sc'>
      <div class='billsby-section-title'>
        Access control shortcodes
      </div>
      <div class='billsby-section-content'>

        <div class='billsby-section-content-details'>
          <div class='bscd-label'>Basic restriction</div>
          <div class='bscd-description'>
            <div class='bscd-copy-text'>[billsby-restrict] Content [/billsby-restrict]</div>
            <div class='bscd-paragraph'>
              Content wrapped within the shortcode will only be visible to users with an active subscription
            </div>
          </div>
        </div>

        <div class='billsby-section-content-details'>
          <div class='bscd-label'>Feature tag restriction</div>
          <div class='bscd-description'>
            <div class='bscd-copy-text'>
              [billsby-restrict featuretag="one,two,three"] Content [/billsby-restrict]
            </div>
            <div class='bscd-paragraph'>
              Content wrapped within the shortcode will only be visible to users with at least one of the listed feature tags in their subscription.
            </div>
          </div>
        </div>

        <div class='billsby-section-content-details'>
          <div class='bscd-label'>Multi-feature tag restriction</div>
          <div class='bscd-description'>
            <div class='bscd-copy-text'>
              [billsby-restrict featuretag="one,two,three" tagmode="alltags"] Content [/billsby-restrict]
            </div>
            <div class='bscd-paragraph'>
              Content wrapped within the shortcode will only be visible to users with all of the listed feature tags in their subscription.
            </div>
          </div>
        </div>

        <div class='billsby-section-content-details'>
          <div class='bscd-label'>Inverse restrictions</div>
          <div class='bscd-description'>
            <div class='bscd-copy-text'>
              display-mode="inverse"
            </div>
            <div class='bscd-paragraph'>
              Add the above rule to any shortcode to inverse the display rules. For example, adding the rule to the second tag will mean the content shows only to users who do not have at least one of the feature tags listed in their subscription.
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>