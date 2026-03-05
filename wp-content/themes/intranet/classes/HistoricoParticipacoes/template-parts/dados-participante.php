<?php
extract( $args );

$perfil = 'ESTAGIÁRIO';

if ( $dados_participante->user_id && $dados_participante->user_id > 0 ) {

    $tipo = get_user_meta( $dados_participante->user_id, 'parceira', true );
    $perfil = $tipo == 1 ? 'PARCEIRO' : 'SERVIDOR';
} 
?>

<div id='informacoes-participante'>
    <div class="row mt-4 mb-3">
        <div class="col col-md-4">
            <div class="col-item">
                <strong>Nome Completo: </strong>
                <span><?php echo esc_html( $dados_participante->nome_completo ); ?></span>
            </div>
            <div class="col-item">
                <strong>CPF: </strong>
                <span class="cpf"><?php echo esc_html( $dados_participante->cpf ); ?></span>
            </div>
            <div class="col-item">
                <strong>E-mail principal: </strong>
                <span><?php echo esc_html( $dados_participante->email_institucional ?: '-' ); ?></span>
            </div>
            <div class="col-item">
                <strong>E-mail secundário: </strong>
                <span><?php echo esc_html( $dados_participante->email_secundario ?: '-' ); ?></span>
            </div>
        </div>
        <div class="col col-md-4 border-right border-left">
            <div class="col-item">
                <strong>Telefone Celular: </strong>
                <span><?php echo esc_html( $dados_participante->celular ?? '-' ); ?></span>
            </div>
            <div class="col-item">
                <strong>Telefone Comercial: </strong>
                <span><?php echo esc_html( $dados_participante->telefone_comercial ?: '-' ); ?>
            </div>
            <div class="col-item">
                <strong>Perfil: </strong>
                <span><?php echo esc_html( $perfil ); ?></span>
            </div>
        </div>
        <div class="col col-md-4">
            <div class="col-item">
                <strong>DRE: </strong>
                <span><?php echo esc_html( $dados_participante->dre ?: '-' ); ?></span>
            </div>
            <div class="col-item">
                <strong>Cargo atual: </strong>
                <span><?php echo esc_html( $dados_participante->cargo_principal ?: '-' ); ?></span>
            </div>
            <div class="col-item">
                <strong>Escola/Setor: </strong>
                <span><?php echo esc_html( $dados_participante->unidade_setor ?: '-' ); ?></span>
            </div>
        </div>
    </div>
    <?php
    if ( isset( $sancao_ativa ) && !empty( $sancao_ativa ) ) :
        $data_formatada = date( 'd/m/Y', strtotime( $sancao_ativa['data_validade'] ) );
        ?>
        <div class="row mt-2 pt-2 border-top alerta-sancao">
            <div class="col">
                <i class="fa fa-minus-circle fa-lg text-danger" aria-hidden="true"></i>
                <strong>
                    Atenção!
                    Você está temporariamente impedido de se inscrever em novas oportunidades, devido à ausência em uma participação anterior.
                    Você poderá realizar novas inscrições a partir de <?php echo esc_html( $data_formatada ); ?>.
                </strong>
            </div>
        </div>
        <?php
    endif;
    ?>
</div>