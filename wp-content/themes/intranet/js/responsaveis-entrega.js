jQuery(function ($) {

    function sincronizarOpcaoResponsavel(lista) {
        const select = lista.find('.select-responsavel-entrega').first();
        const salvo = select.attr('data-valor-salvo');
        const disponivel = !!salvo && select.val() === salvo && !select.prop('disabled');
        const checkbox = lista.find('input[name="responsavel-entrega"]');
        checkbox.prop('disabled', !disponivel);
        if (!disponivel) checkbox.prop('checked', false);
        checkbox.siblings('label').toggleClass('text-muted', !disponivel);
    }

    $(document).on('show.bs.modal', '.conteudo-lista .modal', function () {
        sincronizarOpcaoResponsavel($(this).closest('.conteudo-lista'));
    });
    
    $(document).on('change', '.select-responsavel-entrega', function () {
        const select = $(this);
        const bloco = select.closest('.responsavel-entrega-sessao');
        const uuid = select.val();
        const anterior = select.attr('data-valor-salvo') || '';

        if (uuid === anterior) return;

        select.prop('disabled', true).attr('aria-busy', 'true');
        sincronizarOpcaoResponsavel(select.closest('.conteudo-lista'));

        function falha(mensagem) {
            select.val(anterior);
            toastr.error(mensagem + ' A seleção anterior foi restaurada.', '', { escapeHtml: true });
        }

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'salvar_responsavel_entrega_sessao',
                _ajax_nonce: bloco.attr('data-nonce'),
                post_id: bloco.attr('data-post-id'),
                sessao: bloco.attr('data-sessao'),
                uuid: uuid
            }
        }).done(function (resposta) {
            if (resposta.success) {
                select.attr('data-valor-salvo', resposta.data.uuid);
                toastr.success(resposta.data.message, '', { escapeHtml: true });
            } else {
                falha(resposta.data && resposta.data.message ? resposta.data.message : 'Não foi possível salvar.');
            }
        }).fail(function (xhr) {
            const resposta = xhr.responseJSON;
            falha(resposta && resposta.data && resposta.data.message
                ? resposta.data.message : 'Não foi possível salvar. Tente novamente.');
        }).always(function () {
            select.prop('disabled', false).removeAttr('aria-busy');
            sincronizarOpcaoResponsavel(select.closest('.conteudo-lista'));
        });
    });

    //Adiciona o modal de confirmação antes da remoção de uma linha do repetidor de "Responsáveis pela entrega".
    let confirmacaoAberta = false;
    document.addEventListener('click', function (event) {
        const botao = event.target.closest(
            '[data-event="remove-row"], [data-event="remove-row-confirm"], .acf-repeater-remove-row, .acf-repeater .acf-icon.-minus'
        );
        if (!botao) return;

        const campo = botao.closest('.acf-field[data-type="repeater"]');
        if (!campo || campo.dataset.name !== 'lista_responsaveis_entrega') return;

        event.preventDefault();
        event.stopImmediatePropagation();
        if (confirmacaoAberta) return;

        const row = $(botao).closest('.acf-row');

        confirmacaoAberta = true;
        Swal.fire({
            title: 'Você tem certeza?',
            text: 'A remoção será efetivada ao acionar o botão "Atualizar".',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Remover',
            confirmButtonColor: '#14447C',
            cancelButtonText: 'Cancelar',
            focusCancel: true

        }).then(function (result) {

            if (result.isConfirmed && document.contains(botao)) {
                const repeater = acf.getClosestField($(botao));

                if (repeater && row.length) {
                    repeater.remove(row);
                }
            }
        }).finally(function () {

            confirmacaoAberta = false;
            if (document.contains(botao)) {
                botao.focus();
            }
        });
    }, true);
});
