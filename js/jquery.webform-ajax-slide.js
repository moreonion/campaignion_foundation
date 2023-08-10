/*
 * To be called from Drupal behaviors.
 */

(function ( $ ) {

  $.fn.webformAjaxSlide = function( options ) {

    // Bail if we are not called on an AJAX enabled webform.
    var $ajaxWrapper = $(this).parents('[id^=webform-ajax-wrapper]').first();
    if (!$ajaxWrapper.length) {
      return this;
    }
    var ajax_id = $ajaxWrapper.attr('id');

    // These are the defaults.
    var defaults = {
      minHeight: '0',
      containerClass: 'webform-ajax-slide-wrapper',
      slideClass: 'webform-ajax-slide',
      loadingDummyClass: 'webform-ajax-slide-loading-dummy',
      loadingDummyMsg: 'loading',
      slideAcrossPage: false, // break out of the container
      slideSpeed: 0.9,        // px per millisecond
      onSlideFinished: function () {},
      onSlideBegin: function () {},
      onLastSlideFinished: function () {}
    }
    var settings = $.extend({}, defaults, options );

    var minHeight = 0;
    var maxPageNum = parseInt($('input[name="details\[page_count\]"]', this).attr('value'));
    var pageNum = parseInt($('input[name="details\[page_num\]"]', this).attr('value'));

    // Generate an wrapper around the ajax wrapper to
    // prevent elements visually sliding over the page.
    var $container = $(this).parents('.' + settings.containerClass).first();
    if (!$container.length) {
      $ajaxWrapper.wrap($('<div class="' + settings.containerClass + '">'));
      $container = $ajaxWrapper.parent();
    }
    $container.css({position: 'relative'});

    // Generate a wrapper around the slide if needed.
    var $slide = $ajaxWrapper.parents('.' + settings.slideClass).first();
    if (!$slide.length) {
      $ajaxWrapper.wrap($('<div class="' + settings.slideClass + '">'));
      $slide = $ajaxWrapper.parent();
    }

    // Generate a dummy div to display while loading.
    var $loadingdummy = $('.' + settings.loadingDummyClass, $container).first();
    if (!$loadingdummy.length) {
      $loadingdummy = $('<div class="' + settings.loadingDummyClass + '"><div class="container"><div>' + settings.loadingDummyMsg + '</div></div></div>')
        .hide().insertBefore($slide);
    }

    // Called via ajaxSend().
    var onSend = function(ajax, ajaxOptions) {
      // Set container anew (from loaded data).
      var pageNum = parseInt($('input[name="details\[page_num\]"]', $ajaxWrapper).attr('value'));
      ajax.onLastSlide = (pageNum == maxPageNum);

      settings.onSlideBegin.call($ajaxWrapper, ajaxOptions);

      // After possible tampering of context + options by callback.
      var stepForward = ajaxOptions["ajaxSlidingDirection"] == "back" ? false : true;
      var context = ajaxOptions.data;

      // Set dummy dimensions to current container dimensions.
      $loadingdummy.css({height: $slide.height() + 'px', width: $slide.width() + 'px'});

      // Fetch payment status messages if they exist.
      $loadingdummy.find('.payment-success').remove();
      ajax.form.find('.payment-success').each(function () {
        $('<div class="payment-success form-label">').html($(this).html()).appendTo($loadingdummy);
      });

      // Define the animation and it's "reverse".
      var anim = {},
        reverseAnim = {},
        distance = 0;
      if (stepForward) {
        distance = settings.slideAcrossPage ? $(window).width() - $container.offset().left : $slide.outerWidth() * -1.2;
        $loadingdummy.css({position: 'absolute', right: distance + 'px', left: ''});
        reverseAnim = {right: '0px'};
        $slide.css({position: 'relative', right: '', left: '0px'}); // one element needs position: relative
        anim = {left: distance + 'px'};
      } else {
        distance = settings.slideAcrossPage ? $container.offset().left + $slide.outerWidth() : $slide.outerWidth() * -1.2;
        $loadingdummy.css({position: 'absolute', right: '', left: distance + 'px'});
        reverseAnim = {left: '0px'};
        $slide.css({position: 'relative', right: '0px', left: ''});
        anim = {right: distance + 'px'};
      }

      // Do the slide!
      // Set container overflow to hidden to prevent overlapping.
      if (!settings.slideAcrossPage) {
        $container.css({overflow: 'hidden'});
      }
      // Move dummy in.
      $loadingdummy.show().animate(reverseAnim, Math.abs(distance / settings.slideSpeed));
      // Move container out.
      $slide.animate(anim, Math.abs(distance / settings.slideSpeed), function() {});
    };

    var onSuccess = function(ajax, response) {
      // Don't slide-in if a redirect is in progress.
      for (var i=0; i<response.length; i++) {
        if (response[i].command == 'redirect') {
          return;
        }
      }

      $slide.queue(function() {
        $slide.css({left: '', right: '', position: 'absolute', opacity: 0});
        $loadingdummy.css('position', 'relative');
        if (!settings.slideAcrossPage) {
          $container.css({overflow: 'visible'});
        }
        // To the incoming slide.
        $loadingdummy.animate({height: $slide.height()}, 200, 'swing', function() {
          $slide.css('position', 'relative');
          $loadingdummy.css('position', 'absolute').fadeOut(400);
          $slide.animate({opacity: 1}, 400, function() {
            $loadingdummy.hide();
          }).dequeue();
        });
      });

      if (ajax.onLastSlide) {
        // Call finishing callback.
        settings.onLastSlideFinished.call($ajaxWrapper, response);
      } else {
        settings.onSlideFinished.call($ajaxWrapper, response);
      }
    }

    // Drupal.ajax is keyed by button ids. We have to loop to find ours.
    $.each(Drupal.ajax, function() {
      if (this.wrapper.substr(1) == ajax_id) {
        var ajax = this;
        var old_beforeSend = this.beforeSend;
        var old_success = this.success;

        this.beforeSend = function(xhr, ajaxOptions) {
          if (ajaxOptions.data) {
            onSend(ajax, ajaxOptions);
          }
          old_beforeSend.call(ajax, xhr, ajaxOptions);
        };

        this.success = function(response, status) {
          old_success.call(ajax, response, status);
          onSuccess(ajax, response);
        };

        // Set a beforeSubmit callback to get the
        // direction from a data attribute from the markup.
        ajax['beforeSubmit'] = function(form_values, element_settings, options) {
          options['ajaxSlidingDirection'] = $(this.element).data('direction');
        }
      }
    });

    return this;
  }

})(jQuery);
