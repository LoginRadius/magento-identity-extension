require(['jquery', 'jquery/ui'], function ($) {
    $(document).ready(function () {
        resetpassbuttonhide();        
    });
    
    function resetpassbuttonhide() {   
     $('#resetPassword').hide();
    }
});