

$(document).ready(function(){
    
    $('#paging li a').click(function(el){
        $('#boats').block({ message: null });
        var page = $(this).parent().attr('class');

        $('#boats .page').hide().removeClass('current');
        $('#boats #page_'+page).show().addClass('current');
        $('#boats').unblock();
        return false;

        var url = $(this).attr('href');
        url = encodeURI(url);
        $('#boats').load(url, function(){
            $('#boats').unblock();
        });
        return false;
    });

    $('#zizoo_boat_search_location').chosen();
    
    var clearBtnFnc1 = function(input) {
            setTimeout(function() {
                var buttonPane = $( input )
                    .datepicker( "widget" )
                    .find( ".ui-datepicker-buttonpane" );
                var btn = $( '<button class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" type="button">Clear</button>');
                btn.unbind("click")  .bind("click", function () {  
                    $.datepicker._clearDate( input ); 
                });
                btn.appendTo(buttonPane);
            }, 1 );
        };
        
    var clearBtnFnc2 = function(yes, month, inst) {
            setTimeout(function() {
                var buttonPane = $( this )
                    .datepicker( "widget" )
                    .find( ".ui-datepicker-buttonpane" );
                var btn = $( '<button class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" type="button">Clear</button>');
                btn.unbind("click")  .bind("click", function () {  
                   $.datepicker._clearDate( inst.input ); 
                });
                btn.appendTo(buttonPane);
            }, 1 );
        };

    $( "#zizoo_boat_search_reservation_from" ).datepicker({
        defaultDate: "+1d",
        changeMonth: true,
        numberOfMonths: 1,
        showAnim: 'slideDown',
        dateFormat: 'dd/mm/yy',
        showButtonPanel: true,
        beforeShow: clearBtnFnc1,
        onChangeMonthYear: clearBtnFnc2,
        onClose: function( selectedDate ) {
            $( "#reservation_to" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#zizoo_boat_search_reservation_to" ).datepicker({
        defaultDate: "+1d",
        changeMonth: true,
        numberOfMonths: 1,
        showAnim: 'slideDown',
        dateFormat: 'dd/mm/yy',
        showButtonPanel: true,
        beforeShow: clearBtnFnc1,
        onChangeMonthYear: clearBtnFnc2,
        onClose: function( selectedDate ) {
            $( "#zizoo_boat_search_reservation_from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
    
   
    $('#search_form').on('submit', function(){
        updateSearch(1);
        return false;
    });

});