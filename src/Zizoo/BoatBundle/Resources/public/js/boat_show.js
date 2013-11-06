function setupCalendar(){

    $.extend($.datepick, {
        zizoo_availability: function(date) {
            var selectable = false;
            var previousDate            = date.daysMoreLess(-1);
            var reservedDate            = getDates(availabilityReservedDates, date);
            var previousReservedDate    = getDates(availabilityReservedDates, previousDate);
            var priceDate               = getDates(availabilityPriceDates, date);
            var previousPriceDate       = getDates(availabilityPriceDates, previousDate);

            var overlay = '<div class="day_overlay"></div>';
            var content = priceDate ? overlay + date.getDateFormatted() + '<span class="day_price">'+priceDate[0]+'</span>' : overlay + date.getDateFormatted() + '<span class="day_price">'+(availabilityDefaultAllowed?availabilityDefaultPrice+'*':'')+'</span>';
            
            var todayDefaultDateClass    = determineDefaultDateClass(defaultDateClass, priceDate);
            var previousDefaultDateClass = determineDefaultDateClass(defaultDateClass, previousPriceDate);

            var todayClass  = getDateClass(reservedDate, mappings, todayDefaultDateClass);
            var lastClass   = getDateClass(previousReservedDate, mappings, previousDefaultDateClass);

            var lastClassArr = lastClass.split('-');
            lastClass = lastClassArr[lastClassArr.length-1];
            if (lastClass != todayClass){
                todayClass = lastClass + '-' + todayClass;
            }

            return {selectable: selectable, dateClass: todayClass, title: '', content: content};
        }
    });

    $('#calendar').datepick({
        rangeSelect: false,
        changeMonth: false,
        monthsToShow: [1,1],
        onDate: $.datepick.zizoo_availability,
        renderer: $.datepick.themeRollerRenderer
    });
    
}

function openMessageToOwnerDialog(){
    $('#message_to_owner').dialog({
        modal: true,
        closeOnEscape: false,
        resizable: false,
        width: 500,
        maxWidth: 500,
        open: function( event, ui ) {
            jQuery('#zizoo_message_owner_message').focus();
            
            setupMessageOwner();
            
        },
        fluid: true,
        title: 'Send Message to Owner'
    });
}

$(document).ready(function(){
    setupCalendar();
    
    $('#send_message_btn').click(function(){
        openMessageToOwnerDialog();
        return false;
    });
    
});