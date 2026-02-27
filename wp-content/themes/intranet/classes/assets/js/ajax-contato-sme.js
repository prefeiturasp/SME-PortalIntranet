jQuery(document).ready(function ($) {

    /*jQuery UI sortable*/
    $( ".sortable" ).sortable();
    $( ".sortable" ).disableSelection();

    $('#add_campos_contato').on('click', function (e) {
        var select_add_campo_contato = $('#select_add_campo_contato').val();
        add_campo_contato(select_add_campo_contato);
    });

    function add_campo_contato(select_add_campo_contato){

        var conteudo_a_ser_exibido_contato = $('#conteudo_a_ser_exibido_contato');

        jQuery.ajax({
            url: bloginfo.ajaxurl,
            type: 'post',
            data: {
                // você sempre deve passar o parâmetro 'action' com o nome da função que você criou no seu functions.php ou outro que você esteja incluindo nele
                action: 'criaCamposContato',
                select_add_campo_contato: select_add_campo_contato,
            },

            success: function (data) {
                var $data = $(data);
                conteudo_a_ser_exibido_contato.append($data);
                console.log(conteudo_a_ser_exibido_contato);
            },
        });
    }

    $('.excluir_campo_contato').on('click', function (e) {

        e.preventDefault();

        var x = confirm("Confirma a exclusão deste registro?");
        if (x) {

            // Seleciona o elemeto pai do botão clicado, no caso o paragrafo
            var father = $(this).parent().parent();

            //Remove o elemento pai com os filhos
            father.remove();

        }else{
            return false;
        }

    });



});