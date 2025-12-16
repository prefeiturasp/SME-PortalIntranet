jQuery(document).ready(function($) {
    $("#exportar-sorteados").click(function(e) {
        e.preventDefault();
        const button = $(this);
        const status = $("#export-status");
        const $btnRequerConfirmacao = $('div[data-name="confirm_presen"] input[type="checkbox"]');
        let html = `
            <div class="swal2-html-container">Escolha quais participantes deseja incluir no relatório de sorteados.</div>
            <div style="text-align: left; margin-top: 15px;">
                <label style="display: block; margin-bottom: 10px;">
                    <input type="radio" name="filtro" value="todos" checked>
                    <strong>Todos os participantes</strong><br>
                    <small>Inclui todos os sorteados, independentemente do status de confirmação.</small>
                </label>
                <label style="display: block;">
                    <input type="radio" name="filtro" value="confirmados">
                    <strong>Apenas quem confirmou presença</strong><br>
                    <small>Inclui somente os participantes que confirmaram presença.</small>
                </label>
            </div>
            `
        if (!$btnRequerConfirmacao.is(':checked')) {
            html = `
                <div style="text-align: center; margin-top: 10px;">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="radio" name="filtro" value="todos" class="d-none" checked>
                        <h6>Como este evento não requer confirmação de presença, todos os sorteados serão incluídos automaticamente no relatório.</h6>
                    </label>
                </div>
                `
        }

        Swal.fire({
            title: 'Gerar Relatório de Sorteados',
            html: html,
            iconHtml: '<span class="dashicons dashicons-warning"></span>',
            customClass: {
                popup: 'popup-notificar-sorteados',
            },
            showCancelButton: true,
            confirmButtonText: 'Gerar Relatório',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const selected = document.querySelector('input[name="filtro"]:checked');
                if (!selected) {
                    Swal.showValidationMessage('Você precisa selecionar uma opção!');
                }
                return selected ? selected.value : null;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const filtro = result.value;

                button.prop("disabled", true);
                status.text("Gerando relatório...").removeClass("error");

                // Mostrar spinner de carregamento
                Swal.fire({
                    title: 'Aguarde...',
                    text: 'Estamos gerando o relatório.',
                    icon: 'info',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                        action: "exportar_sorteados_excel",
                        post_id: button.data("post-id"),
                        filtro: filtro,
                        _ajax_nonce: exportVars.nonce
                    },
                    xhrFields: {
                        responseType: "blob"
                    },
                    success: function(data) {
                        const filename = `relatorio_sorteados_${exportVars.dataAtual}.xlsx`;
                        const url = window.URL.createObjectURL(data);
                        const a = document.createElement("a");
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        Swal.fire({
                            icon: 'success',
                            title: 'Relatório gerado com sucesso!',
                            showConfirmButton: true
                        });
                    },
                    error: function(xhr) {
                        status.text("Erro ao gerar relatório").addClass("error");
                        console.error("Erro na exportação:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao gerar o relatório',
                            text: 'Tente novamente mais tarde.',
                            showConfirmButton: true
                        });
                    },
                    complete: function() {
                        setTimeout(function() {
                            status.text("");
                            button.prop("disabled", false);
                        }, 3000);
                    }
                });
            }
        });
    });
});