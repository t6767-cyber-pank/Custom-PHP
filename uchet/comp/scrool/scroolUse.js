(function($, window) {
        var adjustAnchor = function() {

            var $anchor = $(':target');
            fixedElementHeight = 180;

            if ($anchor.length > 0) {
                $('html, body')
                    .stop()
                    .animate({
                        scrollTop: $anchor.offset().top - fixedElementHeight
                    }, 10);


            }
        };

        $(window).on('hashchange load', function() {
            adjustAnchor();
        });

    })(jQuery, window);