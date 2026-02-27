//Scripts dos widgets de calendário dos sorteios
jQuery(function($) {
    var max = 3;

    $('.js-limit-list').each(function() {
        var $ul = $(this);
        var $lis = $ul.find('li');

        if ($lis.length > max) {
            $lis.slice(max).hide();

            if (!$ul.next().hasClass('toggle-list')) {
                var $btn = $('<button class="toggle-list"><span class="ver-mais">+ Ver mais</span><span class="ver-menos hidden">- Ver menos</span></button>');
                $ul.after($btn);

                $btn.on('click', function() {
                    var $hidden = $lis.slice(max);

                    if ($hidden.is(':visible')) {
                        $hidden.slideUp(300);
                        $btn.find('.ver-mais').removeClass('hidden');
                        $btn.find('.ver-menos').addClass('hidden');
                    } else {
                        $hidden.slideDown(300);
                        $btn.find('.ver-mais').addClass('hidden');
                        $btn.find('.ver-menos').removeClass('hidden');
                    }
                });
            }
        }
    });
});