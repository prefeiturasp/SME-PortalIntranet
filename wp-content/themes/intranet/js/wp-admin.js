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
        { insert: "[Cole aqui as informações do evento: O que é, Data, Tipo de Evento, Duração, Classificação Indicativa, Local, Endereço]\n\n" },
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

    $modal.find('input[name="opcao_envio"]').prop('checked', false);

    var $inputAnexo = $modal.find('.input-anexo');
    $inputAnexo.prop('disabled', true).val('');

    $modal.find('.editorEmail').each(function () {
        var quill = $s(this).data('quill');
        if (quill) {
            quill.setContents(getDeltaPorPostType());
        }
    });
});

jQuery(function ($) {

  // Armazena seleções por usuário
  let selecoes = {};

  const iconesContato = {
        1: {
            src: `${wpContato.themeUrl}/img/icon-telefone.svg`,
            alt: 'Telefone'
        },
        2: {
            src: `${wpContato.themeUrl}/img/icon-email.svg`,
            alt: 'Email'
        },
        3: {
            src: `${wpContato.themeUrl}/img/icon-whatsapp.svg`,
            alt: 'WhatsApp'
        }
    };

  $('.btn-contato').each(function () {

    const $btn = $(this);
    const userId = $btn.data('user-id');
    const valorInicial = $btn.data('valor-atual');

    if (valorInicial) {
      selecoes[userId] = valorInicial;
    }

    $btn.popover({
      html: true,
      sanitize: false,
      placement: 'right',
      container: 'body',
      boundary: 'viewport',
      trigger: 'click',
      template: `
        <div class="popover popover-forma-contato" role="tooltip">
          <div class="arrow"></div>
          <h3 class="popover-header"></h3>
          <div class="popover-body"></div>
        </div>
      `,
      content: function () {

        // Clona o template
        const $content = $($('div.popover-template').html());

        // Gera IDs e names únicos usando data-user-id
        $content.find('input[type="radio"]').each(function () {

          const $radio = $(this);
          const valor = $radio.val();

          const radioId = `forma-contato-${userId}-${valor}`;
          const radioName = `forma_contato_${userId}`;

          $radio
            .attr('id', radioId)
            .attr('name', radioName);

          $radio
            .closest('.form-check')
            .find('label')
            .attr('for', radioId);
        });

        // Restaura seleção, se existir
        if (selecoes[userId]) {
          $content
            .find(`input[value="${selecoes[userId]}"]`)
            .prop('checked', true);
        }

        return $content;
      }
    });
  });

  // Fecha popovers ao clicar fora
  $(document).on('click', function (e) {
    if (
      !$(e.target).closest('.popover').length &&
      !$(e.target).closest('.btn-contato').length
    ) {
      $('.btn-contato').popover('hide');
    }
  });

  // Mudança do radio
  $(document).on('change', 'input[type="radio"][name^="forma_contato_"]', function () {

    const $radio = $(this);
    const valor = $radio.val();

    const $popover = $radio.closest('.popover');
    const popoverId = $popover.attr('id');

    const $btn = $(`.btn-contato[aria-describedby="${popoverId}"]`);

    if (!$btn.length) return;

    const userId = $btn.data('user-id');
    const tipo_evento = $btn.data('tipo');

    const $icone = $(`.icone-user-${userId}`);

    // Salva localmente
    selecoes[userId] = valor;

    // AJAX
    $.ajax({
      url: ajaxurl,
      method: 'POST',
      data: {
        action: 'salvar_forma_contato',
        user_id: userId,
        tipo_evento: tipo_evento,
        tipo_contato: valor
      }
    }).done(function (response) {
        if (response.success) {

            if (iconesContato[valor]) {
                const iconData = iconesContato[valor];

                const $img = $('<img>', {
                    src: iconData.src,
                    alt: iconData.alt,
                    class: 'icone-contato'
                });

                $icone.empty().append($img);
            }

            toastr.options.positionClass = 'toast-bottom-right';
            toastr.success('Forma de contato registrada com sucesso!');
        } else {
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.error('Erro ao registrar forma de contato.');
        }
    })
    .fail(function (jqXHR, textStatus) {
        // erro técnico (timeout, 500, etc)
        toastr.options.positionClass = 'toast-bottom-right';
        toastr.error('Erro de comunicação com o servidor.');
    });
  });

});

