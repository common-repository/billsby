(function ($) {
  "use strict";

  var urlParams = new URLSearchParams(window.location.search);
  var page_name = urlParams.get("page");

  if (page_name === "billsby-plugin-settings") {
    $(function () {
      console.log("Loaded settings script");
    });

    /* -------------------------------------------------------------------------- */
    /*                                  Variables                                 */
    /* -------------------------------------------------------------------------- */

    var screen = 1;
    var isSetupComplete =
      $("#db_setup_complete").val() === "0" ||
      $("#db_setup_complete").val() === ""
        ? false
        : true;
    var ajaxurl = billsby_requests.ajaxurl;
    var localpath = "wordpress";
    var webhookUrlPath =
      window.location.origin !== "http://localhost"
        ? `${window.location.origin}/wp-json/billsby/endpoint`
        : `${window.location.origin}/${localpath}/wp-json/billsby/endpoint`;
    var company_id = $("#bfif-company-id").val();
    var api_key = $("#bfif-api-key").val();
    var secret_key = $("#db_secret_key").val();

    var checkbox_subscribe_and_manage = $(
      "#checkbox-subscribe-and-manage-account"
    ).is(":checked");
    var checkbox_account_syncronization = $(
      "#checkbox-account-syncronization"
    ).is(":checked");
    var checkbox_access_control = $("#checkbox-access-control").is(":checked");
    var dbWebhookStatus = $("#db_webhook_status").val();
    var is_api_working = false;
    var permalink_structure = $("#option_permalink_structure").val();

    /* -------------------------------------------------------------------------- */
    /*                              Misconfig Functions                           */
    /* -------------------------------------------------------------------------- */
    const checkAPIStatus = function () {
      return new Promise((resolve, reject) => {
        console.log("check api status", secret_key);
        $.ajax({
          url: webhookUrlPath,
          type: "POST",
          // contentType: "application/json; charset=utf-8",
          headers: { secret: secret_key },
          success: (data) => {
            resolve(data);
          },
          error: (data) => {
            console.log(data);
            if (data.responseJSON) {
              resolve();
            } else {
              reject();
            }
          },
        });
      });
    };

    const isPermalinkCorrect = function () {
      if (permalink_structure) {
        return true;
      }

      return false;
    };

    /* -------------------------------------------------------------------------- */
    /*                              Webhook Functions                             */
    /* -------------------------------------------------------------------------- */
    var showWebhookStatus = (webhook_status) => {
      if (webhook_status === "0") {
        $("#billsby-webhook-waiting").show();
        $("#billsby-webhook-configured").hide();
        $("#billsby-webhook-error").hide();
      } else if (webhook_status === "1") {
        $("#billsby-webhook-waiting").hide();
        $("#billsby-webhook-configured").show();
        $("#billsby-webhook-error").hide();
      } else {
        $("#billsby-webhook-waiting").hide();
        $("#billsby-webhook-configured").hide();
        $("#billsby-webhook-error").show();
      }
    };

    /* -------------------------------------------------------------------------- */
    /*                              Wizard Functions                              */
    /* -------------------------------------------------------------------------- */
    const showScreen = (current_screen) => {
      $(".billsby-header-title").html("Welcome to Billsby for WordPress.");
      $(".billsby-header-description").html(
        "Powering subscription billing for your WordPress powered website."
      );
      if (current_screen === 1) {
        console.log("screen 1");
        $(".billsby-mis-config").hide();
        $(".billsby-wizard-screen-1").show();
        $(".billsby-wizard-screen-2").hide();
        $(".billsby-wizard-screen-3").hide();
      } else if (current_screen === 2) {
        console.log("screen 2");
        $(".billsby-mis-config").hide();
        $(".billsby-header-button").hide();
        $(".billsby-wizard-screen-1").hide();
        $(".billsby-wizard-screen-2").show();
        $(".billsby-wizard-screen-3").hide();
      } else {
        console.log("screen 3");
        $(".billsby-mis-config").hide();
        $(".billsby-wizard-screen-1").hide();
        $(".billsby-wizard-screen-2").hide();
        $(".billsby-wizard-screen-3").show();
      }
    };

    const showCompleteSetupScreen = () => {
      $(".billsby-wizard").hide();
      $(".billsby-header-title").html("Billsby for WordPress is now setup.");
      $(".billsby-header-description").html(
        "You can now start using Billsby to manage your subscription business."
      );
      $(".billsby-header-description").width(461);
      $(".billsby-register-btn").hide();
      $(".billsby-help-and-support-btn").css("display", "inline-block");
      $(".billsby-complete-setup").show();
    };

    const showMisConfigScreen = () => {
      $(".billsby-header-title").html("Welcome to Billsby for WordPress.");
      $(".billsby-header-description").html(
        "Powering subscription billing for your WordPress powered website."
      );
      $(".billsby-mis-config").show();
      $(".billsby-wizard-screen-1").hide();
      $(".billsby-wizard-screen-2").hide();
      $(".billsby-wizard-screen-3").hide();
      $(".billsby-complete-setup").hide();
      $(".billsby-register-btn").css("display", "inline-block");
      $(".billsby-help-and-support-btn").hide();
    };

    /* -------------------------------------------------------------------------- */
    /*                              Error Functions                               */
    /* -------------------------------------------------------------------------- */
    const toggleError = function (input_el, err_msg_el, has_error) {
      if (has_error) {
        err_msg_el.show();
        input_el.addClass("billsby-has-error");
      } else {
        err_msg_el.hide();
        input_el.removeClass("billsby-has-error");
      }
    };

    /* ---------------------------------- Start --------------------------------- */
    checkAPIStatus()
      .then(function (_) {
        console.log("REST API is working");
        // WP REST API is working
        // now check if permalink is properly set
        if (isPermalinkCorrect()) {
          console.log("Permalink is correct!");
          // permalink is not using the Plain structure
          // now check if setup is complete
          if (!isSetupComplete) {
            console.log("Setup Not Complete");
            // toggle header buttons
            $(".billsby-register-btn").css("display", "inline-block");
            $(".billsby-help-and-support-btn").hide();
            $(".billsby-complete-setup").hide();
            // show screen 1
            showScreen(screen);

            // Change the billsby-webhook-url to the actual url of the WP site
            $(".billsby-webhook-url").html(`${webhookUrlPath}`);

            // show default webhook status
            showWebhookStatus(dbWebhookStatus);

            // check for change in webhook status
            setInterval(function () {
              if (company_id && api_key) {
                console.log("Checking webhook status");
                var postdata = {
                  action: "admin_ajax_request",
                  param: "check_billsby_webhook_status",
                  data: {},
                };
                jQuery.post(ajaxurl, postdata, function (response) {
                  var res = JSON.parse(response);
                  console.log("webhook res", res);
                  if (dbWebhookStatus !== res.webhook_status) {
                    console.log("There is a change in the webhook");
                    showWebhookStatus(res.webhook_status);
                    dbWebhookStatus = res.webhook_status;
                  }
                });
              }
            }, 5000);
          } else {
            console.log("Setup complete!");
            showCompleteSetupScreen();
          }
        } else {
          // show permalink error message
          $("#billsby-permalink-error").show();

          // show misconfig screen
          showMisConfigScreen();
        }
      })
      .catch(function (_) {
        console.log("REST API is not working [Catch]");
        // WP REST API is not working

        // show WP REST API error message
        $("#billsby-webhook-404").show();

        // also show permalink error if permalink is not properly set
        if (!isPermalinkCorrect()) {
          $("#billsby-permalink-error").show();
        }

        // show misconfig screen
        showMisConfigScreen();
      });

    /* ------------------- Billsby Wizard Next Button Function ------------------ */

    $(".billsby-wizard-next-btn").click(function () {
      console.log("clicked next button");
      if (screen === 1) {
        company_id = $("#bfif-company-id").val();
        api_key = $("#bfif-api-key").val();

        // Check if company id and api key has inputs
        if (company_id == "" || api_key == "") {
          if (api_key == "") {
            toggleError($("#bfif-api-key"), $(".billsby-apikey-error"), true);
          }
          console.log("please enter input");
        }
        // Check combination of company id and api key
        else {
          const data = null;
          const xhr = new XMLHttpRequest();
          xhr.addEventListener("readystatechange", function () {
            if (this.readyState === this.DONE) {
              if (this.status === 200) {
                console.log("API Key is VALID");
                toggleError(
                  $("#bfif-api-key"),
                  $(".billsby-apikey-error"),
                  false
                );
                $("#bfif-company-id2").html(`${company_id}.billsby.com`);
                screen++;
                showScreen(screen);
              } else {
                console.log("API Key is INVALID");
                toggleError(
                  $("#bfif-api-key"),
                  $(".billsby-apikey-error"),
                  true
                );
              }
            }
          });
          xhr.open(
            "GET",
            `https://public.billsby.com/api/v1/rest/core/${company_id}/customers?page=1&pageSize=10`
          );
          xhr.setRequestHeader("apikey", api_key);
          xhr.send(data);

          // api key validation
          // var validCombinationOfKeys = api_key.includes(company_id);

          // If valid combination
          // if (validCombinationOfKeys) {
          //   toggleError($("#bfif-api-key"), $(".billsby-apikey-error"), false);
          //   $("#bfif-company-id2").html(`${company_id}.billsby.com`);
          //   screen++;
          // }
          // If wrong combination
          // else {
          //   toggleError($("#bfif-api-key"), $(".billsby-apikey-error"), true);
          //   console.log("wrong combination of keys");
          // }
        }
      } else {
        screen++;
        showScreen(screen);
      }
    });

    /* -------------- Billsby Wizard Complete Setup Button Function ------------- */

    $(".billsby-wizard-complete-btn").click(function () {
      checkbox_subscribe_and_manage = $(
        "#checkbox-subscribe-and-manage-account"
      ).is(":checked");
      checkbox_account_syncronization = $(
        "#checkbox-account-syncronization"
      ).is(":checked");
      checkbox_access_control = $("#checkbox-access-control").is(":checked");

      console.log(
        company_id,
        api_key,
        checkbox_access_control,
        checkbox_account_syncronization,
        checkbox_subscribe_and_manage
      );

      var postdata = {
        action: "admin_ajax_request",
        param: "complete_billsby_setup",
        data: {
          company_id,
          api_key,
          checkbox_access_control,
          checkbox_account_syncronization,
          checkbox_subscribe_and_manage,
        },
      };

      jQuery.post(ajaxurl, postdata, function (response) {
        var res = JSON.parse(response);
        console.log(res.message);

        if (res.status === "success") {
          console.log(res.message);
          isSetupComplete = true;
          console.log("Setup Done");
          showCompleteSetupScreen();

          // Reload page to load the sub menus
          window.location.reload();
        }
      });
    });

    /* -------------------------------------------------------------------------- */
    /*                            Button Link Functions                           */
    /* -------------------------------------------------------------------------- */
    /**
     * Need to add the folder name after `window.location.origin` to work on localhost
     */
    $("#billsby-diconnect-btn").click(function () {
      var postdata = {
        action: "admin_ajax_request",
        param: "disconnect_to_billsby",
        data: {},
      };

      jQuery.post(ajaxurl, postdata, function (response) {
        var res = JSON.parse(response);

        if (res.status === "success") {
          console.log(res.message);
          window.location.reload();
        } else {
          console.log(res.message);
        }
      });
    });

    // Go to Buttons Page
    $("#billsby-go-to-buttons-page").click(function () {
      console.log("go to buttons page");
      window.location.href =
        window.location.origin +
        "/wp-admin/admin.php?page=billsby-plugin-buttons";
    });

    // Go to Accouny Sync Page
    $("#billsby-go-to-account-sync-page").click(function () {
      console.log("go to account sync page");
      window.location.href =
        window.location.origin +
        "/wp-admin/admin.php?page=billsby-plugin-account-synchronization";
    });

    // Go to Access control page
    $("#billsby-go-to-access-control-page").click(function () {
      console.log("go to access control page");
      window.location.href =
        window.location.origin +
        "/wp-admin/admin.php?page=billsby-plugin-access-control";
    });

    // Open Billsby Login Page in new Tab
    $("#billsby-go-to-billsby-website").click(function () {
      window.open("https://app.billsby.com/login", "_blank");
    });

    /* -------------------------------------------------------------------------- */
    /*                            Checkbox Functions                              */
    /* -------------------------------------------------------------------------- */
    //If access control is checked, account synchronization must be checked too
    $("#checkbox-access-control").change(function () {
      if ($(this).prop("checked")) {
        $("#checkbox-account-syncronization").prop("checked", true);
      }
    });

    $("#checkbox-access-control").trigger("change");

    $("#checkbox-account-syncronization").click(function (e) {
      // check if access control is checked
      if ($("#checkbox-access-control").is(":checked")) {
        // if access control is checked, do nothing
        e.preventDefault();
      }
    });
  }
})(jQuery);
