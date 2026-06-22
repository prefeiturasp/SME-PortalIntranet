<?php
// @codeCoverageIgnoreStart

wp_enqueue_script( 'oportunidade' );
wp_localize_script(
    'oportunidade',
    'oportunidade',
    [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce_inscricao' => wp_create_nonce( 'realizar_inscricao' ),
        'id' => get_the_ID(),
        'form_complementar' => get_field( 'formulario_complementar' )
    ]
);

$user = wp_get_current_user();
$parceira = get_field('parceira', 'user_'. $user->ID );
$email = $user->user_email;
if(function_exists('email_validate_patterns_in_monitored_domains_php7')){
    $resultado = email_validate_patterns_in_monitored_domains_php7($email);
    if($resultado && !$parceira){
        wp_redirect( home_url('index.php/perfil?atualizar=1') );
        exit;
    }
}

get_header();
the_post();

$pagina_principal = get_field( 'pagina_principal_oportunidades', 'options' );
$pagina_curriculo = get_field( 'pagina_meu_curriculo', 'options' );;
$pagina_minhas_oportunidades = get_field( 'pagina_minhas_oportunidades', 'options' );

$status_oportunidade = Oportunidade::get_status( get_the_ID() );
$usuario_inscrito = Inscricao::usuario_ja_inscrito( get_current_user_id(), get_the_ID() );
$curriculo = Inscricao::obter_curriculo_usuario( get_current_user_id() );

?>

