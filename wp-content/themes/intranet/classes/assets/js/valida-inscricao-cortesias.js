jQuery(document).ready(function ($) {

    // Máscaras dos inputs
    $('#cpf').mask('000.000.000-00');
    $('#celular').mask('(00) 00000-0000');
    $('#telCom').mask('(00) 0000-0000');

    // IDs dos campos obrigatórios
    const camposObrigatorios = [
        '#nomeComp',
        '#emailInsti',
        '#cpf',
        '#emailSec',
        '#celular',
        '#dre',
        '#cargo_principal',
        '#uniSetor',
        '#ciente',
        '#qtd'
    ];

    // Variável global para armazenar o estado do CPF
    let cpfCadastrado = false;
    let cpfSancionado = false;
    let dataSancao = '';

    // Função para adicionar mensagem de erro
    function adicionarMensagemErro(campo, mensagemPersonalizada = 'Este campo é de preenchimento obrigatório.') {
        if ($(campo).is('input[type="checkbox"]')) {
            // Para o checkbox, adiciona a mensagem após o label
            if ($(campo).closest('.form-check').find('.mensagem-erro').length === 0) {
                $(campo).closest('.form-check').append('<span class="mensagem-erro">' + mensagemPersonalizada + '</span>');
            }
            $(campo).closest('.form-check').find('.mensagem-erro').show();
        } else {
            // Para outros campos, adiciona a mensagem após o campo
            if ($(campo).next('.mensagem-erro:visible').length === 0) {
                $(campo).after('<span class="mensagem-erro">' + mensagemPersonalizada + '</span>');
            }
            $(campo).next('.mensagem-erro').show();
        }
    }

    // Função para remover mensagem de erro
    function removerMensagemErro(campo) {
        if ($(campo).is('input[type="checkbox"]')) {
            // Para o checkbox, remove a mensagem do .form-check
            $(campo).closest('.form-check').find('.mensagem-erro').hide();
        } else {
            // Para outros campos, remove a mensagem após o campo
            $(campo).next('.mensagem-erro').hide();
        }
    }

    // Função para validar o CPF
    function validarCPF(cpf) {
        // Remove caracteres não numéricos (pontos e traço)
        const cpfLimpo = cpf.replace(/\D/g, '');
        // Verifica se o CPF tem 11 dígitos
        return /^\d{11}$/.test(cpfLimpo);
    }

    // Função para validar o email digitados nos campos do formulário de inscrição
    function possuiDominioEmailSuspeito(email) {

        if (!email || !email.includes('@')) {
            return false;
        }
    
        const dominio = email
            .split('@')
            .pop()
            .toLowerCase()
            .trim();

        return validacaoConfig.listaDominiosEmail.map(item => item.toLowerCase()).includes(dominio);
    
    }

    // Função para validar todos os campos obrigatórios
    function validarCampos(exibirMensagens = false) {
        let tipo_sorteio = jQuery('.news-content').first().data('tipo-evento');
        let todosPreenchidos = true;
        let camposComErro = [];

        // Validação dos campos obrigatórios
        camposObrigatorios.forEach(function (campo) {
            const $campo = $(campo);
            const label = $(`label[for="${$campo.attr('id')}"]`).text().trim() || campo;

            // Validação específica para o CPF
            if (campo === '#cpf') {
                const cpfValue = $campo.val();
                if ($.trim(cpfValue) === '') {
                    todosPreenchidos = false;
                    camposComErro.push(label);
                    if (exibirMensagens) adicionarMensagemErro($campo, 'Este campo é de preenchimento obrigatório.');
                } else if (!validarCPF(cpfValue)) {
                    todosPreenchidos = false;
                    camposComErro.push(`${label} (inválido)`);
                    if (exibirMensagens) adicionarMensagemErro($campo, 'CPF inválido. O CPF deve ter 11 dígitos.');
                } else {
                    removerMensagemErro($campo);
                }
                return;
            }

            // Validação específica para o campo de e-mail institucional/principal
            if (campo === '#emailInsti') {;
                const emailInstitucional = $.trim($campo.val());

                if (emailInstitucional === '') {
                    todosPreenchidos = false;
                    camposComErro.push(label);
                    if (exibirMensagens) adicionarMensagemErro($campo, 'Este campo é de preenchimento obrigatório.');

                } else if (possuiDominioEmailSuspeito(emailInstitucional)) {

                    todosPreenchidos = false;
                    camposComErro.push(`${label} (verificar)`);
                
                    if (exibirMensagens) {
                        adicionarMensagemErro(
                            $campo,
                            'O domínio informado parece estar incorreto. Verifique o e-mail digitado.'
                        );
                    }
                } else {
                    removerMensagemErro($campo);
                }
                return;
            }

            // Validação específica para o campo de e-mail secundário
            if (campo === '#emailSec') {
                const emailInstitucional = $.trim($('#emailInsti').val());
                const emailSecundario = $.trim($campo.val());

                if (emailSecundario === '') {

                    removerMensagemErro($campo);
                    todosPreenchidos = false;
                    camposComErro.push(label);
                    if (exibirMensagens) adicionarMensagemErro($campo, 'Este campo é de preenchimento obrigatório.');
                
                } else if (possuiDominioEmailSuspeito(emailSecundario)) {

                    removerMensagemErro($campo);
                    todosPreenchidos = false;
                    camposComErro.push(`${label} (verificar)`);
                
                    if (exibirMensagens) {
                        adicionarMensagemErro(
                            $campo,
                            'O domínio informado parece estar incorreto. Verifique o e-mail digitado.'
                        );
                    }

                } else if (emailSecundario === emailInstitucional) {

                    removerMensagemErro($campo);
                    todosPreenchidos = false;
                    camposComErro.push(`${label} (inválido)`);
                    if (exibirMensagens) adicionarMensagemErro($campo, 'O e-mail secundário deve ser diferente do e-mail institucional ou de uso principal.');
                } else {
                    removerMensagemErro($campo);
                }
                return;
            }

            // Validação para campos do tipo checkbox
            if ($campo.is('input[type="checkbox"]')) {
                if (!$campo.is(':checked')) {
                    todosPreenchidos = false;
                    camposComErro.push(label);
                    if (exibirMensagens) {
                        if ($campo.attr('id') === 'ciente') {
                            adicionarMensagemErro($campo, '<br>Você deve marcar esta opção para prosseguir.');
                        } else {
                            adicionarMensagemErro($campo);
                        }
                    }
                } else {
                    removerMensagemErro($campo);
                }
            }

            // Validação para campos do tipo select
            else if ($campo.is('select')) {
                if ($campo.val() === '') {
                    todosPreenchidos = false;
                    camposComErro.push(label);
                    if (exibirMensagens) adicionarMensagemErro($campo);
                } else {
                    removerMensagemErro($campo);
                }
            }

            // Validação para campos de texto, e-mail, etc.
            else {
                if ($.trim($campo.val()) === '') {
                    todosPreenchidos = false;
                    camposComErro.push(label);
                    if (exibirMensagens) adicionarMensagemErro($campo);
                } else {
                    removerMensagemErro($campo);
                }
            }
        });

        const dataSelecionada = $('input[name="data"]:checked').val();
        const grupoDatas = $('#grupo-data-selecionada');
        const labelDatas = grupoDatas.find('label[for="data-selecionada"]');

        if (!dataSelecionada && tipo_sorteio != 'periodo') {
            todosPreenchidos = false;
            (tipo_sorteio == 'premio') ? camposComErro.push('Prêmio que deseja participar') : camposComErro.push('Data que deseja participar');
            if (exibirMensagens && grupoDatas.find('.mensagem-erro').length === 0) {
                if (tipo_sorteio == 'premio') {
                    labelDatas.after('<br><span class="mensagem-erro">Selecione um prêmio.</span>');
                } else {
                    labelDatas.after('<br><span class="mensagem-erro">Selecione uma data.</span>');
                }
                
                grupoDatas.find('.mensagem-erro').show();
            } else {
                grupoDatas.find('.mensagem-erro').show();
            }
        } else {
            grupoDatas.find('.mensagem-erro').hide();
        }

        return { todosPreenchidos, camposComErro };
    }
    
    // Monitora campos obrigatórios em tempo real
    camposObrigatorios.forEach(function (campo) {
        $(campo).on('input change focus blur click', function () {
            validarCampos();
        });
    });

    // Também valida quando o grupo de datas for alterado
    $('input[name="data"]').on('change', function () {
        validarCampos();
    });

    // Validação inicial ao carregar a página (sem exibir mensagens de erro)
    validarCampos();

    // Verifica se o CPF já está cadastrado via AJAX
    const cpfInput = $('#cpf');
    const postIdInput = $('#comment_post_ID');

    cpfInput.on('input', function () {
        const cpfValue = cpfInput.val();
        const postId = postIdInput.val();

        // Remove caracteres não numéricos
        const cpfLimpo = cpfValue.replace(/\D/g, '');
        const cpfRegex = /^\d{11}$/;

        // Verifica se CPF tem 11 dígitos
        if (cpfRegex.test(cpfLimpo)) {
            $.ajax({
                url: '/wp-admin/admin-ajax.php', // Endpoint do WordPress para AJAX
                method: 'POST',
                data: {
                    action: 'verificar_cpf',
                    cpf: cpfLimpo,
                    post_id: postId,
                },
                success: function (response) {
                    if (response.success) {
                        if (response.data.sancao) {
                            cpfSancionado = true;
                            dataSancao = response.data.data_permissao || '';
                            Swal.fire({
                                icon: 'warning',
                                title: 'Atenção',
                                text: `Você está temporariamente impedido de se inscrever em novas oportunidades, devido à ausência em uma participação anterior. Você poderá realizar novas inscrições a partir de ${response.data.data_permissao}.`,
                                confirmButtonText: 'Fechar',
                            });
                        } else if (response.data.cadastrado && response.data.tipo_usuario != 'servidor') {
                            cpfCadastrado = true;
                            Swal.fire({
                                icon: 'warning',
                                title: 'Você já está inscrito!',
                                text: 'Caso queira cancelar sua inscrição, clique no botão "Cancelar Inscrição" abaixo.',
                                showCancelButton: true,
                                cancelButtonText: 'Fechar',
                                cancelButtonColor: "#6E7881",
                                showConfirmButton: true,
                                confirmButtonText: 'Cancelar Inscrição',
                                confirmButtonColor: "#DC3741",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const emails = response.data.emails_cadastrados;
                                    const emailsString = Object.values(emails)
                                        .filter(email => email && email.trim() !== '')
                                        .join('  |  ');                                    
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Solicitar cancelamento de inscrição',
                                        html: `
                                            <p>Enviaremos um link para finalização do cancelamento para o(s) e-mail(s) abaixo. Verifique sua caixa de entrada ou spam.</p>
                                            <em class="badge badge-light">${emailsString}</em>
                                        `,
                                        showCancelButton: true,
                                        cancelButtonText: 'Fechar',
                                        cancelButtonColor: "#6E7881",
                                        showConfirmButton: true,
                                        confirmButtonText: 'Confirmar Cancelamento',
                                        confirmButtonColor: "#DC3741",
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Chamada AJAX para enviar_email_cancelar
                                            $.ajax({
                                                url: '/wp-admin/admin-ajax.php',
                                                method: 'POST',
                                                data: {
                                                    action: 'enviar_email_cancelar_cortesia',
                                                    cpf: cpfLimpo,
                                                    post_id: postId,
                                                },
                                                success: function (response) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Cancelamento solicitado!',
                                                        text: 'Enviamos um e-mail com o link para finalização do cancelamento. Verifique sua caixa de entrada ou spam.',
                                                        confirmButtonText: 'Fechar',
                                                    });

                                                    console.log('Resposta do servidor:', response);
                                                },
                                                error: function (error) {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Erro ao solicitar cancelamento',
                                                        text: 'Tente novamente mais tarde.',
                                                        confirmButtonText: 'Fechar',
                                                    });
                                                    console.error('Erro ao enviar cancelamento:', error);
                                                }
                                            });
                                        }
                                    });

                                }
                            });
                        } else {
                            cpfCadastrado = false;
                            cpfSancionado = false;
                        }
                        validarCampos(); // Revalida após AJAX
                    } else {
                        console.log('Erro ao verificar CPF. Tente novamente.');
                    }
                },
                error: function (error) {
                    console.error('Erro ao verificar CPF:', error);
                },
            });
        } else {
            // CPF inválido
            cpfCadastrado = false;
            removerMensagemErro('#cpf');
            adicionarMensagemErro('#cpf', 'CPF inválido. O CPF deve ter 11 dígitos.');
            validarCampos();
        }
    });

    let confirmouEmails = false;

    // Impede envio do formulário se CPF estiver repetido ou campos estiverem incompletos
    $('#form-inscri').on('submit', function (event) {

        if (confirmouEmails) {
            return true;
        }
        
        const { todosPreenchidos, camposComErro } = validarCampos(true);

        if (cpfCadastrado) {
            event.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Você já está inscrito neste evento!',
                text: 'Em breve enviaremos por e-mail as instruções para utilização do(s) ingresso(s)',
                confirmButtonText: 'Fechar',
            });
        } else if(cpfSancionado) {
            event.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: `Você está temporariamente impedido de se inscrever em novas oportunidades, devido à ausência em uma participação anterior. Você poderá realizar novas inscrições a partir de ${dataSancao}.`,
                confirmButtonText: 'Fechar',
            });
        } else if (!todosPreenchidos) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Preenchimento obrigatório!',
                html: `<p>Por favor, preencha os seguintes campos obrigatórios:</p><ul style="text-align:left">${camposComErro.map(c => `<li>${c}</li>`).join('')}</ul>`,
                confirmButtonText: 'Ok',
                confirmButtonColor: '#14447C'
            });
        } else {
            event.preventDefault();

            const emailPrincipal = $('#emailInsti').val();
            const emailSecundario = $('#emailSec').val();

            Swal.fire({
                html: `
                    <div class="text-left">
                        <h2 class="mb-4"><i class="fa fa-exclamation-circle text-warning" aria-hidden="true"></i> Confirme seus e-mails</h2>
                        <span>Antes de concluir sua inscrição, confira se os e-mails informados estão corretos:</span>

                        <div class="my-3">
                            <div><strong>E-mail principal:</strong> <strong class="new-text-primary">${emailPrincipal}</strong></div>
                            <div><strong>E-mail secundário:</strong> <strong class="new-text-primary">${emailSecundario}</strong></div>
                        </div>

                        <span>Toda comunicação sobre o evento, caso você seja contemplado(a), será enviada para esses e-mails.</span>

                        <p class="mt-4">Deseja continuar?</p>
                        <hr>
                    </div>
                `,
                width: 600,
                showCancelButton: true,
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não',
                confirmButtonColor: '#14447C',
                reverseButtons: true

            }).then((result) => {

                if (result.isConfirmed) {
                    confirmouEmails = true;
                    $('#form-inscri').submit();
                }
            });
        }
    });

});