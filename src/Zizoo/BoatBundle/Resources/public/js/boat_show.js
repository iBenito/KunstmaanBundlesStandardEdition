function dateExists(dates, year, month, day){
    return (typeof dates[year]!='undefined' && typeof dates[year][month]!='undefined' && typeof dates[year][month][day]!='undefined');
}

function availabilityDefaultDate(day){
    if (availabilityDefaultAllowed){
        return {selectable: true, dateClass: 'day_available', title: '&euro; ' + availabilityDefaultPrice, content: day + '<span class="day_price">&euro;' + availabilityDefaultPrice + '</span>'};
    } else {
        return {selectable: false, dateClass: 'day_unavailable', title: 'Unavailable', content: day};
    }
}

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
            var reservedDate    = null;
            var priceDate       = null;

            if (dateExists(availabilityReservedDates, date.getFullYear(), date.getMonthFormatted(), date.getDateFormatted())){
                reservedDate = availabilityReservedDates[date.getFullYear()][date.getMonthFormatted()][date.getDateFormatted()];
            }

            if (dateExists(availabilityPriceDates, date.getFullYear(), date.getMonthFormatted(), date.getDateFormatted())){
                priceDate = availabilityPriceDates[date.getFullYear()][date.getMonthFormatted()][date.getDateFormatted()];
            }

            if (priceDate){
                if (reservedDate){
                    if (reservedDate[0]==STATUS_ACCEPTED || reservedDate[0]==STATUS_SELF || reservedDate[0]==STATUS_HOLD){
                        return {selectable: false, dateClass: 'day_unavailable', title: 'Unavailable', content: date.getDateFormatted()};
                    } else {
                        return availabilityDefaultDate(date.getDateFormatted());
                    }
                } else {
                    return {selectable: false, dateClass: 'day_available', title: '&euro; ' + priceDate[0], content: date.getDateFormatted() + '<span class="day_price">&euro;' + priceDate[0] + '</span>'};
                }
            } else {
                if (reservedDate){
                    if (reservedDate[0]==STATUS_ACCEPTED || reservedDate[0]==STATUS_SELF || reservedDate[0]==STATUS_HOLD){
                        return {selectable: false, dateClass: 'day_unavailable', title: 'Unavailable', content: date.getDateFormatted()};
                    } else {
                        return availabilityDefaultDate(date.getDateFormatted());
                    }
                }
                return availabilityDefaultDate(date.getDateFormatted());    
            }
        }
    });

    $('#calendar').datepick({ 
        rangeSelect: false,
        changeMonth: false,
        monthsToShow: [2,1],
        onDate: $.datepick.zizoo_availability,
        renderer: $.datepick.themeRollerRenderer
    });
    
}

$(document).ready(function(){
    setupCalendar();

    $( "#boat-tabs a" ).click(function(){
        var tabId = $(this).attr('href');
        $('.tabs .tab').removeClass('current');
        $('.tabs '+tabId).addClass('current');
        return false;
    });


});