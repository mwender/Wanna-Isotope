(function($) {
    var $container = $('#' + isovars.id);
    $container.imagesLoaded( function(){
        $container.isotope({
          itemSelector: ".isotope-item",
          layoutMode: isovars.layoutMode
        });
    });

    var filters = {};
    var $optionSets = $('#filters-' + isovars.id),
    $optionLinks = $optionSets.find('a');

    $('.filters-list').on( 'click', '.filter-button', function(){
        var $this = $(this);

        // get group key
        var $buttonGroup = $this.parents('.filter-group');
        var filterGroup = $buttonGroup.attr('data-filter-group');
        // set filter for group
        if( $this.hasClass('active') ){
            filters[ filterGroup ] = '';
        } else {
            filters[ filterGroup ] = $this.attr('data-filter');
        }
        // combine filters
        var filterValue = concatValues( filters );
        $container.isotope({filter: filterValue});
        // set .active
        $('a.filter-button').removeClass('active');
        for( var filterGroup in filters ){
            $('a.filter-button[data-filter="' + filters[filterGroup] + '"]').addClass('active');
        }
    });

    $('.filters-list').on( 'click', '.clear-filters a.button', function(){
        filters = {};
        $('a.filter-button').removeClass('active');
        $container.isotope({filter: ''});
    });

    function concatValues( obj ){
        var value = '';
        for( var prop in obj ){
            value += obj[prop];
        }
        return value;
    }
})(jQuery);