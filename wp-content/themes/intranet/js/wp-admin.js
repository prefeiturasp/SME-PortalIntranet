jQuery( document ).ready(function() {

    if(jQuery('#parent_id').length){
        jQuery("#parent_id").chosen({
            search_contains: true,
            no_results_text: "Nenhum resultado encontrado para "
        });
    }    
});

jQuery(document).on('click', '.btn-enviar-instrucoes', function(e) {
    e.preventDefault(); // impede submit/refresh
});

var $s = jQuery.noConflict();

$s('#attachment_alt').each(function() {
    var textbox = $s(document.createElement('textarea')).val(this.value);
    console.log(this.attributes);
    $s.each(this.attributes, function() {
        if (this.specified) {
            textbox.prop(this.name, this.value)
        }
    });
    $s(this).replaceWith(textbox);
});

$s('#attachment-details-two-column-alt-text').each(function() {
    var textbox = $s(document.createElement('textarea')).val(this.value);
    console.log(this.attributes);
    $s.each(this.attributes, function() {
        if (this.specified) {
            textbox.prop(this.name, this.value)
        }
    });
    $s(this).replaceWith(textbox);
});

window.onload = function() {
    var $div = $s("#__wp-uploader-id-0");
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === "class") {

                //var attributeValue = $s(mutation.target).prop(mutation.attributeName);
                //console.log("Class attribute changed to:", attributeValue);

                $s("[aria-describedby='alt-text-description']").each(function() {
                    var textbox = $s(document.createElement('textarea')).val(this.value);
                    //console.log(this.attributes);
                    $s.each(this.attributes, function() {
                        if (this.specified) {
                            textbox.prop(this.name, this.value)
                        }
                    });
                    $s(this).replaceWith(textbox);
                });
            }
        });
    });
    Array.from($div).forEach((div) => {
        observer.observe(div, {
            attributes: true, 
            subtree: true, 
            childList: true
        });
    });
};

// ==============================
// Helpers
// ==============================

function getPostType() {
    return $s('#post_type').length ? $s('#post_type').val() : null;
}

function getDeltaPadrao() {
    return [
        { insert: "🎉 Parabéns! Você foi sorteado e tem a presença confirmada!\n" },
        { insert: "[Cole aqui as informações do evento: O que é, Data, Gênero, Duração, Classificação Indicativa, Local, Endereço]\n\n" },
        { insert: "✏️ Atente-se para as instruções:\n" },
        { insert: "[Digite aqui as instruções específicas sobre o uso do ingresso, documentos obrigatórios etc.]\n\n" },
        { insert: "_____________\n" },
        { insert: "Sua avaliação é importante, o parceiro gosta de saber! Interaja:\n" },
        { insert: "- Enviando WhatsApp: (11) 3396-1162\n" },
        { insert: "- Mandando e-mail para " },
        {
            insert: "intranet.beneficios@sme.prefeitura.sp.gov.br",
            attributes: { link: "mailto:intranet.beneficios@sme.prefeitura.sp.gov.br" }
        },
        { insert: "\n" },
        { insert: "- Comentando na página do sorteio\n\n" },
        { insert: "Aproveitem! ✨" }
    ];
}

function getDeltaCortesias() {
    return [
        { insert: "🎉 Parabéns! Sua inscrição foi confirmada com sucesso.\n" },
        { insert: "[Cole aqui os detalhes da ação: descrição, data/período, tema (se houver), local/endereço ou orientações de retirada - conforme o tipo de evento]\n\n" },
        { insert: "✏️ Atente-se para as instruções:\n" },
        { insert: "[Digite aqui as instruções específicas: uso do benefício, retirada, documentos obrigatórios, prazos ou demais orientações]\n\n" },
        { insert: "_____________\n\n" },
        { insert: "Sua avaliação é importante, o parceiro gosta de saber! Interaja:\n" },
        { insert: "- Enviando WhatsApp: (11) 3396-1162\n" },
        { insert: "- Mandando e-mail para " },
        {
            insert: "intranet.beneficios@sme.prefeitura.sp.gov.br",
            attributes: { link: "mailto:intranet.beneficios@sme.prefeitura.sp.gov.br" }
        },
        { insert: "\n" },
        { insert: "- Comentando na página do sorteio\n\n" },
        { insert: "Aproveitem! ✨" }
    ];
}

