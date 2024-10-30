// (function ($) {
//   "use strict";

//   var urlParams = new URLSearchParams(window.location.search);
//   var page_name = urlParams.get("page");

//   if (page_name === "billsby-plugin-settings") {
//     $(function () {
//       console.log("Loaded settings script");
//     });

//     /* -------------------------------------------------------------------------- */
//     /*                                  Variables                                 */
//     /* -------------------------------------------------------------------------- */

//     var screen = 1;
//     var isSetupComplete = $("#db_setup_complete").val() === "0" ? false : true;
//     var ajaxurl = billsby_requests.ajaxurl;
//     var webhookUrlPath = window.location.origin + "/wp-json/billsby/endpoint";
//     var company_id = $("#bfif-company-id").val();
//     var api_key = $("#bfif-api-key").val();
//     var checkbox_subscribe_and_manage = $(
//       "#checkbox-subscribe-and-manage-account"
//     ).is(":checked");
//     var checkbox_account_syncronization = $(
//       "#checkbox-account-syncronization"
//     ).is(":checked");
//     var checkbox_access_control = $("#checkbox-access-control").is(":checked");
//     var dbWebhookStatus = $("#db_webhook_status").val();
//     var is_api_working = false;

//     // Check if Webhook is working fine
//     const checkAPIStatus = () => {
//       var url = window.location.origin + "/wp-json/billsby/endpoint";
//       jQuery
//         .post(url, {}, function (result) {
//           if (result) {
//             is_api_working = true;
//             console.log("API is working", result);
//           }
//         })
//         .fail(function (response) {
//           if (response.responseJSON) {
//             is_api_working = true;
//             console.log("API is working", response.responseJSON);
//           } else {
//             console.log("API is not working");
//             $(".billsby-body-container").prepend(
//               `<div class="billsby-notification billsby-notification-danger">It looks like webhooks are not correctly configured on your web server, so the Billsby plugin won't work as expected. Please speak to your system administrator or web host about configuring your server to ensure REST API calls from WordPress work successfully.</div> <br/>`
//             );
//           }
//         });
//     };

//     checkAPIStatus();

//     /* -------------------------------------------------------------------------- */
//     /*                              Webhook Functions                             */
//     /* -------------------------------------------------------------------------- */

//     // Change the billsby-webhook-url to the actual url of the WP site
//     $(".billsby-webhook-url").html(`${webhookUrlPath}`);

//     var showWebhookStatus = (webhook_status) => {
//       if (webhook_status === "0") {
//         $("#billsby-webhook-waiting").show();
//         $("#billsby-webhook-configured").hide();
//         $("#billsby-webhook-error").hide();
//       } else if (webhook_status === "1") {
//         $("#billsby-webhook-waiting").hide();
//         $("#billsby-webhook-configured").show();
//         $("#billsby-webhook-error").hide();
//       } else {
//         $("#billsby-webhook-waiting").hide();
//         $("#billsby-webhook-configured").hide();
//         $("#billsby-webhook-error").show();
//       }
//     };

//     showWebhookStatus(dbWebhookStatus);

//     // Check webhooks status every 5 seconds
//     if (!isSetupComplete)
//       setInterval(function () {
//         console.log("Checking webhook status");
//         var postdata = {
//           action: "admin_ajax_request",
//           param: "check_billsby_webhook_status",
//           data: {},
//         };
//         jQuery.post(ajaxurl, postdata, function (response) {
//           var res = JSON.parse(response);
//           if (dbWebhookStatus !== res.webhook_status) {
//             console.log("There is a change in the webhook");
//             showWebhookStatus(res.webhook_status);
//             dbWebhookStatus = res.webhook_status;
//           }
//         });
//       }, 5000);

//     /* -------------------------------------------------------------------------- */
//     /*                              Wizard Functions                              */
//     /* -------------------------------------------------------------------------- */

//     const showScreen = (current_screen) => {
//       if (current_screen === 1) {
//         console.log("screen 1");
//         $(".billsby-wizard-screen-1").show();
//         $(".billsby-wizard-screen-2").hide();
//         $(".billsby-wizard-screen-3").hide();
//       } else if (current_screen === 2) {
//         console.log("screen 2");
//         $(".billsby-header-button").hide();
//         $(".billsby-wizard-screen-1").hide();
//         $(".billsby-wizard-screen-2").show();
//         $(".billsby-wizard-screen-3").hide();
//       } else {
//         console.log("screen 3");
//         $(".billsby-wizard-screen-1").hide();
//         $(".billsby-wizard-screen-2").hide();
//         $(".billsby-wizard-screen-3").show();
//       }
//     };