<div class="container mt-5" id="single-oportunidade">

    <div class="row">
        <div class="col mb-4">
            <div class="card card-oportunidade border-0 shadow-sm">
                <div class="card-body p-4">

                    <?php if ( $status_oportunidade ) : ?>
                        <span class="badge-oportunidade <?php echo esc_html( $status_oportunidade['class'] ); ?> mb-3">
                            <?php echo esc_html( $status_oportunidade['label'] ); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( $usuario_inscrito ) : ?>
                        <span class="badge-oportunidade inscrito">
                            Inscrito
                        </span>
                    <?php endif;?>

                    <h1 class="titulo-oportunidade">
                        <?php echo esc_html( get_the_title() ); ?>
                    </h1>

                    <?php if ( $tipos_oportindade = get_field( 'tipo_oportunidade' ) ) : ?>
                        <div>
                            <?php foreach ( $tipos_oportindade as $tipo ) : ?>
                                <p class="subtitulo-oportunidade"><?php echo esc_html( $tipo['label'] ); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $inicio_inscricoes = get_field( 'inicio_inscricoes' );
                    $fim_inscricoes = get_field( 'ence_inscricoes' );

                    if ( $inicio_inscricoes && $fim_inscricoes ) :
                        ?>
                        <div class="info-bloco">
                            <h3><i class="fa fa-calendar-o" aria-hidden="true"></i> Período de Inscrições</h3>
                            <p><?php echo esc_html( "{$inicio_inscricoes} a {$fim_inscricoes}" ); ?></p>
                        </div>
                        <?php
                    endif;
                    ?>

                    <?php
                    if ( $eixo_id = get_field( 'eixo_atuacao' ) ) :
                        $eixo_atuacao = get_term_by( 'term_id', $eixo_id, 'eixos_atuacao' );
                        ?>
                        <div class="info-bloco">
                            <h3><i class="fa fa-database" aria-hidden="true"></i> Eixo de Atuação</h3>
                            <p><?php echo esc_html( $eixo_atuacao->name ); ?></p>
                        </div>
                        <?php
                    endif;
                    ?>

                    <?php
                    if ( $local_id = get_field( 'local_trabalho' ) ) :
                        $local_trabalho = get_term_by( 'term_id', $local_id, 'locais' );
                        $endereco_trabalho = !empty( $local_trabalho->description ) 
                            ? $local_trabalho->description
                            : get_field( 'endereco_trabalho' );
                        ?>
                        <div class="info-bloco">
                            <h3><i class="fa fa-map-marker" aria-hidden="true"></i> Local de Trabalho</h3>
                            <p><?php echo esc_html( $local_trabalho->name ); ?></p>
                            <?php if ( $endereco_trabalho ) : ?>
                                <em class="text-secondary"><?php echo esc_html( $endereco_trabalho ); ?></em>
                            <?php endif; ?>
                        </div>
                        <?php
                    endif;
                    ?>

                    <?php if ( $horario_trabalho = get_field( 'horario_trabalho' ) ) : ?>
                        <div class="info-bloco">
                            <h3><i class="fa fa-clock-o" aria-hidden="true"></i> Horário de Trabalho</h3>
                            <p><?php echo esc_html( $horario_trabalho ); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ( $periodo_atuacao = get_field( 'periodo_atuacao' ) ) : ?>
                        <div class="info-bloco">
                            <h3><i class="fa fa-calendar" aria-hidden="true"></i> Período de Atuação</h3>
                            <p><?php echo esc_html( $periodo_atuacao ); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ( $descricao_oportunidade = get_field( 'descricao' ) ) : ?>
                        <div class="info-bloco">
                            <h3>Descrição da Oportunidade</h3>
                            <?php echo wp_kses_post( $descricao_oportunidade ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $principais_atividades = get_field( 'ativi_desen' ) ) : ?>
                        <div class="info-bloco">
                            <h3>Principais atividades desenvolvidas</h3>
                            <?php echo wp_kses_post( $principais_atividades ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $perfil_requerido = get_field( 'perfil_requerido' ) ) : ?>
                        <div class="info-bloco">
                            <h3>Perfil requerido</h3>
                            <?php echo wp_kses_post( $perfil_requerido ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $info_complementar = get_field( 'info_comple' ) ) : ?>
                        <div class="info-bloco">
                            <h3>Informações complementares</h3>
                            <?php echo wp_kses_post( $info_complementar ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $form_complementar = get_field( 'formulario_complementar' ) ) : ?>
                        <div class="info-bloco">
                            <h3>Formulário complementar</h3>
                            <a href="<?php echo esc_url( $form_complementar ); ?>" target="_blank" rel="noopener noreferrer" class="font-italic">
                                Acesse o formulário complementar <i class="fa fa-external-link" aria-hidden="true"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="card my-4 card-informacoes">
                        <div class="card-body p-4">

                            <h2 class="titulo-secao">
                                Informações Importantes
                            </h2>

                            <div class="conteudo-informacoes">
                                <p><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> A designação fica condicionada à anuência da chefia imediata, cuja obtenção é de responsabilidade exclusiva do servidor.</p>

                                <p><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> Tipo de oportunidade:</p>
                                <ul>
                                    <li>Para prestação de Serviços Técnico Educacionais (STE): exclusiva para servidores efetivos do Quadro do Magistério – (Professor, Coordenador Pedagógico, Diretor de Escola e Supervisor Escolar)</li>
                                    <li>Para prestação de Serviços Técnico Administrativos (STA): exclusiva para servidores efetivos do Quadro de Apoio – (Auxiliar Técnicos de Educação).</li>
                                </ul>
                                <p><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> STA e STE pressupõe atuação em jornada de 40h relógio. Os proventos de origem serão mantidos sem acréscimos de gratificações, somente com o pagamento da ampliação de jornada para os servidores que seu cargo base não seja de 40h relógio (professores).</p>
                                <p><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> A comunicação sobre as próximas etapas será realizada, por e-mail, pelo setor responsável pela vaga.</p>
                                <p><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> <strong>Essa inscrição tem caráter de interesse, não implicando obrigação nem garantia de designação.</strong></p>
                            </div>

                        </div>
                    </div>

                    <?php if ( $status_oportunidade['value'] === 'aberta' ) : ?>

                        <?php if (  $usuario_inscrito  ) : ?>

                            <div class="card-informacoes inscrito">
                                <div class="card-body p-4">
                                    <strong><i class="fa fa-check-circle-o fa-lg mr-2" aria-hidden="true"></i> Você já está inscrito nessa oportunidade</strong>
                                    <div class="mt-3">
                                        <p class="text-secondary">
                                            Sua inscrição na oportunidade <b>"<?php echo esc_html( get_the_title() ); ?>"</b> já foi realizada com sucesso. 
                                            Você pode acompanhar o andamento da sua candidatura pelo e-mail informado ou acessando a área "Minhas Oportunidades", onde em breve você receberá atualizações sobre o processo.    
                                        </p>

                                        <a href="<?php the_permalink( $pagina_minhas_oportunidades->ID ); ?>" class="btn btn-primary mt-2">
                                            Ir para Minhas Oportunidades
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ( !$curriculo ) : ?>

                            <div class="alert alert-danger sem-curriculo">
                                <div class="card-body p-4">
                                    <strong><i class="fa fa-exclamation-circle fa-lg mr-2" aria-hidden="true"></i> Currículo não cadastrado</strong>
                                    <div class="mt-3">
                                        <p>
                                            Para se inscrever em uma oportunidade, é necessário cadastrar seu currículo. Preencha suas informações para prosseguir com a candidatura.  
                                        </p>

                                        <a href="<?php the_permalink( $pagina_curriculo->ID ); ?>" class="btn btn-danger mt-2">
                                            Cadastrar Meu Currículo
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ( $curriculo->status_curriculo !== 'finalizado' ) : ?>

                            <div class="alert alert-warning curriculo-incompleto">
                                <div class="card-body p-4">
                                    <strong><i class="fa fa-exclamation-circle fa-lg mr-2" aria-hidden="true"></i> Currículo incompleto</strong>
                                    <div class="mt-3">
                                        <p>
                                            Para se inscrever em uma oportunidade, é necessário completar o preenchimento do seu currículo. Atualize suas informações para prosseguir com a candidatura. 
                                        </p>

                                        <a href="<?php the_permalink( $pagina_curriculo->ID ); ?>" class="btn btn-warning mt-2">
                                            Completar Meu Currículo
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php else : ?>

                            <button class="btn btn-inscricao btn-block">
                                Inscrever-se nesta Oportunidade
                            </button>

                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( $status_oportunidade['value'] === 'breve' ) : ?>
                        <div class="alert alert-warning" role="alert">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            As inscrições para esta oportunidade serão abertas dia <strong><?php echo esc_html( get_field( 'inicio_inscricoes' ) ); ?></strong>.
                            Fique atento e acompanhe para não perder o prazo.
                        </div>
                    <?php endif; ?>

                    <?php if ( $status_oportunidade['value'] === 'encerrada' ) : ?>
                        <div class="card-informacoes">
                            <div class="card-body p-4">
                                <strong><i class="fa fa-exclamation-circle fa-lg text-secondary mr-2" aria-hidden="true"></i> Inscrições Encerradas</strong>
                                <div>
                                    <p class="text-secondary">
                                        O período de inscrição desta oportunidade foi encerrado. Confira outras oportunidades disponíveis.
                                    </p>

                                    <a href="<?php the_permalink( $pagina_principal->ID ); ?>" class="btn btn-outline-primary mt-2">
                                        Ver outras oportunidades
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-5">
                        <a href="<?php the_permalink( $pagina_principal->ID ); ?>" class="link-voltar">
                            <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Voltar ao Portal de Oportunidades
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Outras oportunidades -->
        <?php get_template_part( 'includes/oportunidades/template-parts/outras-oportunidades', null, [
            'titulo' => 'Outras Oportunidades Abertas',
            'url_pagina_principal' => get_the_permalink( $pagina_principal->ID ),
            'status' => 'abertas' // abertas | encerradas | em-breve
        ] ); ?>

    </div>
</div>

<?php

//contabiliza visualizações de noticias
setPostViews(get_the_ID());

get_footer();
// @codeCoverageIgnoreEnd