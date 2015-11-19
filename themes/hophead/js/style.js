(function($) {
  Drupal.behaviors.hophead = {
    attach: function(context, settings) {
      $('body.path-frontpage article').hover(
        function() {
          $(this).addClass('hover');
          $(this).removeClass('nohover');
        },
        function() {
          $(this).addClass('nohover');
          $(this).removeClass('hover');
        }
      )
    }
  };
})(jQuery);
