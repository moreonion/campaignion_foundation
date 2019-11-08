/**
 * @file Drupal behaviors for the Campaignion Foundation Theme.
 */

(function ($) {

Drupal.behaviors.campaignion_foundation = {};
Drupal.behaviors.campaignion_foundation.attach = function (context, settings) {
  // Fix for file upload with AJAX enabled.
  // See https://www.drupal.org/project/drupal/issues/1513200
  if (Drupal.file) {
    $('input.form-submit', context).unbind('mousedown', Drupal.file.disableFields);
  }
}

Drupal.behaviors.campaignion_foundation_clientside_validation = {};
Drupal.behaviors.campaignion_foundation_clientside_validation.attach = function (context, settings) {

  // Helper function for finding the top/last wrapper of radio and checkbox fields.
  function findWrapper ($element) {
    if (!($element.is(':radio') || $element.is(':checkbox'))) {
      return $element;
    }
    var $parents = $element.parents('.form-radios, .form-checkboxes');
    if (!$parents.length) {
      $parents = $element.parents('.form-type-radio, .form-type-checkbox').siblings().addBack().last();
    }
    if (!$parents.length) {
      $parents = $element;
    }
    return $parents;
  }

  // Add classes to error message and place it after the form field.
  $('form', context).on('clientsideValidationInitialized', function() {
    // Register custom error function with clientside validation.
    Drupal.myClientsideValidation['campaignion_foundation_errors'] = function (error, element) {
      $(error).addClass('form-error is-visible');
      if ($(element).is(':radio') || $(element).is(':checkbox')) {
        $(error).css('margin-top', 0);
      }
      $(error).insertAfter(findWrapper($(element)));
    }
  });

  // Add classes to form elements.
  $('form', context).on('clientsideValidationInvalid', function (event, element) {
    var $wrapper = findWrapper($(element));
    $(element).addClass('is-invalid-input');
    $wrapper.siblings('label').addClass('is-invalid-label');
    $wrapper.next('.form-error').addClass('is-visible');
  });
  $('form', context).on('clientsideValidationValid', function (event, element) {
    var $wrapper = findWrapper($(element));
    $(element).removeClass('is-invalid-input');
    $wrapper.siblings('label').removeClass('is-invalid-label');
    $wrapper.next('.form-error').removeClass('is-visible');
  });
};

})(jQuery);
