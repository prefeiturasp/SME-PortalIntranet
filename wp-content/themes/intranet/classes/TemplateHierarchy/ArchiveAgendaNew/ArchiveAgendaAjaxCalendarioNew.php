<?php

namespace Classes\TemplateHierarchy\ArchiveAgendaNew;


use Classes\Lib\Util;

class ArchiveAgendaAjaxCalendarioNew extends Util
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
			'post_type' => 'agendanew',
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
				<div class="agenda mb-4 agenda-new bbb">
					<?php
						$eventos = get_field('eventos_do_dia');
						//echo "<pre>";
						//print_r($eventos);
						//echo "</pre>";
					?>
					<?php foreach($eventos as $evento): ?>
						<div class="agenda mb-4 agenda-new abc">
							<div class="order_hri">
								<?php
									//converte campo hora por extenso para ordenar
									$hri = $evento['hora_evento'];
									echo $hri=date('His',$hri);
								?>
							</div>

							<div class="horario d-inline"><?= $evento['hora_evento']; ?><?= ' - ' . $evento['fim_evento']; ?></div> |
							<?php if( $evento['compromisso'] == 'outros' && $evento['nome_compromisso'] != '') :?>
								<div class="evento d-inline"><?= $evento['nome_compromisso'] ?></div>
							<?php else: ?>
								<div class="evento d-inline"><?= get_term( $evento['compromisso'] )->name; ?></div>
							<?php endif; ?>

							<?php if($evento['pauta_assunto'] != ''): ?>
								<div class="local"><strong>Descrição:</strong> <?= $evento['pauta_assunto']; ?></div>
							<?php endif; ?>

							<?php if( $evento['endereco_evento'] == 'outros' && $evento['digite_o_endereco_do_evento'] != '') :?>
								<div class="local"><strong>Local:</strong> <?= $evento['digite_o_endereco_do_evento'] ?></div>
							<?php else: ?>
								<div class="local"><strong>Local:</strong> <?= get_term( $evento['endereco_evento'] )->name; ?> - <?= get_term( $evento['endereco_evento'] )->description; ?></div>
							<?php endif; ?>

							<?php if($evento['participantes_evento'] != ''): ?>
								<div class="local"><strong>Participantes:</strong><br><?= $evento['participantes_evento']; ?></div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
					
					<?php /*

					<div class="order_hri">
						<?php
							//converte campo hora por extenso para ordenar
				 			$hri = $eventos[0]['hora_evento'];
				 			echo $hri=date('His',$hri);
						?>
					</div>

					<div class="horario d-inline"><?= $eventos[0]['hora_evento']; ?> - <?= $eventos[0]['fim_evento']; ?></div> |
					<div class="evento d-inline"><?= get_the_title()?> <?php echo $titleShow; ?></div>
					<div class="local"><?php 					
						if ($this->getCamposPersonalizados('pauta_assunto') !== null && $this->getCamposPersonalizados('pauta_assunto') !== ''){
					?>
						<div class="local"><strong>Descrição:</strong> <?= $this->getCamposPersonalizados('pauta_assunto') ?></div>
					<?php } ?></div>
					<div class="local"><?php 					
						if ($this->getCamposPersonalizados('endereco_do_evento') !== null && $this->getCamposPersonalizados('endereco_do_evento') !== ''){
					?>
						<div class="local"><strong>Local:</strong> <?= $this->getCamposPersonalizados('endereco_do_evento') ?></div>
					<?php } ?></div>
					<div class="local">
						
						<?php 					
						if ($this->getCamposPersonalizados('participantes_do_evento') !== null && $this->getCamposPersonalizados('participantes_do_evento') !== ''){
					?>
						<div class="local"><strong>Participantes:</strong><?= $this->getCamposPersonalizados('participantes_do_evento') ?></div>
					*/ ?>
					
					<?php //} 
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
			echo '<p class="agenda agenda-new"><strong>Não existem eventos cadastrados nesta data</strong></p>';
		endif;
		wp_reset_postdata();

	}

}

?>