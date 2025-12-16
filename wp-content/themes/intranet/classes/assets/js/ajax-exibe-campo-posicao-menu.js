jQuery(document).ready(function ($) {

    $('#meta_box_select_menus').change(function () {

        var meta_box_select_menus = $(this).val();
        var meta_box_select_menus_posicao = $('#meta_box_select_menus_posicao').val();
        var conteudo_a_ser_exibido = $('#exibir_campo_menu_posicao');
        var conteudo_a_ser_escondido = $('#esconder_campo_menu_posicao');

        conteudo_a_ser_escondido.css('display', 'none');

        jQuery.ajax({
            url: bloginfo.ajaxurl,
            type: 'post',
            data: {
                // você sempre deve passar o parâmetro 'action' com o nome da função que você criou no seu functions.php ou outro que você esteja incluindo nele
                action: 'exibirCampoMenuPosicao',
                ajaxMetaBoxSelectMenus: meta_box_select_menus,
                ajaxMetaBoxSelectMenusPosicao: meta_box_select_menus_posicao,
            },

            success: function (data) {
                // transforma a data em objeto
                var $data = $(data);
                // This outputs the result of the ajax request
                conteudo_a_ser_exibido.html($data);
            },
        });


    });
});
