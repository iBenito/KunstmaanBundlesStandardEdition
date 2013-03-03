function tabbedMenu() {
        $( "#tab-links a" ).click(function(){
            var tabId = $(this).attr('href');
            $('#tabs .tab').removeClass('current');
            $('#tabs '+tabId).addClass('current');
            return false;
        });
}

$(document).ready(function(){
    tabbedMenu();
});