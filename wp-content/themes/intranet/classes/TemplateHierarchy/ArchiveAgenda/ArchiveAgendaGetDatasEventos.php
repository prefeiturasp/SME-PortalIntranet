<?php
namespace Classes\TemplateHierarchy\ArchiveAgenda;
class ArchiveAgendaGetDatasEventos
{
	const CPTAGENDA = 'agenda';
	private $args_ids;
	private $query_ids;
	private $array_ids;
	private $array_datas;
	public function __construct()
	{
		$this->init();
	}
	public function init(){
		$current_url = $_SERVER['REQUEST_URI'];
		$partes = explode("/", $current_url);
		if ($partes[1] === 'agendaold' || $partes[2] === 'agendaold') {
			$this->getTodosIdCtpAgenda();
			$this->getDatasCptAgenda();
		}

		if ($partes[1] === 'agenda' || $partes[2] === 'agenda') {
			$this->getTodosIdCtpAgendaTwo();
			$this->getDatasCptAgendaTwo();
		}
	}
	public function getTodosIdCtpAgenda(){
		$this->args_ids = array(
			'post_type' => 'agenda',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_key' => 'data_do_evento',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			/*'meta_query' => array(
				array(
					'key' => 'data_do_evento',
					'value' => date("d/m/Y"), // date format error
					'compare' => '<='
				)
			)*/
		);
		$this->query_ids = get_posts($this->args_ids);
		if ($this->query_ids){
			foreach ($this->query_ids as $item){
				$this->array_ids[] = $item->ID;
			}
		}
	}
	public function getDatasCptAgenda(){
		foreach ($this->array_ids as $id){
			$this->array_datas[] = get_field('data_do_evento', $id);
		}
		$this->array_datas = json_encode($this->array_datas);

		//echo '<div name="array_datas_agenda" id="array_datas_agenda">'.$this->array_datas.'</div>';
		echo '<input type="hidden" name="array_datas_agenda" id="array_datas_agenda" value='.$this->array_datas.'>';
	}

	public function getTodosIdCtpAgendaTwo(){
		$this->args_ids = array(
			'post_type' => 'agenda',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_key' => 'data_do_evento',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			/*'meta_query' => array(
				array(
					'key' => 'data_do_evento',
					'value' => date("d/m/Y"), // date format error
					'compare' => '<='
				)
			)*/
		);
		$this->query_ids = get_posts($this->args_ids);
		if ($this->query_ids){
			foreach ($this->query_ids as $item){
				$this->array_ids[] = $item->ID;
			}
		}
	}
	public function getDatasCptAgendaTwo(){
		foreach ($this->array_ids as $id){
			$this->array_datas[] = get_field('data_do_evento', $id);
		}
		$this->array_datas = json_encode($this->array_datas);

		//echo '<div name="array_datas_agenda" id="array_datas_agenda">'.$this->array_datas.'</div>';
		echo '<input type="hidden" name="array_datas_agenda" id="array_datas_agenda" value='.$this->array_datas.'>';
	}
}
new ArchiveAgendaGetDatasEventos;