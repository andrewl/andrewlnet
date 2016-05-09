(function($) {
  Drupal.behaviors.hophead = {
    attach: function(context, settings) {

      $('div.content img').each(function(i) {
	console.log(this);
        $(this).attr('href', $(this).attr('src'));
	$(this).colorbox();
      });

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
