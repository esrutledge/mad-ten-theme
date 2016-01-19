(function($) {
	// jquery goodness

  // $('window').on('resize', function(){
  //   console.log(window.innerHeight);
  // });

  $('.pu06-dues').on('click', function(e){
    e.preventDefault();
    e.stopPropagation();
    var $quick_links = $('.quick-links-modal');

    toggleModal($quick_links, true);

    $quick_links.find('.modal-toggle').on('click', function(){
      toggleModal($quick_links, false);
    });

    $quick_links.on('click', function(e){
      e.stopPropagation();
      if(e.eventPhase === 2){
        toggleModal($quick_links, false);
      }
    });
  });

  $('.mobile-title .modal-toggle').on('click', function(e){
    if($('html').hasClass('menu-open')){
      clearMenu();
    } else {
      $('html').addClass('menu-open modal-no-scroll');
      $('.main-menu .menu').css({
        "max-height": window.innerHeight,
        "min-height": window.innerHeight
      });
    }
  });

  // TODO: eliminate these once we have Modernizr
  var is_touch = !!(('ontouchstart' in window) || (window.DocumentTouch && document instanceof window.DocumentTouch));
  var click_event = is_touch ? 'touchend' : 'click';

  $('.main-menu .menu .menu-item a').on('click', function(e){
    e.stopPropagation();
    clearMenu();
    var $link = $(e.currentTarget),
        href = $link.attr("href"),
        target = $link.attr("target");

    if (target) {
      window.open(href, target);
    } else {
      window.location = href;
    }
  });

  // $('.mobile-title .modal-toggle').on('click', function(e){
  //   $('html').removeClass('menu-open modal-no-scroll');
  // });

  function clearMenu(){
    $('html').removeClass('menu-open modal-no-scroll');
    $('.main-menu .menu').css({
      "max-height": '',
      "min-height": ''
    });
  }

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
