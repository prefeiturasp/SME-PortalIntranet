jQuery(document).ready(function ($) {    
    
    $( "#portais-submit" ).click(function() {       

        if(!$('.form-recados #busca').val()){
            $('.form-recados #busca').addClass("error");
            $('.login-error').css('display', 'block');
            event.preventDefault();
        } else{
            $('.form-recados #busca').removeClass("error");
            $('.login-error').hide();
        }
        
    });
});