function getDeltaPorPostType() {
    return getPostType() === 'cortesias'
        ? getDeltaCortesias()
        : getDeltaPadrao();
}

// ==============================
// Modal aberto
// ==============================

// Escuta abertura de QUALQUER modal, mesmo que tenha vindo via Ajax
$s(document).on('shown.bs.modal', '.modal', function () {
    var $modal = $s(this);

    var dataRef = $modal.data('data') || $modal.attr('id').replace('modal_', '');
    var $lista = $s('#sorteados_data_' + dataRef);
    var $inputAnexo = $modal.find('.input-anexo');

    // Função central que decide se anexo fica habilitado
    function toggleAnexo() {
        var totalSelecionados = $lista.find('.check-item:checked:not(:disabled)').length;
        var opcaoEnvio = $modal.find('input[name="opcao_envio"]:checked').val();

        if (opcaoEnvio === 'selecionados' && totalSelecionados === 1) {
            $inputAnexo.prop('disabled', false);
        } else {
            $inputAnexo.prop('disabled', true).val('');
        }
    }

    // Remove handlers antigos e reatribui
    $lista
        .off('change.toggleAnexo', '.check-item')
        .on('change.toggleAnexo', '.check-item', toggleAnexo);

    $modal
        .find('input[name="opcao_envio"]')
        .off('change.toggleAnexo')
        .on('change.toggleAnexo', toggleAnexo);

    // Define radio padrão ao abrir
    var totalMarcadosAoAbrir = $lista.find('.check-item:checked:not(:disabled)').length;
    var $btnRequerConfirmacao = $s('div[data-name="confirm_presen"] input[type="checkbox"]');

    if (totalMarcadosAoAbrir === 0 && $btnRequerConfirmacao.is(':checked')) {
        $modal.find('input[name="opcao_envio"][value="todos"]').prop('checked', true);
    } else if (totalMarcadosAoAbrir === 0 && !$btnRequerConfirmacao.is(':checked')) {
        $modal.find('input[name="opcao_envio"][value="geral"]').prop('checked', true);
    } else {
        $modal.find('input[name="opcao_envio"][value="selecionados"]').prop('checked', true);
    }

    toggleAnexo();

    // ==============================
    // Inicializa Quill
    // ==============================

    $modal.find('.editorEmail').each(function () {
        var $editor = $s(this);

        // Só inicializa se ainda não tiver instância
        if (!$editor.data('quill')) {
            var quill = new Quill($editor[0], {
                theme: 'snow',
                placeholder: 'Digite seu texto...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                        [{ size: ['small', false, 'large', 'huge'] }],
                        ['link']
                    ]
                }
            });

            quill.setContents(getDeltaPorPostType());
            $editor.data('quill', quill);

            var $btnEnviar = $modal.find('.btn-enviar');

            function toggleBtn() {
                $btnEnviar.prop('disabled', quill.getText().trim().length === 0);
            }

            toggleBtn();
            quill.on('text-change', toggleBtn);
        }
    });
});

// ==============================
// Modal fechado
// ==============================

$s('.modal').on('hidden.bs.modal', function () {
    var $modal = $s(this);

    // Desmarca todos os radios
    $modal.find('input[name="opcao_envio"]').prop('checked', false);

    // Reseta input de anexo
    var $inputAnexo = $modal.find('.input-anexo');
    $inputAnexo.prop('disabled', true).val('');

    $modal.find('.editorEmail').each(function () {
        var quill = $s(this).data('quill');
        if (quill) {
            quill.setContents(getDeltaPorPostType());
        }
    });
});