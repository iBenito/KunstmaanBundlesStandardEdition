/*
 * jQuery sfModal v1.3 // customized for Zizoo
 *
 * Sasa Foric (http://www.sasaforic.com)
 *
 */

(function($){
    $.fn.extend ({

        sfModal: function() {

            return this.each(function() {

                // Modal and modal trigger
                var sfModalCurrent = $("#" + $(this).data("modal"));
                var sfModalTrigger = $(this);

                // Activate modal
                $(sfModalTrigger).click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Hide all open dialogs
                    $(".overlay").remove();
                    $(".modal").hide();

                    // Append modal after overlay
                    $("body").append("<div class='overlay'></div>");
                    sfModalCurrent.insertAfter(".overlay").show();
                });

                $("html").click(function() {
                    $(".overlay").remove();
                    sfModalCurrent.hide();
                });

                $(".modal .close").click(function() {
                    $(".overlay").remove();
                    sfModalCurrent.hide();
                    return false;
                });

                sfModalCurrent.click(function(e){
                    e.stopPropagation();
                });

            });
        }
    });
})(jQuery);