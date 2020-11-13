/*
 * To be called from Drupal behaviors.
 *
 * Can be used with hidden "other" radio option; the checked radio is set via JavaScript.
 *
 * Toggles `.select-or-other-checked` on the other textfield’s wrapper.
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

    $(this).each(function() {
      var $element = $(this);
      var $otherRadio = $element.find('input[value="select_or_other"]');
      var $otherLabel = $element.find(
        'label[for="' + $otherRadio.prop("id") + '"]'
      );
      var $otherText = $element.find('input[type="text"]');

      // Get hidden other checkbox label and use it as placeholder in the other
      // amount text field.
      if (settings.setPlaceholder) {
        $otherText.prop("placeholder", $otherLabel.text());
      }

      // Add class to other text field when other checkbox is checked.
      $element.bind('select-or-other-update', function (event, data) {
        $otherText.parent().toggleClass('select-or-other-checked', data.otherSelected);
      });
    });

    // return self for chaining
    return this;
  };
})(jQuery);
