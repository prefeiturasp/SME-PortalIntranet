<?php

wp_enqueue_style('select2-css');        
wp_enqueue_script('select2-js');

$filtros = [];
$tax_filtros = [
    'coordenadorias',
    'locais',
    'eixos_atuacao'
];

foreach ( $tax_filtros as $tax ) {
    $filtros[$tax] = get_terms([
        'taxonomy' => $tax,
        'hide_empty' => false
    ]);
}

$titulo = get_sub_field( 'titulo' );

?>
<div class="container">
    <div class="row">
        <div class="col-sm-12 my-3" id="filtro-eventos">
            <?php if( $titulo ) : ?>
                <div class="title-form mb-3">
                    <h2><?php echo esc_html( $titulo ); ?></h2>
                </div>
            <?php endif; ?>
                
            <div class="tab-content" id="nav-tabContent">

                <div class="tab-pane" id="sort-ativos" role="tabpanel">
                    <form action="<?php echo esc_url( get_the_permalink() ); ?>" method="get" class="bg-white form-filtro-oportunidades">
                
                        <div class="form-row mb-2">
                            <div class="col-md-2 mb-2">
                                <label for="tipo-oportunidade" class="form-label">Tipo de Oportunidade</label>

                                <select class="form-control select-local" id="tipo-oportunidade" name="tipo-oportunidade">
                                    <option value="">Todos</option>
                                    <option value="ste" <?php selected( $_GET['tipo-oportunidade'], 'ste' ); ?>>
                                        STE - Serviços Técnicos Educacionais
                                    </option>
                                    <option value="sta" <?php selected( $_GET['tipo-oportunidade'], 'sta' ); ?>>
                                        STA - Serviços Técnicos Administrativos
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label for="setor" class="form-label">Setor</label>
                                <select class="form-control" id="setor" name="setor">
                                    <option value="">Todos</option>
                                    <?php if ( isset( $filtros['coordenadorias'] ) && !empty( $filtros['coordenadorias'] ) ) : ?>
                                        <?php
                                        foreach ( $filtros['coordenadorias'] as $setor ) :
                                            $label = $setor->description ? "{$setor->name} - {$setor->description}" : $setor->name;
                                            ?>
                                            <option 
                                                value="<?php echo esc_attr( $setor->term_id ); ?>" 
                                                <?php selected( $_GET['setor'], $setor->term_id ); ?>
                                                >
                                                <?php echo esc_html( $label ); ?>
                                            </option>
                                            <?php
                                        endforeach;
                                        ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label for="local" class="form-label">Local de Trabalho</label>
                                <select class="form-control select-tipo-evento" id="local" name="local">
                                    <option value="">Todos</option>
                                    <?php if ( isset( $filtros['locais'] ) && !empty( $filtros['locais'] ) ) : ?>
                                        <?php foreach ( $filtros['locais'] as $local ) : ?>
                                            <option 
                                                value="<?php echo esc_attr( $local->term_id ); ?>" 
                                                <?php selected( $_GET['local'], $local->term_id ); ?>
                                                >
                                                <?php echo esc_html( $local->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="eixo" class="form-label">Eixo de Atuação</label>
                                <select class="form-control select-tipo-evento" id="eixo" name="eixo">
                                    <option value="">Todos</option>
                                    <?php if ( isset( $filtros['eixos_atuacao'] ) && !empty( $filtros['eixos_atuacao'] ) ) : ?>
                                        <?php foreach ( $filtros['eixos_atuacao'] as $eixo ) : ?>
                                            <option 
                                                value="<?php echo esc_attr( $eixo->term_id ); ?>" 
                                                <?php selected( $_GET['eixo'], $eixo->term_id ); ?>
                                                >
                                                <?php echo esc_html( $eixo->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="situacao" class="form-label">Situação de Inscrição</label>

                                <select class="form-control select-local" id="situacao" name="situacao">
                                    <option value="">Todas</option>
                                    <option value="abertas" <?php selected( $_GET['situacao'], 'abertas' ); ?>>
                                        Inscrições Abertas
                                    </option>
                                    <option value="encerradas" <?php selected( $_GET['situacao'], 'encerradas' ); ?>>
                                        Inscrições Encerradas
                                    </option>
                                    <option value="em-breve" <?php selected( $_GET['situacao'], 'em-breve' ); ?>>
                                        Inscrições em Breve
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row"> 
                            <div class="col-12 d-flex align-items-end justify-content-end my-2 form-buttons">
                                <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="btn mr-2">Limpar Filtros</a>
                                <button type="submit" class="btn btn-primary" name="acao" value="filtrar">
                                    <i class="fa fa-search" aria-hidden="true"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>  
        </div>  
    </div>
</div>

<script>
    jQuery(function ($) {

        $('#setor').select2({
            placeholder: "Todos",
            allowClear: true,
            width: '100%',
            language: {
                noResults: () => "Nenhum setor encontrado.",
                searching: () => "Buscando…",

            }
        });

        $('#local').select2({
            placeholder: "Todos",
            allowClear: true,
            width: '100%',
            language: {
                noResults: () => "Nenhum local encontrado.",
                searching: () => "Buscando…",
            }
        });

        $('#eixo').select2({
            placeholder: "Todos",
            allowClear: true,
            width: '100%',
            language: {
                noResults: () => "Nenhum eixo de atuação encontrado.",
                searching: () => "Buscando…",
            }
        });
    }); 
</script>