jQuery(function ($) {

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
