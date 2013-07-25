Date.prototype.getDateFormatted = function() {
    var d = this.getDate();
    return d < 10 ? '0' + (d) : (d); // 
}
Date.prototype.getMonthFormatted = function() {
    var month = this.getMonth();
    return month < 9 ? '0' + (month+1) : (month+1); // ('' + month) for string result
}
Date.prototype.type = null;
Date.prototype.setType = function(type) {
    this.type = type;
}
Date.prototype.getType = function() {
    return this.type;
}
Date.prototype.id = null;
Date.prototype.setId = function(id) {
    this.id = id;
}
Date.prototype.getId = function() {
    return this.id;
}
Date.prototype.daysMoreLess = 
    Date.prototype.daysMoreLess ||
    function(days){
      days = days || 0;
      var ystrdy = new Date(this.setDate(this.getDate()+days));
      this.setDate(this.getDate() + -days);
      return ystrdy;
};

function highestPriorityDateClass(date, mappings, defaultDateClass){
    var dateClass = defaultDateClass;

    var i;
    for (i=0; i<mappings.length; i++){
        var mapping = mappings[i];
        if (date.hasOwnProperty(mapping.original_state)){
            dateClass = mapping.mapped_state;
            break;
        }
    }

    return dateClass;
}

function getDateClass(date, mappings, defaultDateClass){
    if (date){
        return highestPriorityDateClass(date, mappings, defaultDateClass);
    } else {
        return defaultDateClass;
    }
}

function dateExists(dates, year, month, day){
    return (typeof dates[year]!='undefined' && typeof dates[year][month]!='undefined' && typeof dates[year][month][day]!='undefined');
}

function getDates(dates, date){
    var returnDates = null;
    if (dateExists(dates, date.getFullYear(), date.getMonthFormatted(), date.getDateFormatted())){
        returnDates = dates[date.getFullYear()][date.getMonthFormatted()][date.getDateFormatted()];
    }
    return returnDates;
}