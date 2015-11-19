(function($) {
	// jquery goodness

  $('.pu06-dues').on('click', function(){
    var $quick_links = $('.quick-links-container');

    toggleModal($quick_links, true);

    $quick_links.find('.close').on('click', function(){
      toggleModal($quick_links, false);
    });

    $quick_links.on('click', function(e){
      e.stopPropagation();
      if(e.eventPhase === 2){
        toggleModal($quick_links, false);
      }
    });
  });

  function toggleModal($container, is_open){
    if(!!is_open){
      $('html').addClass('modal-no-scroll');
      $container.addClass('open');
    } else {
      $('html').removeClass('modal-no-scroll');
      $container.removeClass('open');
    }
  }

})(jQuery);
