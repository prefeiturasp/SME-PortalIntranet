<?php

namespace Classes\TemplateHierarchy\ArchiveAgenda;


use Classes\Lib\Util;

class ArchiveAgendaAjaxCalendario extends Util
{

    public function __construct()
	{
		$this->page_id = get_the_ID();
	}

	public function montaHtmlListaEventos(){

		if ($_POST['data_pt_br']) {

			$data_recebida_ao_clicar = $_POST['data_pt_br'];

			if ($data_recebida_ao_clicar) {
				$this->montaQueryAgenda($data_recebida_ao_clicar);
			}
		}

	}

	public function montaQueryAgenda($data_recebida_ao_clicar){
		$args = array(
			'post_type' => 'agenda',
			'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'data_do_evento',
					'value' => $data_recebida_ao_clicar,
					'compare' => '=',
				),
				array(
					'relation' => 'AND',
					array(
						'key' => 'data_do_evento',
						'value' => $data_recebida_ao_clicar,
						'type' => 'date',
						'compare' => '<='
					),
					array(
						'key' => 'data_evento_final',
						'value' => $data_recebida_ao_clicar,
						'type' => 'date',
						'compare' => '>='
					),
				),			
			)
			
		);
		$query = new \WP_Query( $args );

		if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
			?>
			<article class="col-lg-12 col-xs-12">
				<div class="agenda">
					<?php
						$eventos = get_field('eventos_do_dia');						
					?>
					<?php foreach($eventos as $evento): ?>
						<div class="agenda mb-4">
							<div class="order_hri">
								<?php
									//converte campo hora por extenso para ordenar
									$hri = $evento['hora_evento'];
									echo $hri=date('His',$hri);
								?>
							</div>

							<?php 
								$categoria = $this->getCamposPersonalizados('tipo_evento');
								if($categoria && $categoria == 'dre'){
									echo '<div class="legenda-dre"></div>';
								} elseif($categoria && $categoria == 'sme'){
									echo '<div class="legenda-sme"></div>';
								}
							?>

							<div class="horario d-inline">
								<?php 
									if($evento['hora_evento']){
										echo $evento['hora_evento'];
									}
								?>
								<?php
									if($evento['fim_evento']){
										echo ' - ' . $evento['fim_evento'];
									}
								?>
							</div> |
							<?php if( $evento['compromisso_sme'] == 'outros' && $evento['nome_compromisso'] != '') :?>
								<div class="evento d-inline"><?= $evento['nome_compromisso'] ?></div>
							<?php else: ?>
								<div class="evento d-inline"><?= get_term( $evento['compromisso_sme'] )->name; ?></div>
							<?php endif; ?>

							<?php if($evento['pauta_assunto'] != ''): ?>
								<div class="local"><strong>Descrição:</strong> <?= $evento['pauta_assunto']; ?></div>
							<?php endif; ?>
							
							<?php if( $evento['endereco_evento_sme'] == 'outros' && $evento['digite_o_endereco_do_evento'] != '') :?>
								<div class="local"><strong>Local:</strong> <?= $evento['digite_o_endereco_do_evento'] ?></div>
							<?php elseif($evento['endereco_evento_sme'] != '' && $evento['endereco_evento_sme'] != 'outros'): ?>
								<div class="local"><strong>Local:</strong> <?= get_term( $evento['endereco_evento_sme'] )->name; ?> - <?= get_term( $evento['endereco_evento_sme'] )->description; ?></div>
							<?php endif; ?>

							<?php if($evento['participantes_evento'] != ''): ?>
								<div class="local"><strong>Participantes:</strong><br><?= $evento['participantes_evento']; ?></div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>					
					
					<?php 
						echo'<script>
						//limpa div a cada click
						jQuery(".agenda-ordenada").html("");
						//ordena por hora
						jQuery(".agenda").sort(function(a, b) {

						  if (a.textContent < b.textContent) {
							return -1;
						  } else {
							return 1;
						  }
						}).appendTo(".agenda-ordenada");
						//oculta campo hora
						jQuery(".order_hri").hide();
						</script>';
						?></div>
				</div>
				
			</article>
		<?php

		endwhile;
		else:
			echo '<p class="agenda"><strong>Não existem eventos cadastrados nesta data</strong></p>';
		endif;
		wp_reset_postdata();

	}

}

?>