//     const showCompleteSetupScreen = () => {
//       $(".billsby-wizard").hide();
//       $(".billsby-header-title").html("Billsby for WordPress is now setup.");
//       $(".billsby-header-description").html(
//         "You can now start using Billsby to manage your subscription business."
//       );
//       $(".billsby-header-description").width(461);
//       $(".billsby-register-btn").hide();
//       $(".billsby-help-and-support-btn").show();
//       $(".billsby-complete-setup").show();
//     };

//     if (!isSetupComplete) {
//       $(".billsby-register-btn").show();
//       $(".billsby-help-and-support-btn").hide();
//       $(".billsby-complete-setup").hide();
//       showScreen(screen);
//     } else {
//       showCompleteSetupScreen();
//     }

//     $(".billsby-wizard-next-btn").click(function () {
//       console.log("clicked next button");
//       if (screen === 1) {
//         company_id = $("#bfif-company-id").val();
//         api_key = $("#bfif-api-key").val();

//         // Check if company id and api key has inputs
//         if (company_id == "" || api_key == "") {
//           if (api_key == "") {
//             toggleError($("#bfif-api-key"), $(".billsby-apikey-error"), true);
//           }
//           console.log("please enter input");
//         }
//         // Check combination of company id and api key
//         else {
//           var validCombinationOfKeys = api_key.includes(company_id);

//           // If valid combination
//           if (validCombinationOfKeys) {
//             toggleError($("#bfif-api-key"), $(".billsby-apikey-error"), false);
//             $("#bfif-company-id2").html(`${company_id}.billsby.com`);
//             screen++;
//           }
//           // If wrong combination
//           else {
//             toggleError($("#bfif-api-key"), $(".billsby-apikey-error"), true);
//             console.log("wrong combination of keys");
//           }
//         }
//       } else {
//         screen++;
//       }
//       showScreen(screen);
//     });

//     $(".billsby-wizard-complete-btn").click(function () {
//       checkbox_subscribe_and_manage = $(
//         "#checkbox-subscribe-and-manage-account"
//       ).is(":checked");
//       checkbox_account_syncronization = $(
//         "#checkbox-account-syncronization"
//       ).is(":checked");
//       checkbox_access_control = $("#checkbox-access-control").is(":checked");

//       console.log(
//         company_id,
//         api_key,
//         checkbox_access_control,
//         checkbox_account_syncronization,
//         checkbox_subscribe_and_manage
//       );

//       var postdata = {
//         action: "admin_ajax_request",
//         param: "complete_billsby_setup",
//         data: {
//           company_id,
//           api_key,
//           checkbox_access_control,
//           checkbox_account_syncronization,
//           checkbox_subscribe_and_manage,
//         },
//       };

//       jQuery.post(ajaxurl, postdata, function (response) {
//         var res = JSON.parse(response);
//         console.log(res.message);

//         if (res.status === "success") {
//           console.log(res.message);
//           isSetupComplete = true;
//           console.log("Setup Done");
//           showCompleteSetupScreen();

//           // Reload page to load the sub menus
//           window.location.reload();
//         }
//       });
//     });

//     /* -------------------------------------------------------------------------- */
//     /*                            Button Link Functions                           */
//     /* -------------------------------------------------------------------------- */
//     /**
//      * Need to add the folder name after `window.location.origin` to work on localhost
//      */
//     $("#billsby-diconnect-btn").click(function () {
//       var postdata = {
//         action: "admin_ajax_request",
//         param: "disconnect_to_billsby",
//         data: {},
//       };

//       jQuery.post(ajaxurl, postdata, function (response) {
//         var res = JSON.parse(response);

//         if (res.status === "success") {
//           console.log(res.message);
//           window.location.reload();
//         } else {
//           console.log(res.message);
//         }
//       });
//     });
//     $("#billsby-go-to-buttons-page").click(function () {
//       console.log("go to buttons page");
//       window.location.href =
//         window.location.origin +
//         "/wp-admin/admin.php?page=billsby-plugin-buttons";
//     });
//     $("#billsby-go-to-account-sync-page").click(function () {
//       console.log("go to account sync page");
//       window.location.href =
//         window.location.origin +
//         "/wp-admin/admin.php?page=billsby-plugin-account-synchronization";
//     });
//     $("#billsby-go-to-access-control-page").click(function () {
//       console.log("go to access control page");
//       window.location.href =
//         window.location.origin +
//         "/wp-admin/admin.php?page=billsby-plugin-access-control";
//     });
//     $("#billsby-go-to-billsby-website").click(function () {
//       window.location.href = "https://app.billsby.com/login";
//     });

//     /* -------------------------------------------------------------------------- */
//     /*                              Error Functions                               */
//     /* -------------------------------------------------------------------------- */
//     const toggleError = function (input_el, err_msg_el, has_error) {
//       if (has_error) {
//         err_msg_el.show();
//         input_el.addClass("billsby-has-error");
//       } else {
//         err_msg_el.hide();
//         input_el.removeClass("billsby-has-error");
//       }
//     };
//   }
// })(jQuery);

