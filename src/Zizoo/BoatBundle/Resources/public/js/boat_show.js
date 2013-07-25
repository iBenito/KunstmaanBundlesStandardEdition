function setupCalendar(){

    Date.prototype.getDateFormatted = function() {
        var d = this.getDate();
        return d < 10 ? '0' + (d) : (d); // 
    }
    Date.prototype.getMonthFormatted = function() {
        var month = this.getMonth();
        return month < 9 ? '0' + (month+1) : (month+1); // ('' + month) for string result
    }

    $.extend($.datepick, {
        zizoo_availability: function(date) {
            var selectable = false;
            var previousDate            = date.daysMoreLess(-1);
            var reservedDate            = getDates(availabilityReservedDates, date);
            var previousReservedDate    = getDates(availabilityReservedDates, previousDate);
            var priceDate               = getDates(availabilityPriceDates, date);

            var overlay = '<div class="day_overlay"></div>';
            var content = priceDate ? overlay + date.getDateFormatted() + '<span class="day_price">'+priceDate[0]+'</span>' : overlay + date.getDateFormatted() + '<span class="day_price">'+(availabilityDefaultAllowed?availabilityDefaultPrice+'*':'')+'</span>';

            var todayClass  = getDateClass(reservedDate, mappings, defaultDateClass);
            var lastClass   = getDateClass(previousReservedDate, mappings, defaultDateClass);

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

$(document).ready(function(){
    setupCalendar();
});