jQuery(document).ready(function($) {

    if (acf.length){
        acf.addAction("new_field/key=field_618d77f0f3fe0", function ($field) {        
            
            $field.on("change", function (e) {
            
                var pauta = $field.$el.closest('.acf-fields').find('.pauta textarea');
                var participantes = $field.$el.closest('.acf-fields').find('.participantes textarea').attr("id");
                var endereco = $field.$el.closest('.acf-fields').find('.endereco select').attr("id");
                var end_manual = $field.$el.closest('.acf-fields').find('.end_manual');
                let tinyInstance = tinyMCE.editors[participantes];
                //console.log(endereco);

                
                var compromisso = $field.val();

                if(compromisso == 'outros'){

                    pauta.val('');
                    tinyInstance.setContent('');
                    $('#' + endereco).val('outros');
                    end_manual.removeClass('acf-hidden');

                } else {
                    var data = {
                        'action': 'my_action',
                        'compromisso': compromisso    
                    };
                        
                    $.ajax({
                        url: ajax_object.ajax_url,
                        type : 'post',
                        data: data,
                        dataType: 'json',
                        success: function( data ) {
                            
                            //console.log(data);

                            pauta.val(data.pauta_assunto);
                            $('#' + endereco).val(data.endereco_do_evento);
                            tinyInstance.setContent(data.participantes_evento);
                            end_manual.addClass('acf-hidden');
                        }
                    })
                }

            });
        });
    }
});