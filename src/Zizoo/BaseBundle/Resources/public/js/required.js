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
                if ($(this).val() == default_text) $(this).val("");
            });
            $(this).blur(function() {
                if ($(this).val() == "") $(this).val(default_text);
            });
        });
    };

    // Trigger clear function
    $(".clear").clear();


    /**
     * sfRotator
     */

    $("#slideshow").sfRotator();


    /*
     * Checkbox, radio
     */

    $("input[type='radio'], input[type='checkbox']").checkBox();


    /**
     * File input
     */

    $(".profile-pic-upload input[type='file']").filestyle({
        buttonText: "Upload Profile Pic"
    });

    $(".file-upload input[type='file']").filestyle({
        buttonText: "Browse"
    });

    /**
     * Selectbox
     */

    $("select").selectBoxIt();


    /**
     * Help
     */

    $(".help").click(function() {
        return false;
    });


/* ==========================================================================
   Results
   ========================================================================== */

   $("#view .view a").click(function() {
       $(this).parent().addClass("current").siblings().removeClass("current");
       $("#all .tabbed > div").eq($(this).parent().index()).show().siblings().hide();
       return false;
   });


/* ==========================================================================
   Boat icon description
   ========================================================================== */

   $(".box .boat .icon.minus").each(function() {
       $(this).append("<span class='tooltip'><strong>Bareboat</strong><br>No Crew Included</span>");
   });

   $(".box .boat .icon.plus").each(function() {
       $(this).append("<span class='tooltip'><strong>Crewed</strong><br>Crew Included</span>");
   });


/* ==========================================================================
   Datepicker / sample
   ========================================================================== */

    // Reset position of datepicker on resize
    var resizeTimer = null;
    $(window).resize(function() {

        // Hide datepicker
        var field = $(document.activeElement);
        field.datepicker('hide');

        // Show datepicker after 300ms after resize
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (field.is('.hasDatepicker')) {
                field.datepicker('hide').datepicker('show');
            }
        }, 300);
    });

    // Search start date
    $("#search .date-picker.start").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $("#search .date-picker.end").datepicker("option", "minDate", selectedDate);
        }
    });

    // Search end date
    $("#search .date-picker.end").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $("#search .date-picker.start").datepicker("option", "maxDate", selectedDate);
        }
    });

    // Summary start date
    $(".summary .date-picker.start").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $(".summary .date-picker.end").datepicker("option", "minDate", selectedDate);
        }
    });

    // Summary end date
    $(".summary .date-picker.end").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $(".summary .date-picker.start").datepicker("option", "maxDate", selectedDate);
        }
    });

    // Filter start date
    $(".filter .date-picker.start").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $(".filter .date-picker.end").datepicker("option", "minDate", selectedDate);
        }
    });

    // Filter end date
    $(".filter .date-picker.end").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $(".filter .date-picker.start").datepicker("option", "maxDate", selectedDate);
        }
    });

    // Filter end date
    $(".payment-options .date-picker").datepicker({
        dateFormat: "yy-mm-dd"
    });

    // Slideshow start date
    $("#slideshow .date-picker.start").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $("#slideshow .date-picker.end").datepicker("option", "minDate", selectedDate);
        }
    });

    // Slideshow end date
    $("#slideshow .date-picker.end").datepicker({
        dateFormat: "yy-mm-dd",
        onClose: function(selectedDate) {
            $("#slideshow .date-picker.start").datepicker("option", "maxDate", selectedDate);
        }
    });


/* ==========================================================================
   Dropdown
   ========================================================================== */

    /**
     * Dropdown
     */

    $(".dropdown").click(function(e) {
        $(this).children(".selectboxit").toggleClass("selectboxit-open");
        $(this).children("ul").toggle();
        e.stopPropagation();
    });

    // Hide dropdown on html click
    $("html").click(function() {
        $(".dropdown").children(".selectboxit").removeClass("selectboxit-open");
        $(".dropdown").children("ul").hide();
    });

    // Add hover class
    $(".dropdown").mouseenter(function() {
        $(this).children(".selectboxit").addClass("selectboxit-hover");
    }).mouseleave(function() {
        $(this).children(".selectboxit").removeClass("selectboxit-hover");
    });

    // Block adding hover class hovering options
    $(".dropdown .selectboxit-options").mouseenter(function(e) {
        e.stopPropagation();
    });


/* ==========================================================================
   Toggle
   ========================================================================== */

    /**
     * Toggle
     */

    $(".toggle").click(function() {

        // Check if toggle is disabled
        if ($(this).is(":not(.disable)")) {

            // Toggle class off
            $(this).toggleClass("off");

            // Check if off class is added
            if ($(this).hasClass("off")) {
                $(this).prev(".text").text($(this).prev(".text").text() == 'Hidden' ? 'Active' : 'Hidden');
            } else {
                $(this).prev(".text").text($(this).prev(".text").text() == 'Active' ? 'Hidden' : 'Active');
            }
        }
        return false;
    });


/* ==========================================================================
   Inspire Me
   ========================================================================== */

    /**
     * Function
     */

    $("#inspire-me article").hover(function() {
        $(this).find(".content").stop(true,true).fadeToggle(200, "easeInQuad");
        $(this).find(".details").stop(true,true).fadeToggle(200, "easeInQuad");
    });


