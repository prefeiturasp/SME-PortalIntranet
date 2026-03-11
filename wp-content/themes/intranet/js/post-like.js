jQuery(document).ready(function($) {


    jQuery('.pp_like').click(function(e){
        e.preventDefault();
        jQuery(this).hide();
        var current = jQuery(this);
        jQuery('.lds-facebook').show();
        var postid=jQuery(this).data('id');

        //alert(postid);
        var data = {
            action: 'post_like',
            security : MyAjax.security,
            postid: postid
        };

        jQuery.post(MyAjax.ajaxurl, data, function(res) {
            var result=jQuery.parseJSON( res );
            console.log(result);
            //alert(res);
            var likes="";

            if(result.likecount == 1){
                likes = result.likecount + " Like";
            } else {
                likes = result.likecount + " Likes";
            }            

            jQuery('span', current).text(likes);
            if(result.like == 1){
                jQuery(current).addClass('liked');
            }
            if(result.dislike == 1){
                jQuery(current).removeClass('liked');
            } 
            jQuery(current).show();
        });
    });    
    
});