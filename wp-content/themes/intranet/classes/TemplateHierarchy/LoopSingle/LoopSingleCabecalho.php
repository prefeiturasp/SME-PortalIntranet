<?php

namespace Classes\TemplateHierarchy\LoopSingle;

use Classes\TemplateHierarchy\Search\SearchFormSingle;

class LoopSingleCabecalho extends LoopSingle
{

	public function __construct()
	{
		$this->cabecalhoDetalheNoticia();
	}

	public function cabecalhoDetalheNoticia(){
		$colunas = get_field('colunas', 'options') ?: 4;
		$cards = get_field( 'cards', 'options' );
		
		if($cards){
			?>
				<div class="bg-cards py-4 w-100">
					<div class="container">
						<div class="row">

							<?php foreach ($cards as $card) : ?>

								<div class="col-sm-<?= $colunas ?>">
									<a href="<?= $card['link']; ?>">
										<div class="card">
											<img class="card-img-top" src="<?= $card['imagem']['url']; ?>" alt="<?= $card['image']['alt']; ?>">
											<div class="card-body">
												<p class="card-title"><?= $card['titulo']; ?></p>
											</div>
										</div>
									</a>
								</div>

							<?php endforeach; ?>

						</div>
					</div>
				</div>

			<?php
		}
        
	}
}