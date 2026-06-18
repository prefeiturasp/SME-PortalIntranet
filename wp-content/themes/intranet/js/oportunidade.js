jQuery(function ($) {
    $(document).on('click', '.btn-inscricao', function () {

        const button = $(this);
        const oportunidadeId = oportunidade.id;
        const tituloOportunidade = $('.titulo-oportunidade').text()

        if (URL.canParse(oportunidade.form_complementar)) {

            Swal.fire({
                html: `
                    <div class="text-left modal-form-complementar">
                        <h3 class="mb-3">
                            <i class="fa fa-exclamation-triangle text-warning mr-2" aria-hidden="true"></i>
                            Formulário Complementar Obrigatório
                        </h3>
                        <p class="text-secondary">Para concluir sua participação neste processo seletivo, é necessário preencher o formulário complementar.</p>
                        <p><strong>O não preenchimento poderá resultar em desclassificação no processo seletivo.</strong></p>
                        <p><strong>Caso você tenha preenchido o formulário, clique no botão "Continuar Inscrição"</strong></p>
                        <p class="text-secondary">Deseja continuar mesmo assim?</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ir para o formulário complementar',
                cancelButtonText: 'Continuar inscrição',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    cancelButton: 'btn btn-link text-dark',
                    confirmButton: 'btn btn-primary'
                },
                preConfirm: () => {
                    window.open(oportunidade.form_complementar, '_blank');
            
                    return false;
                }
            }).then((result) => {
        
                if (result.dismiss === Swal.DismissReason.cancel) {
                    realizarInscricao(oportunidadeId);
                }
        
            });

        } else {
            Swal.fire({
                html: `
                    <div class="text-left">
                        <h3 class="mb-3">Confirmar inscrição</h3>
                        <p>Você confirma sua inscrição na oportunidade <strong>"${tituloOportunidade}"</strong>?</p>
                        <p>Certifique-se de que seu currículo está atualizado com todas as informações necessárias.</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirmar inscrição',
                confirmButtonColor: '#14447C',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
        
                if (!result.isConfirmed) {
                    return;
                }
        
                realizarInscricao(oportunidadeId);
        
            });
        }
    });

    function realizarInscricao(oportunidadeId) {

        const tituloOportunidade = $('.titulo-oportunidade').text();

        $.ajax({
            url: oportunidade.ajax_url,
            type: 'POST',
    
            data: {
                action: 'realizar_inscricao',
                nonce: oportunidade.nonce_inscricao,
                oportunidade_id: oportunidadeId
            },
    
            beforeSend: function () {
                Swal.fire({
                    title: 'Realizando sua inscrição...',
                    text: 'Aguarde alguns instantes.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
    
            success: function (response) {
    
                if (!response.success) {
    
                    Swal.fire({
                        icon: 'error',
                        title: 'Não foi possível realizar a inscrição',
                        text: response.data.message,
                        confirmButtonText: 'Fechar',
                        confirmButtonColor: '#14447C',
                    });
    
                    return;
                }
    
                Swal.fire({
                    html: `
                        <div class="text-left">
                            <h2 class="mb-3"><i class="fa fa-check-circle-o text-success" aria-hidden="true"></i> Inscrição realizada com sucesso!</h2>
                            <p>Sua inscrição na Oportunidade <strong>"${tituloOportunidade}"</strong> foi realizada com sucesso!</p>
                        </div>
                    `,
                    confirmButtonText: 'Fechar',
                    confirmButtonColor: '#14447C',
                }).then(() => {
    
                    location.reload();
    
                });
    
            },
    
            error: function () {
    
                Swal.fire({
                    icon: 'error',
                    title: 'Erro inesperado',
                    text: 'Ocorreu um erro ao processar sua inscrição.',
                    confirmButtonText: 'Fechar',
                    confirmButtonColor: '#14447C',
                });
    
            },

        });
    }
});