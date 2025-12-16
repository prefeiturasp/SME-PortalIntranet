<?php
use Classes\ModelosDePaginas\PaginaAgendaSecretario\PaginaAgendaSecretario;
use Classes\TemplateHierarchy\ArchiveAgenda\ArchiveAgenda;
?>

<?php

function data_periodo($dataIni, $dataFin){
	$dataIni = implode('-', array_reverse(explode('/', $dataIni)));
	$dataFin = implode('-', array_reverse(explode('/', $dataFin)));

    $inicial = new DateTime( $dataIni );
    $final = new DateTime( $dataFin );
    $final = $final->modify( '+1 day' ); 

    $intervalo = new DateInterval('P1D');
    $periodo = new DatePeriod($inicial, $intervalo ,$final);

    return $periodo;
}

$datas = array();
$anos = array();
$eventos_por_dia = [];

$args = array(
    'post_type' => 'agenda',    
    'posts_per_page' => -1
);
$query = new \WP_Query( $args );

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
    $tipo = get_field('tipo_de_data');
    $tipoEvento = get_field('tipo_evento');

    if($tipo){
        $dataIni = get_field('data_do_evento');
	    $dataFin = get_field('data_evento_final');
        $periodo = data_periodo($dataIni, $dataFin);
        foreach ($periodo as $key => $value) {
            $datas[] = $value->format('d/m/Y');
            $eventos_por_dia[$value->format('d/m/Y')][] = $tipoEvento;
            $anos[] = $value->format('Y');     
        }
    } else {
        $datas[] = get_field('data_do_evento');
        $eventos_por_dia[get_field('data_do_evento')][] = $tipoEvento;

        $dataString = get_field('data_do_evento');
        $dataObjeto = \DateTime::createFromFormat('d/m/Y', $dataString);
        $ano = $dataObjeto->format('Y');

        $anos[] = $ano;
    }
    
endwhile;

endif;
wp_reset_postdata();


$marc = '[';
$i = 0;
foreach($eventos_por_dia as $data => $categoria){
    $data = str_replace('/', '\/', $data);
    $qtd = count($categoria);
    if($i == 0){
        if($qtd == 1){
            $marc .= '[&quot;' . $data . '&quot;, &quot;' . $categoria[0] . '&quot;]';
        } else {
            $marc .= '[&quot;' . $data . '&quot;, &quot;multi&quot;]';
        }
    } else {
        if($qtd == 1){
            $marc .= ',[&quot;' . $data . '&quot;, &quot;' . $categoria[0] . '&quot;]';
        } else {
            $marc .= ',[&quot;' . $data . '&quot;, &quot;multi&quot;]';
        }
    }
    $i++;
}

$marc .= ']';

$anos = array_unique($anos);
$menorAno = min($anos);
$maiorAno = max($anos);

?>

<input type="hidden" name="array_datas_agenda" id="array_datas_agenda" class="aqui" value="<?= $marc; ?>">
<input type="hidden" name="array_anos" id="array_anos" value="<?= $menorAno . '-' . $maiorAno; ?>">
<style>
    .agenda{
        display: block;
    }

    .agenda.agenda-new{
        display: none;
    }
</style>

<div class="container">
    <div class="calendario-construtor agenda-sme">
        <div class="titulo-agenda">
            <h3>Agenda de Eventos SME</h3>
            <a href="<?= get_home_url();?>/wp-admin/edit.php?post_type=agenda" class="btn btn-edit-agenda" target="_blank">Adicionar/Editar Evento</a>
        </div>
        
        <?php $pagina_agenda_secretario = new ArchiveAgenda(); ?>
    </div>
</div>