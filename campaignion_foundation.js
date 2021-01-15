/**
 * @file Drupal behaviors for the Campaignion Foundation Theme.
 */

(function ($) {

// Theme specific JS.
Drupal.behaviors.campaignion_foundation = {};
Drupal.behaviors.campaignion_foundation.attach = function (context, settings) {
  // Fix for file upload with AJAX enabled.
  // See https://www.drupal.org/project/drupal/issues/1513200
  if (Drupal.file) {
    $('input.form-submit', context).unbind('mousedown', Drupal.file.disableFields);
  }
};

// Initialize webform ajax slide.
Drupal.behaviors.webformAjaxSlide = {};
Drupal.behaviors.webformAjaxSlide.attach = function (context, settings) {
    // Container id begins with webform-ajax-wrapper.
  $('.webform-client-form', context).webformAjaxSlide({
    loadingDummyMsg: Drupal.t('loading'),
    onSlideBegin: function (ajaxOptions) {},
    onSlideFinished: function (ajaxOptions) {},
    onLastSlideFinished: function (ajaxOptions) {}
  });
}

// Custom clientside validation.
Drupal.behaviors.campaignion_foundation_clientside_validation = {};
Drupal.behaviors.campaignion_foundation_clientside_validation.attach = function (context, settings) {

  // Helper function for finding the top/last wrapper of radio and checkbox fields
  // and the select2 element for selects using select2.
  function findWrapper ($element) {
    if ($element.is('select') && $element.siblings('.select2-container').length) {
      return $element.siblings('.select2-container');
    }
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
      $(error).addClass('form-error is-visible').insertAfter(findWrapper($(element)));
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

// Payment methods sliding in/out.
Drupal.behaviors.payment_slide = {};
Drupal.behaviors.payment_slide.attach = function (context, settings) {
  /*
   * $selector slides out on click and $forms slide in.
   * $forms slide out on click on the back-link and $selector slides in.
   * $wrapper.height() is animated accordingly.
   */
  // Only act on payment webform component wrappers.
  var behavior = this;

  $('.paymethod-select-wrapper', context)
    .css({position: 'relative'})
    .each(function () {
    // Initial state: selector visible forms invisible.
    var $wrapper = $(this);
    var $selectorWrapper = $wrapper.find('.paymethod-select-radios');
    var $selector = $wrapper.children('.form-type-radios');
    behavior.$selector = $selector;
    if ($selector.length <= 0) {
      return;
    }

    var $forms = $('.payment-method-all-forms', $wrapper);

    if (behavior.showForms) {
      $selector.css({left: '-120%', position: 'relative', top: 0, margin: 0}).hide();
      $forms.css({position: 'relative', right:'0%', top:0, margin: 0});
    }
    else {
      $selector.css({position: 'relative', top: 0, left: '0%', margin: 0});
      $forms.css({position: 'absolute', right: '0%', top: 0, margin: 0}).hide();
    }

    var $submit_buttons = $wrapper.parents('form').find('.button.primary').appendTo($forms);

    $selectorWrapper.find('label').click(function () {
      behavior.showForms = true;
      // Slide in forms and select out.
      $wrapper.height($selector.height());
      $selector.css({position: 'absolute', top: 0, left: '0%'})
      .animate({left: '-120%'}, 500, 'swing', function () {
        $selector.hide().css('position', 'relative');
      });

      $forms.show();
      $forms.css({position: 'absolute', width: '100%', right: '-120%'})
      .animate({right: '0%'}, 500, 'swing', function () {
          $forms.css('position', 'relative');
      });

      $wrapper.animate({height: $forms.height()}, 500, 'swing', function () {
        $wrapper.css({'height': 'auto', 'overflow': 'visible'});
      });
    });

    $('<div class="payment-slide-back"><a href="#">' + Drupal.t('back') + '</a></div>')
    .prependTo($forms)
    .click(function (e) {
      behavior.showForms = false;
      // Slide out forms and selector in.
      $selector.css({position: 'relative', width: '100%'});
      $selector.show().animate({left: '0%'}, 500, 'swing', function () {
        $selector.css('position', 'relative');
      });

      $wrapper.height($forms.height());
      $forms.css('position', 'absolute');
      $forms.animate({right: '-120%'}, 500, 'swing', function () {
        $forms.hide().css('position', 'relative');
      });

      $wrapper.animate({height: $selector.height()}, 500, 'swing', function () {
        $wrapper.css('height', 'auto');
      });
      // Do not bubble.
      e.stopPropagation();

      // Return false to prevent a page reload.
      return false;
    });
  });
};

/**
 * Provide an event to act on form step changes.
 *
 * If no form step is yet known, assume we are initially loading a page with a
 * webform.
 *
 * You can use this event to conditionally show/hide parts of a page.
 *
 * NB: We currently need to use native events to allow dispatching between
 * different versions of jQuery (`.trigger()` and `.on()` communication
 * between the Drupal's `$` and the the mo-foundation-base's `$` does not
 * work).
 */
Drupal.behaviors.form_step_data = {};
Drupal.behaviors.form_step_data.attach = function (context, settings) {
  // Return early if we are on a node without webform.
  if (!(settings.campaignion_foundation && settings.campaignion_foundation.webform)) {
    return
  }
  // Current step is always a number > 0 if the settings information is correct.
  var currentStep = settings.campaignion_foundation.webform.current_step || 1;

  currentStep = parseInt(currentStep, 10);
  var previousStep = $("#page").attr("data-form-step") || 0;
  previousStep = parseInt(previousStep, 10);

  if (currentStep !== previousStep) {
    var event = new CustomEvent("changeFormStep", {
      detail: { current: currentStep, previous: previousStep },
    });
    document.dispatchEvent(event);
    // update known step
    $("#page").attr("data-form-step", currentStep);
  }
  else {
    var event = new CustomEvent("sameFormStep", {
      detail: { current: currentStep, previous: previousStep },
    });
    document.dispatchEvent(event);
  }
};

})(jQuery);
