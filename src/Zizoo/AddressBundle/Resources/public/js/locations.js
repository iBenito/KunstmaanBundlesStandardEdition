var map = null;
var markers = null;
var mc = null;
var geocoder = null;
var center = new google.maps.LatLng(38, 15);
var zoom = 2;

function codeAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}

/**
* Initializes the map and listeners.
*/
function initialize() {
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(document.getElementById('map'), {
        center: center,
        zoom: zoom,
        mapTypeId: 'terrain'
    });
    markers = new Array();
    mc = new MarkerClusterer(map, markers, {maxZoom: 19});
}

$(document).ready(function(){
    $('#map').show();
    if(typeof google === 'undefined' || google==null){
        $('#map').hide();
        return;
    }
    initialize();

    //$('#unique_locations, #reservation_from, #reservation_to').attr('autocomplete', 'true');

    $('#unique_locations').chosen();
    
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

    $( "#reservation_from" ).datepicker({
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
    $( "#reservation_to" ).datepicker({
        defaultDate: "+1d",
        changeMonth: true,
        numberOfMonths: 1,
        showAnim: 'slideDown',
        dateFormat: 'dd/mm/yy',
        showButtonPanel: true,
        beforeShow: clearBtnFnc1,
        onChangeMonthYear: clearBtnFnc2,
        onClose: function( selectedDate ) {
            $( "#reservation_from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });


    $('#search_form').on('submit', function(){
        updateSearch();
        return false;
    });

});