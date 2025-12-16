<?php

if ( is_user_logged_in() ){
    
    add_action('acf/input/admin_footer', 'cortesias_atualiza_endereco_com_observer');
    add_action('acf/input/admin_footer', 'valida_campos_obrigatorios');

	wp_enqueue_script('bootstrap-sorteio-js');
	wp_enqueue_style('datatables-css');
	wp_enqueue_script('datatables-js');
}

// Preenche o campo de endereço (ACF) com base no local (ACF) selecionado.
function cortesias_atualiza_endereco_com_observer() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'cortesias') return;

    $tags = get_terms([
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ]);

    $dados = [];
    foreach ($tags as $tag) {
        $dados[$tag->term_id] = esc_js($tag->description);
    }

    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const descricoes = <?php echo json_encode($dados); ?>;
        const select = document.querySelector('#local-evento select');
        const inputEndereco = document.querySelector('#endereco-evento input[type="text"]');

        function atualizarEndereco(termId) {
            const descricao = descricoes[termId];
            if (descricao) {
                inputEndereco.value = descricao;
                inputEndereco.setAttribute('readonly', 'readonly');
            } else {
                inputEndereco.value = '';
                inputEndereco.removeAttribute('readonly');
            }
        }

        function processarSelecao() {
            const termId = parseInt(select.value);
            if (!termId) return;

            if (!(termId in descricoes)) {
                // novo termo
                inputEndereco.value = '';
                inputEndereco.removeAttribute('readonly');
            } else {
                atualizarEndereco(termId);
            }
        }

        if (select && inputEndereco) {
            jQuery(select).on('select2:select', function () {
                processarSelecao();
            });

            // Executa a primeira vez
            processarSelecao();

            // Observa mudanças no select (ex: novo termo adicionado via botão "+")
            const observer = new MutationObserver(() => {
                processarSelecao();
            });

            observer.observe(select, {
                childList: true,
                subtree: true,
            });
        }
    });
    </script>
    <?php
}

// Adiciona a obrigatoriedade no campo de link quando a administração dos ingressos e feita pelo parceiro.
function valida_campos_obrigatorios() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'cortesias') return;

    ?>
    <script>
    jQuery(function ($) {

        function validaCamposObrigatorios() {
            const admIngressos = $('[data-name="administracao_ingressos"] label.selected input').val();
            const $linkInfo = $('[data-name="link_infos"]');
            const $linkTitle = $('[data-name="texto_do_link"]');

            if ( admIngressos === 'parceiro' ) {
                $linkInfo.find('input[type="url"]').first().prop("required", true);
                $linkInfo.find('.acf-label label').append('<span class="acf-required"> *</span>');

                $linkTitle.find('input[type="text"]').first().prop("required", true);
                $linkTitle.find('.acf-label label').append('<span class="acf-required"> *</span>');

            } else {
                $linkInfo.find('input[type="url"]').first().prop("required", false);
                $linkInfo.find('.acf-input .acf-error-message').remove();
                $linkInfo.find('.acf-label label span.acf-required').remove();

                $linkTitle.find('input[type="text"]').first().prop("required", false);
                $linkTitle.find('.acf-input .acf-error-message').remove();
                $linkTitle.find('.acf-label label span.acf-required').remove()
            }
        }

        $('[data-name="administracao_ingressos"] input[type="radio"]').on('change', function () {
            validaCamposObrigatorios();
        });

        validaCamposObrigatorios();
    })
    </script>
    <?php
}