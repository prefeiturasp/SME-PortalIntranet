<?php

namespace Classes\TemplateHierarchy\ArchiveAgenda;


use Classes\Lib\Util;

class ArchiveAgenda extends Util
{

	public function __construct()
	{
	    $container_calendario_tags = array('section', 'section');
	    $container_calendario_css = array('container mb-5', 'row');
	    $this->abreContainer($container_calendario_tags, $container_calendario_css);
		$this->montaHtmlCalendario();
		$this->insereDivRecebeData();
		$this->fechaContainer($container_calendario_tags);
	}

	public function montaHtmlCalendario(){
		?>
		<section class="col-lg-6 col-xs-12">
			
            <!--<div class="container-loading-agenda-secretario">
                <img src="<?/*= STM_URL*/?>/wp-content/uploads/2019/10/loading.gif" alt="Carregando Agenda do Secretário">
            </div>-->
			<section class="calendario-agenda-sec d-block"></section>
			<div class="legenda-agenda">
				<p><span class="legenda-dre"></span> Eventos em DREs e Unidades <span class="legenda-sme"></span> Eventos em Coordenadorias e SME</p>
			</div>
		</section>


		<?php
	}

	public function insereDivRecebeData(){
		?>
		<section class="col-lg-6 col-xs-12 bd-left">
			<h2 class="data_agenda mb-4 pb-2 border-bottom">Dia do Evento</h2>
			<section id="mostra_data"></section>
			<!-- Monta a lista ordenada por horário -->
			<section class="agenda-ordenada"></section>
		</section>
		<?php
	}
}