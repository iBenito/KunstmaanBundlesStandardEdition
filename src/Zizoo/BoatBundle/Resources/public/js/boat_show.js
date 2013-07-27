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

function openMessageComingSoonDialog(){
    $('#message_coming_soon').dialog({
        modal: true,
        closeOnEscape: false,
        resizable: false,
        width: 470,
        maxWidth: 470,
        fluid: true,
        title: 'Coming Soon'
    });
}

$(document).ready(function(){
    setupCalendar();
    
    $('#send_message_btn').click(function(){
        openMessageComingSoonDialog();
        return false;
    });
    
});