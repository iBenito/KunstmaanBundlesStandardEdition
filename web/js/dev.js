$(document).ready(function(){
    
    $(document).delegate('button[type="submit"], input[type="submit"]', 'click', function(){
        var validate = confirm('Perform client-site form validation?'); 
        if (validate==false){
             $(this).parents('form').attr('novalidate', 'novalidate');

        } else {
             $(this).parents('form').attr('novalidate', null);
        }
        return true;
    });
    
    
});