function dateExists(dates, year, month, day){
    return (typeof dates[year]!='undefined' && typeof dates[year][month]!='undefined' && typeof dates[year][month][day]!='undefined');
}

function availabilityDefaultDate(day){
    if (availabilityDefaultAllowed){
        return {selectable: false, dateClass: 'day_available', title: '&euro; ' + availabilityDefaultPrice, content: day + '<span class="day_price">&euro;' + availabilityDefaultPrice + '</span>'};
    } else {
        return {selectable: false, dateClass: 'day_unavailable', title: 'Unavailable', content: day};
    }
}

function defaultDateClass(day){
    if (availabilityDefaultAllowed){
        return 'day_available';
    } else {
        return 'day_unavailable';
    }
}

function combinedDateClasses(dayStates, numStates){
    var arr = [], p, i = 0;
    for (dayStateId in dayStates){
        var dayState = dayStates[dayStateId];
        var suffix = dayState['suffix'];
        if (dayState!=null && suffix!=null && numStates==1){
            if (suffix=='start'){
                arr[i++] = (availabilityDefaultAllowed?'day_available':'day_unavailable') + '-' + dayState['reservation_state'];
            } else {
                arr[i++] = dayState['reservation_state'] + '-' + (availabilityDefaultAllowed?'day_available':'day_unavailable');
            }
        } else {
            arr[i++] = dayState['reservation_state'];
        }
    }
    return arr.join('-');
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
            var reservedDates    = null;
            var priceDate       = null;

            if (dateExists(availabilityReservedDates, date.getFullYear(), date.getMonthFormatted(), date.getDateFormatted())){
                reservedDates = availabilityReservedDates[date.getFullYear()][date.getMonthFormatted()][date.getDateFormatted()];
            }

            if (dateExists(availabilityPriceDates, date.getFullYear(), date.getMonthFormatted(), date.getDateFormatted())){
                priceDate = availabilityPriceDates[date.getFullYear()][date.getMonthFormatted()][date.getDateFormatted()];
            }

            if (priceDate){
                if (reservedDates){
                    var dateClasses = new Array();
                    var selectable = false;
                    var i, numStates=0, numRequested=0;
                    for (i=0; i<reservedDates.length; i++){
                        var reservedDate = reservedDates[i];
                        if (reservedDate[0]==STATUS_ACCEPTED || reservedDate[0]==STATUS_SELF || reservedDate[0]==STATUS_HOLD){
                            dateClasses['day_booked'] = {'reservation_state': 'day_booked', 'suffix': reservedDate[3]};
                            numStates++;
                        } else {
                            var defDayClass = defaultDateClass(date.getDateFormatted());
                            dateClasses[defDayClass] = {'reservation_state': defDayClass, 'suffix': null};
                            numStates++;
                        }
                    }
                    if (numRequested>0) numStates++;
                    var dateClass = combinedDateClasses(dateClasses, numStates);
                    return {selectable: selectable, dateClass: dateClass, title: '', content: date.getDateFormatted() + '<span class="day_price">'+priceDate[0]+'</span>'};
                } else {
                    return {selectable: false, dateClass: 'day_available', title: '&euro; ' + priceDate[0], content: date.getDateFormatted() + '<span class="day_price">&euro;' + priceDate[0] + '</span>'};
                }
            } else {
                if (reservedDates){
                    var dateClasses = new Array();
                    var selectable = false;
                    var i, numStates=0, numRequested=0;
                    for (i=0; i<reservedDates.length; i++){
                        var reservedDate = reservedDates[i];
                        if (reservedDate[0]==STATUS_ACCEPTED || reservedDate[0]==STATUS_SELF || reservedDate[0]==STATUS_HOLD){
                            dateClasses['day_booked'] = {'reservation_state': 'day_booked', 'suffix': reservedDate[3]};
                            numStates++;
                        } else {
                            var defDayClass = defaultDateClass(date.getDateFormatted());
                            dateClasses[defDayClass] = {'reservation_state': defDayClass, 'suffix': null};
                            numStates++;
                        }
                    }
                    if (numRequested>0) numStates++;
                    var dateClass = combinedDateClasses(dateClasses, numStates);
                    return {selectable: selectable, dateClass: dateClass, title: '', content: date.getDateFormatted() + '<span class="day_price">'+(availabilityDefaultAllowed?availabilityDefaultPrice+'*':'')+'</span>'};
                }
                return availabilityDefaultDate(date.getDateFormatted());    
            }
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