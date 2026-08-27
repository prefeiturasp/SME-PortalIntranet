<?php

class OportunidadeAdminController {

    public function __construct() {

        add_action( 'load-post.php', [$this, 'inicializar_regras_edicao'] );

        /*
        * Fluxo de exclusão e auditoria de exclusão de oportunidades.
        */
        add_filter( 'pre_trash_post', [$this, 'impedir_exclusao_com_candidatos_inscritos'], 10, 2 );
        add_filter( 'manage_oportunidade_posts_columns', [$this, 'adicionar_colunas_lixeira'] );

        add_action( 'wp_trash_post', [$this, 'registrar_exclusao'] );
        add_action( 'manage_oportunidade_posts_custom_column', [$this, 'renderizar_colunas_lixeira'], 10, 2 );
        add_action( 'admin_notices', [$this, 'exibir_aviso_exclusao_bloqueada'] );
    }

    /**
    * Inicializa as regras específicas da edição de uma oportunidade.
    */
    public function inicializar_regras_edicao() {

        $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

        if ( !$post_id ) {
            return;
        }

        if ( get_post_type( $post_id ) !== 'oportunidade' ) {
            return;
        }

        if ( !$this->deve_restringir_edicao( $post_id ) ) {
            return;
        }

        add_filter( 'acf/prepare_field', [$this, 'bloquear_campos_edicao'] );
    }

    /**
     * Bloqueia os campos da oportunidade quando
     * o Gestor estiver editando uma oportunidade que possui inscrições.
     */
    public function bloquear_campos_edicao( $field ) {

        $tipos_ignorados = [
            'accordion',
            'message',
        ];

        if ( in_array( $field['type'], $tipos_ignorados, true ) ) {
            return $field;
        }

        /*
         * Mantém o campo de encerramento das incrições desbloqueado.
         */
        if ( $field['_name'] === 'ence_inscricoes' ) {
            return $field;
        }

        $field['wrapper']['class'] = isset( $field['wrapper']['class'] )
            ? $field['wrapper']['class'] . ' campo-admin-bloqueado'
            : 'campo-admin-bloqueado';

        return $field;
    }

    /**
     * Verifica se o usuário possui algum dos perfis informados.
     */
    private function check_permissoes_usuario_logado( array $perfis ) {

        $usuario_logado = wp_get_current_user();

        if ( !$usuario_logado->exists() ) {
            return false;
        }

        return !empty( array_intersect( $perfis, (array) $usuario_logado->roles ) );
    }

    private function deve_restringir_edicao( int $post_id ) {

        if ( $this->check_permissoes_usuario_logado( ['admin_portal', 'administrator'] ) ) {
            return false;
        }

        if ( !$this->possui_candidatos( $post_id ) ) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se a oportunidade possui candidatos inscritos.
     */
    private function possui_candidatos( int $post_id ) {

        $inscricoes = Oportunidade::get_inscricoes( $post_id );

        return !empty($inscricoes);
    }

    public function impedir_exclusao_com_candidatos_inscritos( $trash, $post ) {

        if ( $post->post_type !== 'oportunidade' ) {
            return $trash;
        }

        if ( !$this->possui_candidatos( $post->ID ) ) {
            return $trash;
        }

        $referer = wp_get_referer();

        if ( !$referer ) {
            $referer = admin_url( 'edit.php?post_type=oportunidade' );
        }

        $url = add_query_arg( 'exclusao_bloqueada', 1, $referer );

        wp_safe_redirect( $url );
        exit;
    }

    public function exibir_aviso_exclusao_bloqueada() {

        if ( empty( $_GET['exclusao_bloqueada'] ) ) {
            return;
        }

        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <strong>Esta oportunidade possui candidatos inscritos e não pode ser excluída.</strong>
            </p>
        </div>
        <?php
    }

    /**
     * Registra o usuário e a data em que a oportunidade foi enviada para a lixeira.
     */
    public function registrar_exclusao( $post_id ) {

        if ( get_post_type( $post_id ) !== 'oportunidade' ) {
            return;
        }

        $usuario_id = get_current_user_id();

        if ( !$usuario_id ) {
            return;
        }

        $agora = obter_data_com_timezone( 'Y-m-d H:i:s', 'America/Sao_Paulo' );

        update_post_meta( $post_id, '_excluido_por', $usuario_id );
        update_post_meta( $post_id, '_excluido_em', $agora );
    }

    /**
     * Adiciona as colunas de exclusão somente na lixeira.
     */
    public function adicionar_colunas_lixeira( $columns ) {

        if ( !isset( $_GET['post_status'] ) || sanitize_key( $_GET['post_status'] ) !== 'trash' ) {
            return $columns;
        }

        $columns['excluido_por'] = 'Excluído por';
        $columns['excluido_em']  = 'Excluído em';

        return $columns;
    }

    /**
     * Exibe o conteúdo das colunas de exclusão.
     */
    public function renderizar_colunas_lixeira( $column, $post_id ) {

        if ( !isset( $_GET['post_status'] ) || sanitize_key( $_GET['post_status'] ) !== 'trash' ) {
            return;
        }

        if ( $column === 'excluido_por' ) {

            $usuario_id = get_post_meta( $post_id, '_excluido_por', true );

            if ( !$usuario_id ) {
                echo '—';
                return;
            }

            $usuario = get_userdata( $usuario_id );

            echo $usuario ? esc_html( $usuario->display_name ) : '-';

            return;
        }

        if ( $column === 'excluido_em' ) {

            $excluido_em =  get_post_meta( $post_id, '_excluido_em', true );

            if ( !$excluido_em ) {
                echo '—';
                return;
            }

            $excluido_em = DateTime::createFromFormat( 'Y-m-d H:i:s', $excluido_em )->format('d/m/Y H:i');

            echo $excluido_em;
        }
    }
}

new OportunidadeAdminController();