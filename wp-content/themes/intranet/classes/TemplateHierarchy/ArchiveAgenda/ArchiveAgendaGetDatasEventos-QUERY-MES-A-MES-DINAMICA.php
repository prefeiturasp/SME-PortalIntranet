<?php

namespace Classes\TemplateHierarchy\ArchiveAgenda;


class ArchiveAgendaGetDatasEventos
{
	const CPTAGENDA = 'agenda';
	private $args_ids;
	private $query_ids;
	private $array_ids;
	private $array_datas;
	private $ano_e_mes;

	public function __construct()
	{

	}

	public function recebeDadosAjax(){
		$this->ano_e_mes = $_POST['ano_mes'];

		$this->imprimeDadosAjax($this->ano_e_mes);

	}

	public function imprimeDadosAjax($ano_mes)
	{
		?>
        <!-- Este comentário é necessário para a Função montaQueryMesAtual em Ajax Funcionar Corretamente -->
		<?php


		$this->args_ids = array(
			'post_type' => 'agenda',
			'post_status' => 'publish',
			'posts_per_page' => -1,

			'meta_key' => 'data_do_evento',
			'orderby' => 'meta_value',
			'order' => 'ASC',

			'meta_query' => array(
				array(
					'key' => 'data_do_evento',
					'value' => $ano_mes,
					'compare' => 'LIKE'
				)
			)

		);


		$this->query_ids = get_posts($this->args_ids);

		if ($this->query_ids){
			foreach ($this->query_ids as $item){
				$this->array_ids[] = $item->ID;
			}
		}

		foreach ($this->array_ids as $id){
			$this->array_datas[] = get_field('data_do_evento', $id);
		}

		$this->array_datas = json_encode($this->array_datas);

		echo '<input class="imprime-dados-ajax" type="hidden" name="array_datas_agenda" id="array_datas_agenda" value='.$this->array_datas.'>';

	}

}
