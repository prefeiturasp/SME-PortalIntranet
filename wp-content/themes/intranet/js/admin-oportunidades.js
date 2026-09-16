//Taxonomias
jQuery(function ($) {

    const configuracoes = {

        locais: {
            nome: 'Sigla e descrição completa da Coord/Div/DREs',
            texto_apoio_nome: 'Este campo deverá ser preenchido com a sigla e a descrição completa da Coord/Div/DREs (Ex: COPED – Coordenadoria Pedagógica, DIDEM - Divisão de Planejamento da Demanda Escolar, etc)',
            endereco: 'Endereço da Unidade de Exercício',
            texto_apoio_endereco: 'Este campo deverá ser preenchido com o endereço completo da Coord/Div/DREs (Ex: Rua Borges Lagoa, 1230, Vila Clementino, CEP: 04038-003)',
            btn_salvar: 'Salvar Local'
        },

        eixos_atuacao: {
            nome: 'Título do Eixo de Atuação',
            texto_apoio_nome: 'Informe, de forma ampla, o eixo de atuação relacionado às atribuições e à área de atuação da especialidade desejada. O texto informado será utilizado como título do eixo. Exemplo: Inclusão e Acessibilidade',
            btn_salvar: 'Salvar Eixo'
        },
    };

    const taxonomy = new URLSearchParams(window.location.search).get('taxonomy');
    const configuracao = configuracoes[taxonomy];

    if (!configuracao) {
        return;
    }

    function alterarLabel(selector, texto) {

        const $label = $(selector);
    
        if ($label.length) {
            $label.text(texto);
        }
    }

    // Cadastro/Listagem
    alterarLabel('label[for="tag-name"]', configuracao.nome);
    alterarLabel('#name-description', configuracao.texto_apoio_nome);
    alterarLabel('label[for="tag-description"]', configuracao.endereco);
    alterarLabel('#description-description', configuracao.texto_apoio_endereco);
    alterarLabel('thead .column-name a span:eq(0)', configuracao.nome);
    alterarLabel('thead .column-description a span:eq(0)', configuracao.endereco);
    alterarLabel('tfoot .column-name a span:eq(0)', configuracao.nome);
    alterarLabel('tfoot .column-description a span:eq(0)', configuracao.endereco);
    
    // Tela de edição
    alterarLabel('label[for="name"]', configuracao.nome);
    alterarLabel('label[for="description"]', configuracao.endereco);
    
    $('p.submit #submit').val(configuracao.btn_salvar);

    // Adiciona "required" nos campos para informar que são de preenchimento obrigatório
    $('#name').attr('required', true);
    $('#description').attr('required', taxonomy === 'locais');

});

//Post type
jQuery(function($) {
    $(document).find('#submitdiv .postbox-header h2').text('Ações');
});