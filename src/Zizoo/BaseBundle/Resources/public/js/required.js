$(document).ready(function() {

/* ==========================================================================
   General
   ========================================================================== */

    /**
     * Clear form elements on focus
     */

    $.fn.clear = function() {
        return this.each(function() {
            var default_text = $(this).val();
            $(this).focus(function() {
                if ($(this).val() == default_text) $(this).val('');
            });
            $(this).blur(function() {
                if ($(this).val() == '') $(this).val(default_text);
            });
        });
    };

    // Trigger clear function
    $('.clear').clear();


    /**
     * sfRotator
     */

    $('#slideshow').sfRotator();


    /*
     * Checkbox, radio
     */

    //$('input[type="radio"], input[type="checkbox"]').checkBox();


/* ==========================================================================
   Inspire Me
   ========================================================================== */

    /**
     * Function
     */

    $('#inspire-me article').hover(function() {
        $(this).find('.content').stop(true,true).fadeToggle(200, 'easeInQuad');
        $(this).find('.details').stop(true,true).fadeToggle(200, 'easeInQuad');
    });


/* ==========================================================================
   sfModal
   ========================================================================== */

    /**
     * sfModal
     */

    //$('.trigger').sfModal();


    /**
     * Register expand
     */

    $('#register .table').click(function() {
        $(this).parent().next('.expand').slideToggle(200, 'easeInQuad', function() {
            $(this).find('input[type="text"]:first').focus();
        });
        return false;
    });


/* ==========================================================================
   Overview media
   ========================================================================== */

    /**
     * Function
     */

    $('.media .thumbs ul li a').click(function() {
        $('.media .full img').attr('src', $(this).attr('href'));
        return false;
    });


    /**
     * Media thumbs carousel
     */

    $('.media .thumbs ul').carouFredSel({
        circular: false,
        infinite: false,
        width: 'variable',
        height: 50,
        items: {
            visible: 'variable',
            width: 80
        },
        scroll: {
            items: 1,
            easing: 'easeInQuad'
        },
        auto: false,
        next: {
            button: '.media .next',
            key: 'right'
        },
        prev: {
            button: '.media .prev',
            key: 'left'
        }
    });
    
    
    
 /* ==========================================================================
   jQuery responsive dialog
   ========================================================================== */
    
    // on window resize run function
    $(window).resize(function () {
        fluidDialog();
    });

    // catch dialog if opened within a viewport smaller than the dialog width
    $(document).on("dialogopen", ".ui-dialog", function (event, ui) {
        fluidDialog();
    });

    function fluidDialog() {
        var $visible = $(".ui-dialog:visible");
        // each open dialog
        $visible.each(function () {
            var $this = $(this);
            var dialog = $this.find(".ui-dialog-content").data("dialog");
            // if fluid option == true
            if (dialog.options.fluid) {
                var wWidth = $(window).width();
                // check window width against dialog width
                if (wWidth < dialog.options.maxWidth + 50) {
                    // keep dialog from filling entire screen
                    $this.css("max-width", "90%");
                } else {
                    // fix maxWidth bug
                    $this.css("max-width", dialog.options.maxWidth);
                }
                //reposition dialog
                dialog.option("position", dialog.options.position);
            }
        });

    }
    

});


