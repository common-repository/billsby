(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(function () {
    console.log("Loaded plugin script");
  });

  var pathname = window.location.pathname;
  var urlParams = new URLSearchParams(window.location.search);
  var page_name = urlParams.get("page");

  /**
   *   Script for Settings
   */

  // Variables
  var ajaxurl = billsby_requests.ajaxurl;

  if (page_name === "billsby-plugin-settings") {
    var dbSecretKey = $("#db_secret_key").val();
    var dbCompanyId = $("#db_company_id").val();
    var dbApiKey = $("#db_api_key").val();
    var dbWebhookStatus = $("#db_webhook_status").val();
    var companyId = dbCompanyId;
    var apiKey = dbApiKey;
    var validSecretKey = dbWebhookStatus === "1" ? true : false;
    var validCombinationOfKeys = false;
    var urlPath = window.location.origin + "/wp-json/billsby/endpoint";

    console.log(urlPath, validSecretKey);

    // Change the billsby-webhook-url to the actual url of the WP site
    $(".billsby-webhook-url").html(`${urlPath}`);

    var mode = 0; //   Mode of Page: 0 for setup; 1 for update
    if (dbApiKey !== "" && dbCompanyId !== "") mode = 1;

    var companyName = dbCompanyId;
    if (companyName === undefined || companyName === "")
      companyName = "yourcompany";

    //   Billsby Forms
    var billsbyForms = `
    <div class='billsby-form-input-group center-v'>
      <div class='billsby-form-input-label'>
        Company ID
      </div>
      <div class='billsby-form-input-field'>
        <input type='text' class='bfif-input-text' id='bfif-company-id' value='${dbCompanyId}'/> .billsby.com
      </div>
    </div>
  
    <div class='billsby-form-input-group'>
      <div class='billsby-form-input-label'>
        API key
      </div>
      <div class='billsby-form-input-field'>
        <input type='text' class='bfif-input-text bfif-api-key' id='bfif-api-key' value='${dbApiKey}'/>
  
        <br/>
  
        <br/>
  
        <div class='bfif-description'>
          Find this in Settings > Configuration > API Keys and Webhooks > API Keys
        </div>
      
      <div id='billsby-combination-notifications'></div>
  
      </div>
    </div>
  
    <div class='billsby-form-input-group'>
      <div class='billsby-form-input-label'>
        Webhook URL
      </div>
      <div class='billsby-form-input-field'>
        <div class='bfif-description'>
          In Settings > Configuration > API Keys and Webhooks > Webhooks set the URL to:
        </div>
  
        <br/>
  
        <div class='bfif-copy-text'>${urlPath}</div>
  
        <br/>
  
        <div class='bfif-description'>
          And set the secret key to:
        </div>
  
        <br/>
  
        <div class='bfif-copy-text'>
          ${dbSecretKey}
        </div>
  
        <br/>
  
        <div class='bfif-description'>
          Then press the test button next to Customer Created
        </div>
  
        <br/>
        
        <div id="billsby-connect-notifications"></div>
  
        <br/>
        <br/>
  
        <button class='bfif-btn bfif-btn-primary' id='connect-billsby-btn' disabled>
          Connect Billsby
        </button>
      </div>
    </div> `;

    // Billsby settings page
    var setupSettingsPage = `
      <div class='billsby-header'>
        <div class='billsby-header-title'>
          Welcome to Billsby <br/>
          for Wordpress
        </div>
        <div  class='billsby-header-description'>
          Powering subscription billing for <br/>
          your Wordpress powered websites.
        </div>
  
        <a href='https://app.billsby.com/registration' target='_blank' class='billsby-header-button'>
          Sign up for a Billsby account (if you haven't already)
        </a>
      </div>
  
      <div class='billsby-body-container'>
        <div class='billsby-form'>
  
          ${billsbyForms}
  
        </div>
      </div>`;
    var updateSettingsPage = `
      <div class='billsby-header-alt'>
        <div class='billsby-header-title-alt'> 
          Billsby for Wordpress.
        </div>
      </div>
  
      <div class='billsby-body-container'>
        <div class='billsby-form'>
        
          <div class='billsby-form-input-group center-v'>
            <div class='billsby-form-input-label'>
              Header Code
            </div>
            <div class='billsby-form-input-field'>
              <div class='billsby-notification  billsby-notification-success'>
                The header code for <b>${companyName}</b> is installed on your website.
              </div>
            </div>
          </div>
  
          ${billsbyForms}
  
        </div>
      </div>`;

    var showWebhookStatus = (webhook_status) => {
      if (webhook_status === "0") {
        if (mode === 0) {
          $("#billsby-connect-notifications").html(
            `<div class='billsby-notification  billsby-notification-warning'>
              Waiting to receive test webhook. Please follow the steps above to send a test webhook so we can finish off the set up process.
            </div>`
          );
        } else {
          $("#billsby-connect-notifications").html(
            `<div class='billsby-notification  billsby-notification-warning'>
            To proceed with changing your company ID, please send a test webhook as detailed above
          </div>`
          );
        }
      } else if (webhook_status === "1") {
        $("#billsby-connect-notifications").html(
          `<div class='billsby-notification billsby-notification-success billsby-notification-with-btn'>
          <span> Your webhooks is configured successfully!</span>
          <button class="bfif-btn bfif-btn-primary billsby-wizard-next-btn"> Next step </button>
        </div>`
        );
      } else {
        $("#billsby-connect-notifications").html(
          `<div class='billsby-notification  billsby-notification-danger'>
            Test webhook received with the wrong secret key. Please correct the secret key and send another test webhook to continue with setup.
          </div>`
        );
      }
    };

    showWebhookStatus(dbWebhookStatus);

    //   Dynamically get value of company id
    $("#bfif-company-id").on("input", function () {
      if (this.value !== "" || this.value !== undefined) {
        companyName = this.value;
        companyId = this.value;
        enableBillsbyConnect();
      }
    });

    //   Dynamically get value of api key
    $("#bfif-api-key").on("input", function () {
      if (this.value !== "" || this.value !== undefined) {
        apiKey = this.value;
        enableBillsbyConnect();
      }
    });

    //   Checks company id and api key to ENABLE connect billsby button
    function enableBillsbyConnect() {
      var boolCompanyId = companyId === "" ? false : true;
      boolCompanyId = companyId === undefined ? false : true;

      var boolApiKey = apiKey === "" ? false : true;
      boolApiKey = apiKey === undefined ? false : true;

      if (boolCompanyId && boolApiKey) {
        $(".bfif-btn").removeAttr("disabled");
      }
    }

    // Action when connect billsby button was clicked
    $("#connect-billsby-btn").click(function () {
      console.log("clicked");
      companyId = $("#bfif-company-id").val();
      apiKey = $("#bfif-api-key").val();
      //   console.log(company_id, api_key);

      validCombinationOfKeys = apiKey.includes(companyId);

      if (validCombinationOfKeys === false) {
        $("#billsby-combination-notifications").html(
          `<br/><div class='billsby-notification billsby-notification-danger'>This combination of company ID and API key doesn’t seem right</div>`
        );
      } else {
        $("#billsby-combination-notifications").html("");
      }

      if (validSecretKey === false) {
        $("#billsby-connect-notifications").html(
          `<div class='billsby-notification  billsby-notification-danger'>
            Please correct the secret key and then send another test webhook
          </div>`
        );
      }

      if (validCombinationOfKeys && validSecretKey) {
        // Send AJAX Request to WP
        var postdata = {
          action: "admin_ajax_request",
          param: "check_and_connect_to_billsby",
          data: {
            company_id: companyId,
            api_key: apiKey,
          },
        };
        jQuery.post(ajaxurl, postdata, function (response) {
          var res = JSON.parse(response);
          console.log(res.message);

          if (res.status === "success") {
            $("#billsby-settings-page").html(updateSettingsPage);
            location.reload(true);
          }
        });
      }
    });

    // Check webhooks every 3 seconds
    setInterval(function () {
      var postdata = {
        action: "admin_ajax_request",
        param: "check_billsby_webhook_status",
        data: {},
      };
      console.log(dbCompanyId && dbApiKey);
      if (dbCompanyId && dbApiKey) {
        console.log("Checking webhook status");
        jQuery.post(ajaxurl, postdata, function (response) {
          var res = JSON.parse(response);
          if (dbWebhookStatus !== res.webhook_status) {
            console.log("There is a change in the webhook");
            showWebhookStatus(res.webhook_status);
            dbWebhookStatus = res.webhook_status;
          }
        });
      } else {
        console.log("No Company Id and Api key for checking webhook");
      }
    }, 5000);
  }

  /**
   *   Script for Account Sync
   */

  if (page_name === "billsby-plugin-account-synchronization") {
    var acccounSync = $("#db_account_sync").val();
    var logOutURL = $("#db_logout_url").val();
    if (logOutURL !== null || logOutURL !== "")
      $(".bfif-log-out-url").val(logOutURL);
    if (acccounSync == "1")
      $("#billsby-account-sync-box").attr("checked", "checked");

    // Get changes when check box changes value
    $("#billsby-account-sync-box").change(function () {
      if (this.checked) acccounSync = true;
      else acccounSync = false;
    });

    // Get value of logout URL if not null or empty
    $(".bfif-log-out-url").on("input", function () {
      if (this.value !== "" || this.value !== undefined) logOutURL = this.value;
    });

    // If user clicked update settings
    $("#billsby-update-settings").click(function () {
      var postdata = {
        action: "admin_ajax_request",
        param: "update_billsby_account_settings",
        data: {
          logout_url: logOutURL,
          account_sync: acccounSync,
        },
      };

      jQuery.post(ajaxurl, postdata, function (response) {
        var res = JSON.parse(response);
        console.log(res.message);
        if (res.status === "success") {
          console.log("update settings", logOutURL);
          $(".billsby-body-container").append(
            `<div class='billsby-notification  billsby-notification-success'>Success Updating Account Sync Settings!</div>`
          );
        } else {
          $(".billsby-body-container").append(
            `<div class='billsby-notification  billsby-notification-danger'>Failed Updating Account Sync Settings!</div>`
          );
        }
        setTimeout(function () {
          $(".billsby-notification").remove();
        }, 5000);
      });
    });
  }

  /**
   * Script for Access Control
   */

  if (page_name === "billsby-plugin-access-control") {
    var accessControl = $("#db_access_control").val();
    var restrictionMessage = $("#db_restriction_message").val();

    if (accessControl == "1")
      $("#billsby-access-control-box").attr("checked", "checked");

    $("#billsby-restriction-message").val(restrictionMessage);

    // Get changes when check box changes value
    $("#billsby-access-control-box").change(function () {
      if (this.checked) accessControl = true;
      else accessControl = false;

      var postdata = {
        action: "admin_ajax_request",
        param: "update_billsby_access_control",
        data: {
          access_control: accessControl,
        },
      };
      jQuery.post(ajaxurl, postdata, function (response) {
        var res = JSON.parse(response);
        if (res.status === "success") {
          console.log("update settings", logOutURL);
          $(".billsby-access-control").after(
            `<div class='billsby-notification-container'>
              <div class='billsby-notification  billsby-notification-success'>
                ${res.message}
              </div>
              <br/>
            </div>`
          );
        } else {
          $(".billsby-access-control").after(
            `<div class="billsby-notification-container"><div class='billsby-notification  billsby-notification-danger'>Failed Updating Access Control!</div><br/></div>`
          );
        }
        setTimeout(function () {
          $(".billsby-notification-container").remove();
        }, 3000);
      });
    });

    //setup before functions
    var typingTimer; //timer identifier
    var doneTypingInterval = 2500; //time in ms (2.5 seconds)

    //on keyup, start the countdown
    $("#billsby-restriction-message").keyup(function () {
      clearTimeout(typingTimer);
      if ($("#billsby-restriction-message").val()) {
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
      }
    });

    // $("#billsby-restriction-message").keyup(_.debounce(doneTyping, 500));

    //user is "finished typing," do something
    function doneTyping() {
      restrictionMessage = $("#billsby-restriction-message").val();
      console.log(restrictionMessage);
      var postdata = {
        action: "admin_ajax_request",
        param: "update_restriction_message",
        data: {
          restriction_message: restrictionMessage,
        },
      };

      jQuery.post(ajaxurl, postdata, function (response) {
        var res = JSON.parse(response);
        if (res.status === "success") {
          console.log("update settings", logOutURL);
          $("#billsby-restriction-message").after(
            `<div class='billsby-notification-container'>
              <div class='billsby-notification  billsby-notification-success'>
                ${res.message}
              </div>
              <br/>
            </div>`
          );
        } else {
          $("#billsby-restriction-message").after(
            `<div class="billsby-notification-container"><div class='billsby-notification  billsby-notification-danger'>Failed Updating Restriction Message!</div><br/></div>`
          );
        }
        setTimeout(function () {
          $(".billsby-notification-container").remove();
        }, 3000);
      });
    }
  }
})(jQuery);