/* ==========================================================================
   sfModal
   ========================================================================== */

    /**
     * sfModal
     */

    $(".trigger").sfModal();


    /**
     * Register expand
     */

    $("#register .table").click(function() {
        $(this).parent().next(".expand").slideToggle(200, "easeInQuad", function() {
            $(this).find("input[type='text']:first").focus();
        });
        return false;
    });


/* ==========================================================================
   Overview media
   ========================================================================== */

    /**
     * Function
     */

    $(".media .thumbs ul li a").click(function() {
        $(".media .full img").attr("src", $(this).attr("href"));
        return false;
    });


    /**
     * Media thumbs carousel
     */

    if ($(".media").length == 1) {
        $(".media .thumbs ul").carouFredSel({
            circular: false,
            infinite: false,
            width: "variable",
            height: 50,
            items: {
                visible: "variable",
                width: 80
            },
            scroll: {
                items: 1,
                easing: "easeInQuad"
            },
            auto: false,
            next: {
                button: ".media .next",
                key: "right"
            },
            prev: {
                button: ".media .prev",
                key: "left"
            }
        });
    };


/* ==========================================================================
   How it works tabs
   ========================================================================== */

    /**
     * Function
     */

    // Show first tab on load
    $("#how-it-works .tab:first-child").show();

    // Add class current for first subnavigation
    $(".how-it-works li:first-child").addClass("current");

    // Switch tabs with subnavigation
    $(".how-it-works li a").click(function() {
        $(this).parent().addClass("current").siblings().removeClass("current");
        $("#how-it-works .tab").eq($(this).parent().index()).show().siblings().hide();
        return false;
    });


/* ==========================================================================
   Overview tabs
   ========================================================================== */

    /**
     * Function
     */

    // Show first tab on load
    $(".tabbed .tab").hide();
    $(".tabbed .tab:first-child").show();

    // Add class current for first subnavigation
    $(".overview li:first-child").addClass("current");

    // Switch tabs with subnavigation
    $(".overview li a").click(function() {
        $(this).parent().addClass("current").siblings().removeClass("current");
        $(".tabbed .tab").eq($(this).parent().index()).show().siblings().hide();

        if ($(this).attr("title") == "Location") {
            load_ivory_google_map_api();
        }

        return false;
    });


/* ==========================================================================
   Change functions
   ========================================================================== */

    //  Add default change value to input fields
    $(".slider .input-wrap").each(function() {
        var changeText = $(this).closest(".box").find(".change-options li:first-child a span").text();
        $(this).append("<span>"+ changeText +"</span>");
    });

    //  Add default change value to hidden field
    $(".change .change-value").each(function() {
        var changeText = $(this).closest(".box").find(".change-options li:first-child a span").text();
        $(this).attr("value", changeText);
    });

    // Change function in slider
    $(".change > a").click(function(e) {
        $(this).parent().toggleClass("active");
        $(this).next(".change-options").toggle();
        e.stopPropagation();
        return false;
    });

    $("html").click(function() {
        $(".change").removeClass("active");
        $(".change-options").hide();
    });

    // Udate change values to hidden field and input visible values
    $(".change .change-options a").click(function() {
        $(this).closest(".box").find(".slider .input-wrap span").text($(this).find("span").text());
        $(this).closest(".change").find(".change-value").attr("value", $(this).find("span").text());
        $(this).closest(".change").removeClass("active");
        $(this).closest(".change-options").hide();
        return false;
    });


/* ==========================================================================
   Filters
   ========================================================================== */

    // More
    $("#filter .more").click(function() {
        $(this).prev(".expand").slideToggle(200);
        $(this).text($(this).text() == 'View More' ? 'Show Less' : 'View More');
        return false;
    });

    // Length slider
    $("#length-slider").slider({
        range: true,
        min: 0,
        max: 300,
        values: [0, 300],
        slide: function(event, ui) {
            $("#length-from").val(ui.values[ 0 ]);
            $("#length-to").val(ui.values[ 1 ]);
        }
    });
    $("#length-from").val($("#length-slider").slider("values", 0));
    $("#length-to").val($("#length-slider").slider("values", 1));


    // Price slider
    $("#price-slider").slider({
        range: true,
        min: 0,
        max: 100000,
        values: [0, 100000],
        slide: function(event, ui) {
            $("#price-from").val(ui.values[ 0 ]);
            $("#price-to").val(ui.values[ 1 ]);
        }
    });
    $("#price-from").val($("#price-slider").slider("values", 0));
    $("#price-to").val($("#price-slider").slider("values", 1));


    // Year slider
    $("#year-slider").slider({
        range: true,
        min: 1990,
        max: 2013,
        values: [1990, 2013],
        slide: function(event, ui) {
            $("#year-from").val(ui.values[ 0 ]);
            $("#year-to").val(ui.values[ 1 ]);
        }
    });
    $("#year-from").val($("#year-slider").slider("values", 0));
    $("#year-to").val($("#year-slider").slider("values", 1));


/* ==========================================================================
   Payment options
   ========================================================================== */

   /**
    * Function
    */

   $("#details .options select").change(function() {
       if ($(this).val() != "options") {
           $("#details .payment-options").show();
           $("#details .payment-options #" + $(this).val()).show().siblings().hide();
       } else {
           $("#details .payment-options").hide();
           $("#details .payment-options > section").hide();
       }
   });

});