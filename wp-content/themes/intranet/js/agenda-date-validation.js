jQuery(document).ready(function($) {
    // 1. Variáveis de estado
    var dateFieldHidden = 'input[name="acf[field_67d9b9ce73c67]"]';
    var dateFinalFieldHidden = 'input[name="acf[field_67d9b9ce73ca8]"]';

    var dialogOpen = false;
    var formSubmitted = false;
    var $submitButton = $('#publish, #save-post');
    
    // 2. Validação em tempo real
    $(document).on('change', dateFieldHidden, function() {
        validateDate($(this).val(), $(dateFinalFieldHidden).val());
        $('#start-date-feedback').remove();
        $('#end-date-feedback').remove();
    });

    $(document).on('change', dateFinalFieldHidden, function() {
        validateDate($(this).val(), $(dateFieldHidden).val());
        $('#start-date-feedback').remove();
        $('#end-date-feedback').remove();
    });
    
    $(document).on('change', '.acf-input-wrap input.input', function() {
        validateDate($(dateFieldHidden).val(), $(dateFinalFieldHidden).val());
        $('#start-date-feedback').remove();
        $('#end-date-feedback').remove();
    });

    $(document).on('change', '.acf-input-wrap input.input', function() {
        validateDate($(dateFinalFieldHidden).val(), $(dateFieldHidden).val());
        $('#start-date-feedback').remove();
        $('#end-date-feedback').remove();
    });

    function validateDate(startDate, endDate) {
        var postId = $('#post_ID').val() || 0;
        
        // Obter elementos
        var $startWrapper = $(dateFieldHidden).closest('.acf-input-wrap');
        var $endWrapper = $(dateFinalFieldHidden).closest('.acf-input-wrap');
        var $startFeedback = $('#start-date-feedback');
        var $endFeedback = $('#end-date-feedback');

        // Limpar estados anteriores
        $startWrapper.removeClass('has-conflict');
        $endWrapper.removeClass('has-conflict');
        $startFeedback.remove();
        $endFeedback.remove();

        // Só valida se a data inicial estiver preenchida
        if (!startDate) return;

        $.post(agenda_ajax.ajaxurl, {
            action: 'check_agenda_date',
            date: startDate,
            dateFinal: endDate,
            post_id: postId
        }, function(response) {
            if (response.success && response.data) {
                
                $('.date-feedback').remove();

                // Aplicar para o campo inicial
                $startWrapper.addClass('has-conflict');
                $startWrapper.after('<div id="start-date-feedback" class="date-feedback">' + agenda_ajax.duplicate_message + '</div>');
                
                // Aplicar para o campo final apenas se estiver preenchido
                if (endDate) {
                    $endWrapper.addClass('has-conflict');
                    $endWrapper.after('<div id="end-date-feedback" class="date-feedback">' + agenda_ajax.duplicate_message + '</div>');
                }
            }
        });
    }

    // 3. Configuração do diálogo
    var conflictDialog = $('<div/>', {
        id: 'date-conflict-dialog',
        title: 'Cadastro de Evento',
        html: '<p>Já existe um evento registrado para a data selecionada. No entanto, não há problema em cadastrar o seu evento para o mesmo dia.</p>'
    }).appendTo('body').dialog({
        autoOpen: false,
        modal: true,
        width: 600,
        dialogClass: 'wp-dialog alert-data-conflict',
        closeOnEscape: false,
        buttons: [{            
            text: "Fechar",
            class: "dialog-button dialog-cancel-button",
            click: function() {
                $(this).dialog("close");
                $submitButton.removeClass('disabled'); // Remove classe ao cancelar
                $(dateFieldHidden).focus();
            }
        }, {
            text: "Ciente",
            class: "dialog-button dialog-confirm-button",
            click: function() {
                formSubmitted = true;
                $(this).dialog("close");
                $submitButton.removeClass('disabled'); // Remove classe ao continuar
                $('#post').off('submit.agenda').submit();
            }
        }]
    });

    // 4. Interceptação do formulário com loading
    $('#post').on('submit.agenda', function(e) {
        if (formSubmitted) {
            formSubmitted = false;
            return true;
        }
        
        var dateValue = $(dateFieldHidden).val();
        if (!dateValue) return true;

        var dateFinalValue = $(dateFinalFieldHidden).val();
        
        // Previne envio e adiciona classe disabled
        e.preventDefault();
        $submitButton.addClass('disabled').text('Verificando...');
        
        // Verificação síncrona
        $.ajax({
            url: agenda_ajax.ajaxurl,
            type: 'POST',
            async: false,
            data: {
                action: 'check_agenda_date',
                date: dateValue,                
                dateFinal: dateFinalValue,
                post_id: $('#post_ID').val() || 0
            },
            success: function(response) {
                $submitButton.removeClass('disabled').text($submitButton.data('original-text') || 'Publicar');
                
                if (response.success && response.data && !dialogOpen) {
                    dialogOpen = true;
                    conflictDialog.dialog('open');
                    
                    conflictDialog.on('dialogclose', function() {
                        dialogOpen = false;
                    });
                } else {
                    formSubmitted = true;
                    $('#post').off('submit.agenda').submit();
                }
            },
            error: function() {
                $submitButton.removeClass('disabled').text($submitButton.data('original-text') || 'Publicar');
            }
        });
    });

    // Salva o texto original do botão
    $submitButton.each(function() {
        $(this).data('original-text', $(this).text());
    });

});