// Fechar quando abrir outro popover
$s(document).on('click', '.btn-contato', function (e) {
  e.stopPropagation();

  // Fecha todos os outros
  $s('.btn-contato').not(this).popover('hide');
});

$s(document).on('shown.bs.popover', function () {
    $s('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        trigger: 'hover'
    });
});

// ==============================
// Calendário dos sorteios (widget)
// ==============================

jQuery(function($){
    $(document).on('click', '.js-bloco-toggle .js-toggle-lista', function(e){

        e.preventDefault();
        e.stopImmediatePropagation();

        var $bloco = $(this).closest('.js-bloco-toggle');
        var $lista = $bloco.find('.js-lista-conteudo');
        var $icon  = $bloco.find('.js-toggle-icon');

        $lista.stop(true,true).slideToggle(200);
        $icon.toggleClass('is-open');

    });
});

// ==============================
// Histórico de participantes
// ==============================

jQuery(document).ready(function($) {
    var tabelaEventos = $('.historico-participantes#tabela-eventos').DataTable({
        ordering: false,
        lengthChange: false,
        searching: true,
        dom: 'rtip',
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        }
    });

    // Busca personalizada
    $('.filtro-eventos-participante #evento-input').on('keyup', function() {
        tabelaEventos.search($(this).val()).draw();
    });

    // Botão limpar
    $('.filtro-eventos-participante #btn-limpar-filtro').on('click', function() {
        $('.filtro-eventos-participante #evento-input').val('');
        tabelaEventos.search('').draw();
    });
});

// ==============================
// Botão para ver email de instruções
// ==============================

jQuery(function($){

    $(document).on('click', '.ver-email-instrucao', function(e){

        e.preventDefault();

        var inscricao_id = $(this).data('inscricao');

        $('#email-participante-nome').text('');
        $('#email-participante-email1').text('');
        $('#email-participante-email2').text('');

        $('#email-evento').text('');
        $('#email-admin').text('');
        $('#email-data').text('');

        $('#email-mensagem').html('Carregando...');

        $.ajax({

            url: ajaxurl,
            type: 'POST',

            data:{
                action: 'buscar_email_instrucao',
                inscricao_id: inscricao_id
            },

            success:function(response){

                console.log(response);

                if(response.success){

                    $('#email-participante-nome')
                        .text(response.data.nome);

                    $('#email-participante-email1')
                        .text(response.data.email1);

                    $('#email-participante-email2')
                        .text(response.data.email2);

                    $('#email-evento')
                        .text(response.data.evento);

                    $('#email-admin')
                        .text(response.data.admin);

                    $('#email-data')
                        .text(response.data.data_envio);

                    $('#email-mensagem')
                        .html(response.data.mensagem);

                }else{

                    $('#email-mensagem')
                        .html('Nenhuma informação encontrada.');

                }

                $('#modalEmailInstrucao').modal('show');

            },

            error:function(){

                $('#email-mensagem')
                    .html('Erro ao carregar os dados.');

                $('#modalEmailInstrucao').modal('show');

            }

        });

    });

    $(document).on('click', '#copiar-dados-email', function () {
        var htmlOriginal = $('#email-mensagem').html();

        var temp = document.createElement('div');
        temp.innerHTML = htmlOriginal;

        // Converter emojis do WordPress (<img class="emoji"> → emoji real)
        temp.querySelectorAll('img.emoji').forEach(function(img){
            img.replaceWith(img.alt);
        });

        // Converter <br> em quebra de linha
        temp.querySelectorAll('br').forEach(function(br){
            br.replaceWith("\n");
        });

        // Converter parágrafos em quebra de linha
        temp.querySelectorAll('p').forEach(function(p){
            p.append("\n");
        });

        // Converter itens de lista
        temp.querySelectorAll('li').forEach(function(li){
            li.prepend("• ");
            li.append("\n");
        });

        var texto = temp.textContent.trim();

        navigator.clipboard.writeText(texto).then(function () {
            toastr.success('Conteúdo copiado com sucesso!');
        }).catch(function () {
            toastr.error('Não foi possível copiar o conteúdo.');
        });
    });


});