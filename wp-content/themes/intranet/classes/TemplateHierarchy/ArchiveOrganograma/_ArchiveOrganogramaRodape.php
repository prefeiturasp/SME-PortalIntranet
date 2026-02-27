<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma;


class ArchiveOrganogramaRodape
{
	public function __construct()
	{
		$this->montaHtmlRodape();
	}

	public function montaHtmlRodape(){
		?>
		<a class="btn btn-primary btn-lg btn-block rodape-bt-unidades-escolares mt-5" href="<?= STM_URL ?>/escolaaberta">Unidades Escolares</a>

		<p class="mt-5 mb-4 text-center"><strong><a class="rodape-links" href="<?= STM_URL ?>/wp-content/uploads/2019/10/organograma.pdf">Download do organograma em PDF</a></strong></p>

		<p class="text-center"><strong><a class="rodape-links" href="<?= STM_URL.'/contato/'?>">Ver telefones e emails de contato</a></strong></p>
		<?php
	}



}