(function($) {
    var $container = $('#' + isovars.id);
    $container.imagesLoaded( function(){
        $container.isotope({
          itemSelector: ".isotope-item",
          layoutMode: isovars.layoutMode
        });
    });

    var $optionSets = $('#filters-' + isovars.id),
    $optionLinks = $optionSets.find('a');

    $optionLinks.click(function(){
        var $this = $(this);
        // don't proceed if already active
        if ( $this.hasClass('active') ) {
          return false;
        }
        var $optionSet = $this.parents('#filters-' + isovars.id);
        $optionSets.find('.active').removeClass('active');
        $this.addClass('active');

        //When an item is clicked, sort the items.
         var selector = $(this).attr('data-filter');
        $container.isotope({ filter: selector });

        return false;
    });
})(jQuery);