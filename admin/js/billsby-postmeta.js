(function ($) {
  "use strict";

  console.log("POST META SCRIPT");

  // const container = $();
  const featureTagsWrapper = $("#js-feature-tags-wrapper");
  const featureTagInput = $("#js-feature-tag-input");

  const addFeatureTag = function (value) {
    if (/^[a-z]*$/.test(value) === true) {
      const featureTag = `<div class="billsby-feature-tag"><span class="billsby-remove-tag">X</span><div>${value}<div></div>`;

      $(featureTagsWrapper).append(featureTag);

      // add hidden fields
      const hiddenField = `<input type="hidden" class="js-feature-tags" name="billsby_feature_tags[]" value="${value}" />`;

      $("#js-billsby-meta-box").prepend(hiddenField);

      // add class to tags wrapper
      featureTagsWrapper.addClass("feature-tags-wrapper");
    } else {
      window.alert(
        "Feature hags should only be lowercase letters. UPPERCASE letters, numbers (0-9), and special characters are invalid."
      );
    }
  };

  const removeFeatureTag = function (node, value) {
    // remove feature tag
    node.remove();

    // remove hidden field base on value
    $(`#billsby_meta_box :input[value="${value}"]`).remove();
  };

  // click event listener for adding tags
  $("#js-add-tag-btn").on("click", function (e) {
    e.preventDefault();
    const tagName = featureTagInput.val();
    const featureTags = $(".billsby-feature-tag");

    let isNew = true;

    if (!tagName) {
      // if input field is empty, do nothing
      return;
    }

    // get all tags to check if current tag exists
    if (featureTags.length > 0) {
      featureTags.each(function (idx) {
        let existingTagName = $(this).text().substring(1);
        if (existingTagName.toLowerCase() === tagName.toLowerCase()) {
          isNew = false;
        }
      });
    }

    if (isNew) {
      addFeatureTag(tagName);
      // reset field value
      featureTagInput.val("");
    }
  });

  // click event listener for removing tags
  $("#js-billsby-meta-box").on("click", ".billsby-remove-tag", function (e) {
    e.preventDefault();
    const target = $(e.target);
    const tagName = target.next().text();

    removeFeatureTag(target.parent(), tagName);
  });
})(jQuery);
