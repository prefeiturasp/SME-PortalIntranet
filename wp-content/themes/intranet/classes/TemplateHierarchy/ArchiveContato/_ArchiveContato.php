<?php

namespace Classes\TemplateHierarchy\ArchiveContato;


use Classes\Lib\Util;

class ArchiveContato extends Util
{
	const TAXONOMIA = 'categorias-contato';
	protected $args_terms;
	protected $terms;
	protected $array_ordenacao;
	protected $qtdeNiveis;
	protected $tag_html_titulo;
	protected $css_html_titulo;


	public function __construct($tag_html_titulo = 'h1', $css_html_titulo='mb-5')
	{
		$this->tag_html_titulo = $tag_html_titulo;
		$this->css_html_titulo = $css_html_titulo;

		$container_html_tags = array('section', 'section');
		$container_html_css = array('container', 'row container-contatos');
		$this->abreContainer($container_html_tags,$container_html_css);

		$this->exibeCabecalho();
		$this->getTermosTaxonomiasContato();
		$this->obterQtdeDeNiveis();
		$this->percorreNiveis();

		$this->fechaContainer($container_html_tags);
	}

	public function exibeCabecalho(){
		echo '<'.$this->tag_html_titulo.' class="'.$this->css_html_titulo.'" id="contato">Contatos SME</'.$this->tag_html_titulo.'>';
	}

	public function setQtdeNiveis($qtdeNiveis)
	{
		$this->qtdeNiveis = $qtdeNiveis;
	}


	public function getQtdeNiveis()
	{
		return $this->qtdeNiveis;
	}

	public function abreColunas($i){
		if ($i == 1){
			echo '<article class="col-12 container-contatos">';
		}else{
			echo '<article class="col-12 col-md-6 container-contatos">';
		}

	}

	public function fechaColunas(){
		echo '</article>';
	}

	public function getTermosTaxonomiasContato(){

		$this->args_terms = array(
			'orderby'           => 'meta_value_num',
			'order'             => 'ASC',
			'hide_empty'        => false,
			'exclude'           => array(),
			'exclude_tree'      => array(),
			'include'           => array(),
			'number'            => '',
			'fields'            => 'all',
			'slug'              => '',
			'parent'            => '',
			'hierarchical'      => true,
			'child_of'          => 0,
			'childless'         => false,
			'get'               => '',
			'name__like'        => '',
			'description__like' => '',
			'pad_counts'        => false,
			'offset'            => '',
			'search'            => '',
			'cache_domain'      => 'core'

		);

		$this->terms = get_terms(self::TAXONOMIA, $this->args_terms);

	}

	public function obterQtdeDeNiveis(){
		foreach ($this->terms as $term){
			$campo_contato_nivel = get_post_meta($term->term_id, 'campo_contato_nivel', true);
			$array_qtde_niveis[] = $campo_contato_nivel;
		}
		$this->setQtdeNiveis( max($array_qtde_niveis));
	}

	public function percorreNiveis(){

		for ($i = 1; $i <= $this->getQtdeNiveis(); $i++) {
			$this->abreColunas($i);
			$this->exibeDadosTaxonomiasContato($i);
			$this->fechaColunas();
		}
	}


	public function exibeDadosTaxonomiasContato($nivel){

		$novo_array = array();

		foreach ($this->terms as $term){
			$campo_contato_nivel = get_post_meta($term->term_id, 'campo_contato_nivel', true);
			$novo_array[] = ['termo_id' => $term->term_id, 'termo_nome' =>$term->name,  'ordenacao' => $campo_contato_nivel ];
		}

		foreach ($novo_array as $array){

			if ($array['ordenacao'] == $nivel) {

				$this->exibeCamposCadastrados($array['termo_id'], $array['termo_nome'], true);
				$this->getContatosTaxonomia($array['termo_id']);
			}
		}
	}

	public function getContatosTaxonomia($term_id){

		$args = array(
			//'orderby' => array('title date'),
			'orderby' => array( 'title' => 'ASC', 'date' => 'ASC' ),
			'order' => 'ASC',
			'post_type' => 'contato',
			'posts_per_page'   => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'categorias-contato',
					'field' => 'term_id',
					'terms' => $term_id,
				)
			)
		);

		$posts_array = get_posts( $args );

		foreach ($posts_array as $cpt){
			$this->exibeCamposCadastrados($cpt->ID, $cpt->post_title);
		}
	}

	public function exibeCamposCadastrados($term_id, $term_name=null, $nivel_superior=null, $organograma=null){

		$campo_contato = get_post_meta($term_id, 'campo_contato', true);

		if ($term_name) {
			echo $this->montaTituloCamposCadastrados($term_name, $nivel_superior);
		}

		$array_tipo_de_campo=[];
		$array_valor_do_campo=[];

		if ($campo_contato) {

			foreach ($campo_contato as $index => $valor) {
				if ($index == 'tipo') {
					foreach ($valor as $tipo) {
						$array_tipo_de_campo[] = $tipo;
					}
				}
				if ($index == 'valor') {
					foreach ($valor as $v) {
						$array_valor_do_campo[] = $v;
					}
				}
			}
		}

		foreach($array_tipo_de_campo as $key => $value) {
			$data[] = array('tipo' => $value, 'valor' => $array_valor_do_campo[$key]);
		}

		$this->montaHtmlCamposCadastrados($data, $organograma);


	}

	public function montaHtmlCamposCadastrados($data, $organograma){
		if ($data) {

			if ($organograma){
				$classe_css = 'text-white';
			}else{
				$classe_css = '';
			}

			foreach ($data as $d) {

				if ($d['tipo'] == 'email') {
					echo '<p>' . $this->getNomeTipoCampo($d['tipo']) . '<a class="'.$classe_css.'" href="mailto: ' . esc_attr($d['valor']) . '">' . esc_attr($d['valor']) . '</a></p>';
				}elseif ($d['tipo'] == 'tel'){
					echo '<p>' . $this->getNomeTipoCampo($d['tipo']) . '<a class="'.$classe_css.'" href="tel: ' . esc_attr($d['valor']) . '">' . esc_attr($d['valor']) . '</a></p>';
				}elseif($d['tipo'] == 'site'){
					echo '<p> <strong>Visite o site:</strong> ' . '<a class="'.$classe_css.'" href="' . esc_attr($d['valor']) . '">' . esc_attr($d['valor']) . '</a></p>';
				}else{
					echo '<p>'.$this->getNomeTipoCampo($d['tipo']).esc_attr($d['valor']).'</p>';
				}
			}
		}else{
			return;
		}
	}

	public function montaTituloCamposCadastrados($term_name, $nivel_superior=null){
		if ($nivel_superior) {
			return '<p class="titulo-nivel-superior mt-2 pt-2">' . $term_name . '</p>';
		}else{
			return '<p class="titulo-nivel-nao-superior mt-2 pt-2 border-top">' . $term_name . '</p>';
		}
	}

	public function getNomeTipoCampo($tipo_de_campo){
		switch ($tipo_de_campo) {
			case 'text':
				return '';
			//break;
		}
		switch ($tipo_de_campo) {
			case 'tel':
				return '<span class="nome-campo"><strong>Telefone: </strong></span>';
			//break;
		}

		switch ($tipo_de_campo) {
			case 'email':
				return '<span class="nome-campo"><strong>Email: </strong></span>';
			//break;
		}


	}

}