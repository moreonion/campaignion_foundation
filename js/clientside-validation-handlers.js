/**
 * @file Register event handlers for clientside_validation.
 */

(function ($) {

// Helper function for finding the element after which to insert the error message.
function findWrapper ($element) {
  // Find the input group if there is one.
  var $group = $element.parents('.input-group');
  if ($group.length) {
    return $group;
  }
  // Find the select2 element for selects using select2.
  if ($element.is('select') && $element.siblings('.select2-container').length) {
    return $element.siblings('.select2-container');
  }
  if (!($element.is(':radio') || $element.is(':checkbox'))) {
    return $element;
  }
  // Find the top/last wrapper of radio and checkbox fields.
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
$(document).on('clientsideValidationInitialized', function() {
  // Register custom error function with clientside validation.
  Drupal.myClientsideValidation['campaignion_foundation_errors'] = function (error, element) {
    $(error).addClass('form-error is-visible').attr('role', 'alert').insertAfter(findWrapper($(element)));
  }
});

// Add classes to form elements.
$(document).on('clientsideValidationInvalid', function (event, element) {
  if (element) {
    var $wrapper = findWrapper($(element));
    $(element).addClass('is-invalid-input');
    $wrapper.siblings('label').addClass('is-invalid-label');
    $wrapper.siblings('.form-error').addClass('is-visible');
  }
});
$(document).on('clientsideValidationValid', function (event, element) {
  if (element) {
    var $wrapper = findWrapper($(element));
    $(element).removeClass('is-invalid-input');
    $wrapper.siblings('label').removeClass('is-invalid-label');
    $wrapper.siblings('.form-error').removeClass('is-visible');
  }
});

})(jQuery);
