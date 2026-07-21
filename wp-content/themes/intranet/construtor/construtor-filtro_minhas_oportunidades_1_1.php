<?php
$etapas_processo = Inscricao::get_etapas_processo();

?>
<div class="container">
    <div class="card filtro-minhas-oportunidades my-4">

        <div class="card-header">
            <i class="fa fa-filter mr-1" aria-hidden="true"></i>
            <strong><?php echo esc_html( strtoupper( get_sub_field( 'titulo' ) ) ); ?></strong>
        </div>

        <div class="card-body">
            <div class="form-group">
                <label for="filtro-titulo">
                    Busque pela Oportunidade:
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fa fa-search text-secondary"></i>
                        </span>
                    </div>

                    <input
                        type="text"
                        id="filtro-titulo"
                        class="form-control border-left-0"
                        placeholder="Digite a oportunidade. Ex.: COPED / DA - Divisão de Avaliação ..."
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">

                    <label for="filtro-tipo">Tipo de Oportunidade:</label>
                    <select
                        id="filtro-tipo"
                        class="form-control"
                        >
                        <option value="">Todas</option>
                        <option value="ste">STE - Serviços Técnico Educacionais</option>
                        <option value="sta">STA - Serviços Técnico Administrativos</option>
                    </select>

                </div>

                <div class="form-group col-md-6">

                    <label for="filtro-etapa">Etapa do Processo Seletivo:</label>
                    <select
                        id="filtro-etapa"
                        class="form-control"
                        >
                        <option value="">Todas</option>
                        <option value="inscrito">Inscrição Realizada</option>

                        <?php foreach ( $etapas_processo as $value => $etapa ) : ?>
                            <?php
                            if ( $value === 'inscrito' )
                                continue;
                            ?>
                            <option value="<?php echo esc_html( $value ); ?>">
                                <?php echo esc_html( $etapa['descricao'] ); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                </div>
            </div>

            <div class="text-right mt-2">
                <button type="button" class="btn btn-outline-secondary mr-2" id="btn-limpar-filtros">
                    <i class="fa fa-times mr-1" aria-hidden="true"></i>
                    Limpar Filtros
                </button>

                <button type="button" class="btn btn-primary" id="btn-filtrar">
                    <i class="fa fa-search mr-1" aria-hidden="true"></i>
                    Buscar
                </button>
            </div>
        </div>
    </div>
</div>