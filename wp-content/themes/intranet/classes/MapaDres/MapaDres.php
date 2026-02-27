<?php

namespace Classes\MapaDres;

use Classes\TemplateHierarchy\ArchiveContato\ArchiveContato;

class MapaDres extends ArchiveContato
{
	public function __construct()
	{
		$this->init();
	}

	public function init(){
		$container_geral_tags = array('section', 'section');
		$container_geral_css = array('container', 'row');
		$this->abreContainer($container_geral_tags, $container_geral_css);

		$this->getTitulo();
		$this->htmlMapaDresMapa();
		$this->htmlMapaDresBotoes();

		$this->fechaContainer($container_geral_tags);
	}

	public function getTitulo(){
		echo '<article class="col-12">';
		echo "<h1 class='mb-5' id='mapa-dres'>DRE's — Diretorias Regionais de Educação</h1>";
		echo '<p>Selecione a DRE desejada e acesse suas informações de contato.</p>';
		echo '</article>';
	}

	public function htmlMapaDresBotoes(){
		?>
        <section class="col-12 col-md-4 todas-dres">
            <?php new MapaDresBotoes() ?>

        </section>
		<?php
	}

	public function htmlMapaDresMapa(){
		?>

        <section class="col-12 col-md-8 d-none d-sm-block">
            <?php new MapaDresMapa()?>
        </section>

		<?php
	}


}