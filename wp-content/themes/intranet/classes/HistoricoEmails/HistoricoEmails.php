<?php
class Historico_Emails {

    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    function admin_menu() {
        add_submenu_page(
            'edit.php',                             // Menu pai: Posts
            'Histórico de E-mails de Instrução',    // Título da página
            'Histórico de E-mails',                 // Texto do menu
            'manage_options',                       // Capabilidade
            'historico-emails',                     // Slug
            array( $this, 'render_page' )           // Callback
        );
    }

    function register_metaboxes() {
        // Metabox principal
        add_meta_box(
            'historico_emails_form',    // ID
            'Localizar Evento',         // Título
            array( $this, 'box_form' ), // Callback
            'historico-emails',         // Tela (slug da página)
            'normal',                   // Contexto
            'default'                   // Prioridade
        );

        // Metabox lateral
        add_meta_box(
            'historico_emails_container',   // ID
            'Listagem de Remetentes',       // Título
            array( $this, 'box_envios' ),   // Callback
            'historico-emails',             // Tela
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
        ?>

        <div class="form-group mt-3">
            <div class="row justify-content-between align-items-center">

                <div class="col-10">
                    <label for="post_id_select">Nome do Evento</label>
                    <select id="post_id_select" name="post_id" class="form-control">
                        <option value="">Selecione um evento...</option>

                        <?php foreach ( $results as $row ) : ?>
                            <option value="<?= esc_attr( $row->ID ); ?>">
                                <?= esc_html( $row->post_title ); ?> (<?= esc_html( $row->ID ); ?>)
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="col-2 align-self-end">
                    <button id="buscar-eventos" class="btn btn-danger btn-block">Buscar</button>
                </div>

            </div> <!-- .row -->
        </div> <!-- .form-group -->

        <script>
            // JS inline para ativar select2
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

                // Clique no botão
                $('#buscar-eventos').on('click', function(e){
                    e.preventDefault();

                    let post_id = $('#post_id_select').val();
                    var participante = $('#participante').val();

                    if (!post_id) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            text: 'Selecione um evento para realizar a busca.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Modal de loading
                    Swal.fire({
                        title: 'Aguarde um instante...',
                        html: 'Estamos buscando o histórico de envios para o evento selecionado.',
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
                        action: 'exibir_historico_emails_instrucao',
                        post_id: post_id,
                    }, function(response){
                        // Insere resultado na div
                        $('#lista-envios').html(response);
                        $('#lista-envios').attr('data-post', post_id);

                        Swal.close();
                    });
                });
            });
        </script>
        <?php
    }

    function box_envios() {
        echo "<div id='lista-envios'>
            <h6 class='p-5 text-center'>Para visualizar o histórico de e-mails de instrução, busque o evento pelo nome ou ID.</h6>
        </div>";
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
            <h1>Histórico de E-mails de Instrução</h1>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    
                    <!-- Conteúdo principal -->
                    <div id="post-body-content">
                        <?php 
                        $this->register_metaboxes();
                        do_meta_boxes( 'historico-emails', 'normal', null ); 
                        ?>
                    </div>

                    <!-- Sidebar -->
                    <div id="postbox-container-1" class="postbox-container">
                        <?php do_meta_boxes( 'historico-emails', 'side', null ); ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($){
            postboxes.add_postbox_toggles('historico-emails'); // mesmo slug do submenu
        });
        </script>
        <?php
    }
}

new Historico_Emails;