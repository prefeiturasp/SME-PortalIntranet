<?php
	$select_pages = get_sub_field('select_pages');
?>

<div class="container">
	<div class="row">
		<div class="col-sm-12" id="filtro-eventos">			

			<div class="nav nav-tabs d-flex resultados-busca" id="nav-tab" role="tablist">
				<?php
					if($select_pages && is_array($select_pages)){		
						foreach($select_pages as $pagina){

							$active = ((int) get_the_ID() === (int) $pagina['pagina']) ? 'active' : '';
							$selected = ((int) get_the_ID() === (int) $pagina['pagina']) ? 'true' : 'false';

							echo '<a 
									class="nav-link flex-fill ' . $active . '" 								
									href="' . get_the_permalink($pagina['pagina']) . '"
									>
									' . '<img src="' . $pagina['icone'] . '" alt="Inscrições abertas"> ' . get_the_title($pagina['pagina']) . '
								</a>';
						}
					}
				?>				
			</div>

		</div>
	</div>
</div>