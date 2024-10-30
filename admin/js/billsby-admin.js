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

  // Variables
  var ajaxurl = billsby_requests.ajaxurl;
  var have_changes = false;

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

    // If user clicked update settings
    $("#billsby-update-settings").click(function () {
      var account_sync_input = $("#billsby-account-sync-box").prop("checked");
      var logout_url_input = $(".bfif-log-out-url").val();
      var postdata = {
        action: "admin_ajax_request",
        param: "update_billsby_account_settings",
        data: {
          logout_url: logout_url_input,
          account_sync: account_sync_input,
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
            `<div class='billsby-notification  billsby-notification-warning'>Account Sync Settings did not update! Nothing was changed.</div>`
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

    // show saved values
    if (accessControl == "1")
      $("#billsby-access-control-box").attr("checked", "checked");

    $("#billsby-restriction-message").val(restrictionMessage);

    // If user clicked update settings
    $("#billsby-ac-update-settings").click(function () {
      accessControl = $("#billsby-access-control-box").prop("checked");
      restrictionMessage = $("#billsby-restriction-message").val();
      var postdata = {
        action: "admin_ajax_request",
        param: "update_billsby_access_settings",
        data: {
          restriction_message: restrictionMessage,
          access_control: accessControl,
          account_sync: null,
        },
      };

      // Turn on account sync if access_control is on
      if (accessControl) {
        postdata.data.account_sync = true;
      }

      console.log(postdata);

      jQuery.post(ajaxurl, postdata, function (response) {
        var res = JSON.parse(response);
        console.log(res.message);
        if (res.status === "success") {
          $(".billsby-restriction-msg").after(
            `<div class='billsby-notification-container'>
              <div class='billsby-notification billsby-notification-success'>
                Success updating Access Control settings!
              </div>
              <br />
            </div>`
          );
        } else {
          $(".billsby-restriction-msg").after(
            `<div class='billsby-notification-container'>
              <div class='billsby-notification billsby-notification-warning'>
                Access Control settings did not update! Nothing was changed.
              </div>
              <br />
            </div>`
          );
        }
        setTimeout(function () {
          $(".billsby-notification-container").remove();
        }, 3000);
      });
    });
  }
})(jQuery);
