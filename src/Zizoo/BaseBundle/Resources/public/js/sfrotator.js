/*
 * jQuery sfRotator v1.2
 *
 * Sasa Foric (http://www.sasaforic.com)
 *
 */

(function($) {
    $.fn.extend({

        // sfParallax
        sfRotator : function(options) {
            var defaults = {
                duration    : 4000,
                speed        : 600
            };

            // Rotators
            var options = $.extend(defaults, options);
            return this.each(function() {

                // Variables
                var option            = options,
                    rotator            = $(this),
                    rotators        = rotator.find('.rotators'),
                    button            = rotator.find('.arrow'),
                    links            = $('.links[data-rotator="' + rotator.attr('id') + '"]');

                // Start rotator if more than one children and if rotator is visible
                if (rotators.children().length > 1 && rotator.is(':visible')) {

                    rotators.children().hide();
                    rotators.children('li:first-child').show();
                    rotators.children('li:first-child').addClass('current');

                    links.children('li:first-child').addClass('current');

                    var timeOut;
                    function rotate() {

                        var currentLink     = links.children('.current'),
                            currentRotate    = rotators.children('.current'),

                            nextLink        = currentLink.next(),
                            nextRotate        = currentRotate.next();

                        timeOut  = setTimeout(function() {
                            if (nextRotate.length === 0) {
                                nextLink    = links.children('li:first-child');
                                nextRotate    = rotators.children('li:first-child');
                            }

                            currentLink.removeClass('current');
                            currentRotate.removeClass('current');
                            currentRotate.fadeOut(option.speed);

                            nextLink.addClass('current');
                            nextRotate.addClass('current');
                            nextRotate.fadeIn(option.speed);

                            rotate();
                        }, option.duration);
                    }

                    // Previous and next functions
                    button.click(function() {

                        var currentLink     = links.children('.current'),
                            currentRotate    = rotators.children('.current'),

                            prevLink        = currentLink.prev(),
                            prevRotate        = currentRotate.prev(),

                            nextLink        = currentLink.next(),
                            nextRotate        = currentRotate.next();

                        if (nextRotate.length === 0) {
                            nextLink    = links.children('li:first-child');
                            nextRotate    = rotators.children('li:first-child');
                        }

                        if (prevRotate.length === 0) {
                            prevLink    = links.children('li:last-child');
                            prevRotate    = rotators.children('li:last-child');
                        }

                        if ($(this).is('.prev')) {
                            currentLink.removeClass('current');
                            currentRotate.removeClass('current');
                            currentRotate.stop(true,true).fadeOut(option.speed);

                            prevLink.addClass('current');
                            prevRotate.addClass('current');
                            prevRotate.fadeIn(option.speed);
                        }

                        if ($(this).is('.next')) {
                            currentLink.removeClass('current');
                            currentRotate.removeClass('current');
                            currentRotate.stop(true,true).fadeOut(option.speed);

                            nextLink.addClass('current');
                            nextRotate.addClass('current');
                            nextRotate.fadeIn(option.speed);
                        }

                        return false;
                    });

                    // Pause on mouse over on button
                    button.mouseenter(function() {
                        clearTimeout(timeOut);
                    }).mouseleave(function() {
                        rotate();
                    });

                    // Links functions to rotator
                    links.find('a').click(function() {
                        rotators.children().eq($(this).parent().index()).fadeIn(option.speed).addClass('current')
                            .siblings().removeClass('current').stop(true,true).fadeOut(option.speed);
                            $(this).parent().addClass('current').siblings().removeClass('current');

                            clearTimeout(timeOut);
                            timeOut = setTimeout(function() {
                                rotate();
                            }, option.duration);
                        return false;
                    });

                    rotate();
                }
            });

        }

    });

})(jQuery);