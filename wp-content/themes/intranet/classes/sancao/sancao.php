<?php
class Sancao_Options_Page {

    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    function admin_menu() {
        add_submenu_page(
            'edit.php',                         // Menu pai: Posts
            'Aplicar Sanção em Participantes',  // Título da página
            'Aplicar Sanção',                   // Texto do menu
            'manage_options',                   // Capabilidade
            'aplicar-sancao',                   // Slug
            array( $this, 'render_page' )       // Callback
        );
    }

    function render_page() {
        // Scripts/estilos necessários para postboxes
        wp_enqueue_script('postbox');
        wp_enqueue_script('dashboard');
        wp_enqueue_style('dashboard');
        wp_enqueue_script('sorteio-js');
        wp_enqueue_style('select2-css');        
        wp_enqueue_script('select2-js');
        wp_enqueue_style('bootstrap-sorteio-css');
        wp_enqueue_script('bootstrap-sorteio-js');
        wp_enqueue_style('toastr-sorteio-css');
        wp_enqueue_script('toastr-sorteio-js');
        wp_enqueue_style('sweetalert-sorteio-css');
	    wp_enqueue_script('sweetalert-sorteio-js');
        wp_enqueue_style('select2-bootstrap4-css', 'https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css');


        ?>
        <div class="wrap">
            <h1>Aplicar Sanção em Participantes</h1>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    
                    <!-- Conteúdo principal -->
                    <div id="post-body-content">
                        <?php 
                        $this->register_metaboxes();
                        do_meta_boxes( 'aplicar-sancao', 'normal', null ); 
                        ?>
                    </div>

                    <!-- Sidebar -->
                    <div id="postbox-container-1" class="postbox-container">
                        <?php do_meta_boxes( 'aplicar-sancao', 'side', null ); ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($){
            postboxes.add_postbox_toggles('aplicar-sancao'); // mesmo slug do submenu
        });
        </script>
        <?php
    }

    function register_metaboxes() {
        // Metabox principal
        add_meta_box(
            'sancao_form',                  // ID
            'Localizar Evento e/ou Participante para Sanção',         // Título
            array( $this, 'box_form' ),     // Callback
            'aplicar-sancao',               // Tela (slug da página)
            'normal',                       // Contexto
            'default'                       // Prioridade
        );

        // Metabox lateral
        add_meta_box(
            'sancao_config',                // ID
            'Listagem de Participantes',         // Título
            array( $this, 'box_participantes' ),   // Callback
            'aplicar-sancao',               // Tela
            'normal',                       // Contexto
            'default'
        );
    }

    function box_form() {
        global $wpdb;

        $results = $wpdb->get_results("
            SELECT p.ID, p.post_title, p.post_date
            FROM int_inscricoes i
            INNER JOIN int_posts p ON p.ID = i.post_id
            WHERE i.sorteado = 1
            AND p.post_type = 'post'
            AND p.post_status = 'publish'
            GROUP BY p.ID, p.post_title, p.post_date
            ORDER BY p.post_date DESC
        ");

        echo '<div class="form-group">';        
        echo '<div class="row">';
            
            echo '<div class="col-md-5">';
                echo '<label for="post_id_select">Nome do evento</label>';
                echo '<select id="post_id_select" name="post_id" class="form-control">';
                echo '<option value="">Selecione um evento...</option>'; // Corrigido
                foreach ( $results as $row ) {
                    echo '<option value="' . esc_attr($row->ID) . '">' . esc_html($row->post_title) . ' (' . esc_html($row->ID) . ')</option>';
                }
                echo '</select>';
            echo '</div>';
            
            echo '<div class="col-md-7">';
                echo '<label for="participante">Nome do participante</label>';
                echo '<input type="text" name="participante" id="participante" class="form-control" placeholder="Nome do participante" disabled>';
            echo '</div>';

            echo '<div class="col-12 d-flex justify-content-between mt-4">';
                echo '<button id="relatorio-sancoes" class="btn btn-sm">Relatório de Sanções Ativas</button>';
                echo '<button id="buscar-participantes" class="btn btn-sm">Buscar participantes</button>';
            echo '</div>';

        echo '</div>'; // .row
        echo '</div>'; // .form-group

        // JS inline para ativar select2
        ?>
        <script>
            jQuery(document).ready(function($){
                // Ativa o select2
                $('#post_id_select').select2({
                    placeholder: "Selecione um evento",
                    allowClear: true,
                    width: '100%',
                    theme: 'bootstrap4',
                    language: {
                        noResults: function() {
                            return "Nenhum evento encontrado";
                        }
                    }
                });

                // Habilita/desabilita o input de participante com base na seleção
                $('#post_id_select').on('change', function() {
                    var valor = $(this).val();

                    if (valor) {
                        $('#participante').prop('disabled', false);
                    } else {
                        $('#participante').prop('disabled', true).val('');
                    }
                });

                // Clique no botão
                $('#buscar-participantes').on('click', function(e){
                    e.preventDefault();

                    let post_id = $('#post_id_select').val();
                    var participante = $('#participante').val();

                    if (!post_id) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            text: 'Selecione um evento antes de buscar os participantes.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Modal de loading
                    Swal.fire({
                        title: 'Aguarde um instante...',
                        html: 'Estamos localizando participantes sorteados...',
                        iconHtml: '<span class="dashicons dashicons-warning"></span>',
                        customClass: {
                            popup: 'popup-notificar-sorteados',
                        },
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading(); // mostra o spinner
                        }
                    });

                    // Chamada AJAX
                    $.post(ajaxurl, {
                        action: 'exibir_lista_sorteados_por_data',
                        post_id: post_id,
                        participante: participante
                    }, function(response){
                        // Insere resultado na div
                        $('#lista-participantes').html(response);
                        $('#lista-participantes').attr('data-post', post_id);

                        // Fecha todos os accordions
                        $('#lista-participantes-sorteados .collapse')
                            .removeClass('show')
                            .attr('aria-expanded', 'false');

                        // Abre só o primeiro accordion
                        let $primeiro = $('#lista-participantes-sorteados .collapse').first();
                        $primeiro.addClass('show');

                        // Ajusta o toggle correspondente
                        let targetId = '#' + $primeiro.attr('id');
                        $('#lista-participantes-sorteados .accordion-toggle[data-target="'+targetId+'"]')
                            .removeClass('collapsed')
                            .attr('aria-expanded', 'true');

                        // Fecha o modal
                        Swal.close();
                    });
                });
            });
        </script>
        <?php
    }

    function box_participantes() {
        echo '<div id="lista-participantes">';
        echo '</div>'; // #lista-participantes
    }
}

new Sancao_Options_Page;