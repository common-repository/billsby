(function ($) {
  let pathname = window.location.pathname;
  let localfilePath = "wordpress";

  if (
    pathname === `/${localfilePath}/wp-admin/profile.php` ||
    pathname === `/wp-admin/profile.php` ||
    pathname === `/${localfilePath}/wp-admin/user-edit.php` ||
    pathname === `/wp-admin/user-edit.php`
  ) {
    let user_id = 1;
    if (
      pathname === `/${localfilePath}/wp-admin/user-edit.php` ||
      pathname === "/wp-admin/user-edit.php"
    ) {
      let urlParams = new URLSearchParams(window.location.search);
      user_id = urlParams.get("user_id");
    }

    /* ----------------------------- Show Meta Data ----------------------------- */
    let urlPath =
      window.location.origin === "http://localhost"
        ? `${window.location.origin}/${localfilePath}/wp-json/billsby/get-billsby-info?user_id=${user_id}`
        : `${window.location.origin}/wp-json/billsby/get-billsby-info?user_id=${user_id}`;

    // Use ajax to get meta data of user
    let ajaxurl = billsby_requests.ajaxurl;
    let postdata = {
      action: "admin_ajax_request",
      param: "get_billsby_meta",
      data: {
        user_id,
      },
    };
    jQuery.post(ajaxurl, postdata, function (response) {
      console.log(response);
      let data = JSON.parse(response);
      let billsby_customer_id = data.billsby_customerId;
      let billsby_subscription_data = data.billsby_subscriptionData;
      let billsby_feature_tags = data.billsby_featureTags;

      if (billsby_customer_id) {
        // Add another section in the user profile
        $(`
        <h3>Billsby Info</h2>
        <table class="form-table billsby-usermeta-table" role="presentation">
          <tbody>
            <tr>
              <th><label for="billsby_customer_id">Billsby Customer ID</label></th>
              <td><input type="text" name="billsby_customer_id" id="billsby_customer_id" class="regular-text ltr" disabled value="${billsby_customer_id}"/></td>
            </tr>
            <tr>
              <th><label for="billsby_customer_id">Billsby Subscriptions</label></th>
              <td>
                <ul id="billsby-subscription-list"> </ul>
              </td>
            </tr>
            <tr>
              <th><label for="billsby_feature_tags">Billsby Feature Tags</label></th>
              <td>
                <ul id="billsby-tags-list"> </ul>
              </td>
            </tr>
          </tbody>
        </table>
      `).insertAfter("#user_id");

        function render_subscrtion(status) {
          if (status === "Active")
            return " billsby-subscription-status billsby-subscription-status-active";
          else if (status === "Suspended")
            return "billsby-subscription-status billsby-subscription-status-suspended";
          else if (status === "NA") return "billsby-subscription-status";
        }

        if (billsby_subscription_data.length > 0) {
          billsby_subscription_data.forEach((subscription) => {
            $("#billsby-subscription-list").append(`
              <li class="billsby-subscription-list-items">
                <ul>
                <li>
                  <b>Subscription Id: </b>
                  <span class="billsby-subscription-details">${
                    subscription.SubscriptionUniqueId
                  }</span>
                </li>
                <li>
                  <b>Subscription Status: </b>
                  <span class="billsby-subscription-details ${render_subscrtion(
                    subscription.SubscriptionStatus
                  )}">${subscription.SubscriptionStatus}</span>
                </li>
                <li>
                  <b>Product Name: </b>
                  <span class="billsby-subscription-details">${
                    subscription.BillsbyProductName
                  }</span>
                </li>
                <li>
                  <b>Plan Name: </b>
                  <span class="billsby-subscription-details">${
                    subscription.BillsbyPlanName
                  }</span>
                </li>
                <ul/>
              </li>`);
          });
        } else {
          $("#billsby-subscription-lists").append(`None`);
        }

        if (billsby_feature_tags) {
          if (billsby_feature_tags.length > 0) {
            billsby_feature_tags.forEach((tags) => {
              $("#billsby-tags-list").append(`
                <li class="billsby-subscription-list-items">
                  <span class="billsby-tags">${tags}</span>
                </li>`);
            });
          } else {
            $("#billsby-tags-list").append(`None`);
          }
        } else {
          $("#billsby-tags-list").append(`None`);
        }
      }
    });
  }
})(jQuery);
