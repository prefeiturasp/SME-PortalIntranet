<?php

namespace Classes\TemplateHierarchy\ArchiveAgendaNew;


use Classes\Lib\Util;

class ArchiveAgendaNew extends Util
{

	public function __construct()
	{
	    $container_calendario_tags = array('section', 'section');
	    $container_calendario_css = array('container', 'row');
	    $this->abreContainer($container_calendario_tags, $container_calendario_css);
		$this->montaHtmlCalendario();
		$this->insereDivRecebeData();
		$this->fechaContainer($container_calendario_tags);
	}

	public function montaHtmlCalendario(){
		?>
		<section class="col-lg-6 col-xs-12">			
			<section class="calendario-agenda-sec d-block"></section>
		</section>


		<?php
	}

	public function insereDivRecebeData(){
		?>
		<section class="col-lg-6 col-xs-12 agenda-lista">
			<h2 class="data_agenda pb-2">Dia do Evento</h2>
			<section id="mostra_data"></section>
			<!-- Monta a lista ordenada por horário -->
			<section class="agenda-ordenada aaa"></section>
		</section>
		<?php
	}
}