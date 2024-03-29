/**
 * @file Drupal behaviors for the Campaignion Foundation Theme.
 */

(function ($) {

// Theme specific JS.
Drupal.behaviors.campaignion_foundation = {};
Drupal.behaviors.campaignion_foundation.attach = function (context, settings) {
  // Upload managed files on forms immediately when selected.
  if (settings.file && settings.file.elements) {
    $.each(settings.file.elements, function(selector) {
      $(selector, context).on('change', function() {
        var $input = $(this);
        // Wait for validation to finish.
        setTimeout(function () {
          var $error = $('.file-upload-js-error');
          if ($error.length) {
            $error.addClass('callout alert');
          }
          else if ($input.val()) {
            // Click (hidden) upload button.
            // File events are bound to "mousedown", not "click".
            // Make sure the button stays hidden.
            $input.siblings('input.upload').mousedown().hide();
            // Disable the visible button.
            $input.siblings('label.button').attr('disabled', 'disabled');
          }
        }, 100);
      });
    });
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
Drupal.behaviors.form_steps = {};
Drupal.behaviors.form_steps.attach = function (context, settings) {
  // Return early if we are on a node without webform.
  if (!(settings.campaignion_foundation && settings.campaignion_foundation.webform)) {
    return
  }

  // Current step is always a number > 0 if the settings information is correct.
  var currentStep = settings.campaignion_foundation.webform.current_step || 1;
  currentStep = parseInt(currentStep, 10);
  var previousStep = $("#page").attr("data-form-step") || 0;
  previousStep = parseInt(previousStep, 10);

  // Set current step on #page as data attribute.
  // Usable also in CSS.
  $("#page").attr("data-form-step", currentStep);

  // Determine which event to dispatch by looking at the context.
  if (context === document) {
    var event = new CustomEvent("initialFormStep", {
      detail: {
        form: settings.campaignion_foundation.webform,
        current: currentStep,
        previous: previousStep
      }
    });
    document.dispatchEvent(event);
  }
  else if ($(context).is('[id^=webform-ajax-wrapper]')) {
    var event = new CustomEvent("changeFormStep", {
      detail: {
        form: settings.campaignion_foundation.webform,
        current: currentStep,
        previous: previousStep
      },
    });
    document.dispatchEvent(event);

    // afterFirstFormStep dispatched only once
    $('#page').once('form-steps').each(function () {
      var event = new CustomEvent("afterFirstFormStep", {
        detail: {
          form: settings.campaignion_foundation.webform
        }
      });
      document.dispatchEvent(event);
    });
  }
};

})(jQuery);
