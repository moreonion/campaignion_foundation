/*
 * To be called from Drupal behaviors.
 *
 * Can be used with hidden "other" radio option; the checked radio is set via JavaScript.
 *
 * Sets `.select-or-other-checked` on the other textfield if it is selected.
 *
 * Optionally set the "Other label" as placeholder (e.g. in case the label is hidden).
 */

(function($) {
  $.fn.donationButtons = function(options) {
    // These are the defaults.
    var defaults = {
      setPlaceholder: false
    };
    var settings = $.extend({}, defaults, options);

    // Return if no element was matched
    if (!$(this).length) {
      return this;
    }

    var $element = $(this);
    var $radios = $element.find('input[type="radio"]');
    var $otherRadio = $element.find('input[value="select_or_other"]');
    var $normalRadios = $radios.not($otherRadio);
    var $otherLabel = $element.find(
      'label[for="' + $otherRadio.prop("id") + '"]'
    );
    var $otherText = $element.find('input[type="text"]');

    // Get hidden other checkbox label and use it as placeholder in the other
    // amount text field.
    if (settings.setPlaceholder) {
      $otherText.prop("placeholder", $otherLabel.text());
    }

    // Other amount text field with a valid value checks the other checkbox.
    // Add class to other text field when other checkbox is checked.
    function clearRadios(e) {
      $otherRadio.prop("checked", true).trigger("change");
      $otherText.parent().addClass("select-or-other-checked");
    }
    function clearTextField(e) {
      if (!$otherRadio.prop("checked")) {
        $otherText.parent().removeClass("select-or-other-checked");
      }
    }
    // Switch to selected other textfield when user clicks into it or
    // inputs/changes some data.
    $otherText.on("click", clearRadios);
    $otherText.on("change", clearRadios);
    $normalRadios.on("change", clearTextField);

    // return self for chaining
    return this;
  };
})(jQuery);
