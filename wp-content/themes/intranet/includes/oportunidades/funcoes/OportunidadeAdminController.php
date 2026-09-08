<?php

class OportunidadeAdminController {

    public function __construct() {

        add_action( 'load-post.php', [$this, 'inicializar_regras_edicao'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
        add_action( 'admin_bar_menu', [ $this, 'personalizar_barra_admin' ], 999 );
        
        /*
        * Fluxo de exclusão e auditoria de exclusão de oportunidades.
        */
        add_filter( 'pre_trash_post', [$this, 'impedir_exclusao_com_candidatos_inscritos'], 10, 2 );
        add_filter( 'manage_oportunidade_posts_columns', [$this, 'adicionar_colunas_lixeira'] );

        add_action( 'wp_trash_post', [$this, 'registrar_exclusao'] );
        add_action( 'manage_oportunidade_posts_custom_column', [$this, 'renderizar_colunas_lixeira'], 10, 2 );
        add_action( 'admin_notices', [$this, 'exibir_aviso_exclusao_bloqueada'] );

        /**
         * Taxonomias
         */
        add_filter( 'pre_insert_term', [$this, 'validar_taxonomias'], 10, 2 );
        add_filter( 'views_edit-oportunidade', [$this, 'alterar_labels_filtros_oportunidades'] );

        /**
         * Post type
         */
        add_filter( 'post_row_actions', [$this, 'ordenar_acoes_oportunidade' ], 10, 2 );

        add_action( 'pre_get_posts', [$this, 'filtrar_oportunidades_encerradas'] );
        add_action( 'pre_get_posts', [$this, 'filtrar_oportunidades_listagem_padrao'] );
        add_action( 'admin_menu', [ $this, 'remover_menu_painel' ] );
        add_action( 'admin_init', [ $this, 'redirecionar_painel' ] );
        add_action( 'edit_form_before_permalink', [ $this, 'exibir_titulo_oportunidade' ] );

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

    public function enqueue_admin_styles() {

        $screen = get_current_screen();

        if ( !$screen ) {
            return;
        }

        if ( $screen->post_type !== 'oportunidade' ) {
            return;
        }

        wp_enqueue_style( 'admin-oportunidades' );
        wp_enqueue_script( 'admin-oportunidades' );
    }

    public function validar_taxonomias( $term, $taxonomy ) {

        $taxonomias = ['locais', 'eixos_atuacao'];
        $mensagens_validação = [
            'locais' => [
                'nome' => 'Sigla e descrição completa da Coord/Div/DREs',
                'endereco' => 'Endereço da Unidade de Exercício'
            ],
            'eixos_atuacao' => [
                'nome' => 'Título do Eixo de Atuação',
            ]
        ];
        
        if ( !in_array( $taxonomy, $taxonomias ) ) {
            return $term;
        }

        if ( empty( trim( $term ) ) ) {
            return new WP_Error( 'nome_obrigatorio', "O campo {$mensagens_validação[$taxonomy]['nome']} é obrigatório." );
        }

        if ( $taxonomy === 'locais' ) {
            if ( isset( $_POST['description'] ) && empty( trim( wp_unslash( $_POST['description'] ) ) ) ) {
                return new WP_Error( 'descricao_obrigatoria', "O campo {$mensagens_validação[$taxonomy]['endereco']} é obrigatório." );
            }
        }

        return $term;
    }

    //Modifica a nomenclatura e a ordenação padrão dos filtros nas listagens das "Oportunidades".
    public function alterar_labels_filtros_oportunidades( $views ) {

        if ( isset( $views['mine'] ) ) {
            $views['mine'] = str_replace( 'Meus', 'Editando', $views['mine'] );
        }

        if ( isset( $views['pending'] ) ) {
            $views['pending'] = str_replace( 'Pendentes', 'Aguardando Validação', $views['pending'] );
        }

        $views['encerradas'] = $this->criar_view_encerradas();

        $ordem = [
            'all',
            'mine',
            'pending',
            'publish',
            'encerradas',
            'draft',
            'trash',
        ];

        $views_ordenadas = [];

        foreach ( $ordem as $key ) {
            if ( isset( $views[ $key ] ) ) {
                $views_ordenadas[ $key ] = $views[ $key ];
            }
        }

        // Remove os contadores de todos os filtros.
        foreach ( $views_ordenadas as $key => $view ) {
            $views_ordenadas[ $key ] = preg_replace( '/\s*<span class="count">.*?<\/span>/', '', $view );
        }

        return $views_ordenadas;
    }

    //Adiciona o link do filtro de "Encerradas" na listagem de Oportunidades
    private function criar_view_encerradas() {

        $url = add_query_arg([
                'post_type' => 'oportunidade',
                'oportunidade_view' => 'encerradas'
            ],
            admin_url( 'edit.php' )
        );

        $menu_classe = isset( $_GET['oportunidade_view'] ) && $_GET['oportunidade_view'] === 'encerradas'
            ? 'current'
            : '';

        return sprintf(
            '<a href="%s" class="%s">Encerradas</a>',
            esc_url( $url ),
            $menu_classe
        );
    }

    //Monta o filtro "Encerradas" na listagem de "Oportunidades"
    public function filtrar_oportunidades_encerradas( $query ) {

        if ( !is_admin() || !$query->is_main_query() ) {
            return;
        }

        if ( $query->get( 'post_type' ) !== 'oportunidade' ) {
            return;
        }

        if ( !isset( $_GET['oportunidade_view'] ) || $_GET['oportunidade_view'] !== 'encerradas' ) {
            return;
        }

        $data_atual = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
        $meta_query = $query->get( 'meta_query' );
        
        $query->set( 'post_status', ['pending','publish'] );

        if ( !is_array( $meta_query ) ) {
            $meta_query = [];
        }

        $meta_query[] = [
            'key'     => 'ence_inscricoes',
            'value'   => $data_atual,
            'compare' => '<',
            'type'    => 'NUMERIC',
        ];

        $query->set( 'meta_query', $meta_query );
    }

    //Modifica a listagem padrão para remover oportunidades "Encerradas" da listagem.
    public function filtrar_oportunidades_listagem_padrao( $query ) {

        if ( !is_admin() || !$query->is_main_query() ) {
            return;
        }

        if ( $query->get( 'post_type' ) !== 'oportunidade' ) {
            return;
        }

        // Não interfere nas views/filtros personalizados.
        if ( !empty( $_GET['oportunidade_view'] ) ) {
            return;
        }

        if ( !empty( $query->get( 'post_status' ) ) ) {
            return;
        }

        $query->set( 'post_status', ['pending','publish'] );

        $data_atual = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
        $meta_query = $query->get( 'meta_query' );

        if ( !is_array( $meta_query ) ) {
            $meta_query = [];
        }

        $meta_query[] = [
            'relation' => 'OR',
            [
                'key'     => 'ence_inscricoes',
                'value'   => $data_atual,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
            [
                'key'     => 'ence_inscricoes',
                'compare' => 'NOT EXISTS',
            ],
        ];

        $query->set( 'meta_query', $meta_query );
    }

    //Modifica a ordenação padrão das ações exibidas em cada item da listagem de "Oportunidades".
    public function ordenar_acoes_oportunidade( $actions, $post ) {

        if ( $post->post_type !== 'oportunidade' ) {
            return $actions;
        }

        $ordem = [
            'view',
            'edit',
            'inline hide-if-no-js',
            'trash'
        ];

        $acoes_ordenadas = [];

        foreach ( $ordem as $acao ) {
            if ( isset( $actions[ $acao ] ) ) {
                $acoes_ordenadas[ $acao ] = $actions[ $acao ];
            }
        }

        // Mantém qualquer ação adicional que não precise de uma ordem especifica.
        foreach ( $actions as $key => $action ) {
            if ( !isset( $acoes_ordenadas[ $key ] ) ) {
                $acoes_ordenadas[ $key ] = $action;
            }
        }

        return $acoes_ordenadas;
    }

    //Remove o menu painel dos perfis de usuários do Portal de Oportunidades
    public function remover_menu_painel() {

        if ( !$this->check_permissoes_usuario_logado( ['admin_portal', 'gestor_unidade'] ) ) {
            return;
        }

        remove_menu_page( 'index.php' );
    }

    //Redireciona usuários do Portal de Oportunidades diretamente para o menu de "Gestão de Oportunidades"
    public function redirecionar_painel() {

        if ( !$this->check_permissoes_usuario_logado( ['admin_portal', 'gestor_unidade'] ) ) {
            return;
        }

        global $pagenow;

        if ( $pagenow !== 'index.php' ) {
            return;
        }

        wp_safe_redirect( admin_url( 'edit.php?post_type=oportunidade' ) );
        exit;
    }

    //Exibe o título da oportunidade na tela de edição
    public function exibir_titulo_oportunidade( $post ) {

        if ( $post->post_type !== 'oportunidade' ) {
            return;
        }

        if ( !$post->post_title ) {
            return;
        }

        ?>
        <div class="campo-titulo-oportunidade">
            <span>Oportunidade:</span>
            <strong><?php echo esc_html( $post->post_title ); ?></strong>
        </div>
        <?php
    }

    //Personalizar as opções disponíveis na "Admin bar" para usuários com perfil do Portal de Oportunidades
    public function personalizar_barra_admin( $wp_admin_bar ) {

        $usuario = wp_get_current_user();

        if ( !in_array( 'admin_portal', $usuario->roles, true ) && !in_array( 'gestor_unidade', $usuario->roles, true ) ) {
            return;
        }

        $itens = [
            'wp-logo',
            'updates',
            'comments',
            'new-content',
            'customize',
        ];

        foreach ( $itens as $item ) {
            $wp_admin_bar->remove_node( $item );
        }
    }

}

new OportunidadeAdminController();