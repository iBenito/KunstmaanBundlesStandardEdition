function startCountDown(i, p, countDownObj, f) {
    // store parameters
    var pause = p;
    var fn = f;
    // make reference to div
    if (countDownObj == null) {
        // error
        alert("div not found, check your id");
        // bail
        return;
    }
    countDownObj.count = function(i) {
        // write out count
        countDownObj.innerHTML = i;
        if (i == 0) {
        // execute function
        fn();
        // stop
        return;
    }
    setTimeout(function() {
        // repeat
        countDownObj.count(i - 1);
        }, pause);
    }
    // set it going
    countDownObj.count(i);
}

$(document).ready(function(){
   
   $('.redirect_timer').each(function(index,el){
       startCountDown($(el).attr('time'), 1000, el, function(){
           window.location.href = $(el).attr('redirect');
       });
   });
   
});