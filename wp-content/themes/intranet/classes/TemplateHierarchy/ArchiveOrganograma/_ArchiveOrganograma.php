<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma;

use Classes\TemplateHierarchy\ArchiveContato\ArchiveContato;

class ArchiveOrganograma extends ArchiveContato
{
	const CPT = 'organograma';
	const TAXONOMIA = 'categorias-organograma';
	const SLUG_TAXONOMIA_SECRETARIO = 'secretarioa-secretarioa-adjuntoa';
	const SLUG_TAXONOMIA_ADJUNTO = 'secretarioa-adjuntoa';
	const SLUG_TAXONOMIA_DRES = 'diretorias-regionais-de-educacao-dres';
	protected $page_id;
	protected $args_taxonomias;
	protected $query_taxonomias;
	protected $taxonomy_name;
	protected static $id_taxonomia_coordenadorias;
	protected static $id_taxonomia_dres;

	public function __construct()
	{
		self::$id_taxonomia_coordenadorias = get_term_by('slug', 'coordenadorias', self::TAXONOMIA)->term_id;
		self::$id_taxonomia_dres = get_term_by('slug', 'diretorias-regionais-de-educacao-dres', self::TAXONOMIA)->term_id;
	}

	public function init(){

	    $this->getTituloSubtitulo();

		$divs_geral = array('container mb-5', 'row', 'col-lg-12 mb-5', 'organograma fonte-catorze d-lg-block d-sm-block d-none');
		$this->abreDivs(null, $divs_geral);

		new ArchiveOrganogramaConselhos();

		new ArchiveOrganogramaSecretario();

		$divs_assessoria_coordenadorias_dres = array('w-75 ml-auto position-relative');
		$this->abreDivs(null, $divs_assessoria_coordenadorias_dres);

		new ArchiveOrganogramaAssessorias();

		new ArchiveOrganogramaCoordenadorias();

		new ArchiveOrganogramaDres();

		$this->fechaDivs($divs_assessoria_coordenadorias_dres);

		new ArchiveOrganogramaRodape();

		$this->fechaDivs($divs_geral);

	}

	public function getTituloSubtitulo(){
	    ?>
        <section class="container">
            <article class="row">
                <article class="col-lg-12 col-xs-12">
                    <h1 class="mb-5" id="organograma">Organograma — Secretaria Municipal de Educação</h1>
                </article>
            </article>
            <article class="row">
                <article class="col-lg-10 col-xs-12">
                    <h2>Decreto nº 58.154, de 22 de março de 2018</h2>
                    <p>Atualizado em: <?php echo get_the_modified_date('d/m/Y'); ?> </p>
                </article>
            </article>
        </section>
        <?php

    }

	public function setTaxonomyName($taxonomy_name){
		$term = get_term_by('slug', $taxonomy_name, self::TAXONOMIA);
		$this->taxonomy_name = $term;
	}

	public function getTaxonomyName(){
		return $this->taxonomy_name->name;
	}
	
	public function setTaxonomyAdj($taxonomy_adj){
		$term = get_term_by('slug', $taxonomy_adj, self::TAXONOMIA);
		$this->taxonomy_adj = $term;
	}

	public function getTaxonomyAdj(){
		return $this->taxonomy_adj->name;
	}

	public function getTaxonomyTermId(){
		return $this->taxonomy_name->term_id;
	}

	public function abreDivs($id= null, array $classes_divs, $aria_labelledby = null){

		foreach ($classes_divs as $classe_div){

		    if ($classes_divs != null) {
				if ($id) {
					echo '<div class="' . $classe_div . '" id="' . $id . '" >';
				} else {
					echo '<div class="' . $classe_div . '">';
				}
			}
		}
	}

	public function fechaDivs(array $classes_divs){

		if ($classes_divs != null) {

			for ($i = 1; $i <= count($classes_divs); $i++) {
				echo '</div>';
			}
		}
	}

	public function verificaSeExistemItensTaxonomias($term){

		$this->args_taxonomias = array(
			'post_type' => self::CPT,
			'posts_per_page' => -1,
			'hide_empty' => false,
			'tax_query' => array(
				array(
					'taxonomy' => self::TAXONOMIA,
					'field' => 'slug',
					'terms' => $term,
				),
			),
		);

		$this->query_taxonomias = get_posts($this->args_taxonomias);
		$total = count(get_posts($this->args_taxonomias));
        return $total;
    }

	public function montaQueryItensTaxonomias($term)
	{
		$this->args_taxonomias = array(
			'post_type' => self::CPT,
			'posts_per_page' => -1,
			'hide_empty' => false,
			'tax_query' => array(
				array(
					'taxonomy' => self::TAXONOMIA,
					'field' => 'slug',
					'terms' => $term,
				),
			),
		);
		$this->query_taxonomias = get_posts($this->args_taxonomias);

		return $this->query_taxonomias;
	}

	public function getContatos(){
		$deseja_exibir_contato = get_field('deseja_exibir_contato', self::TAXONOMIA . '_' . $this->getTaxonomyTermId());
		$escolha_o_contato_que_deseja_exibir = get_field('escolha_o_contato_que_deseja_exibir', self::TAXONOMIA . '_' . $this->getTaxonomyTermId());

		if ($deseja_exibir_contato == 'sim'){
			$this->montaHtmlContatos($escolha_o_contato_que_deseja_exibir);
		}

	}

	public function montaHtmlNameTaxonomy($classe_p)
	{
		//Adiciona secretário e Adjunto
		echo '<p class="'.$classe_p.'">'.$this->getTaxonomyName().'<br>'.$this->getTaxonomyAdj().'</p>';

	}

	public function montaHtmlNameTaxonomyFilhas($classe_h2)
	{
		echo '<h2 class="'.$classe_h2.'">';
		echo '<a class="text-white font-weight-bold text-decoration-none" data-toggle="collapse" data-target="#id_'.$this->getTaxonomyTermId().'" aria-expanded="false" aria-controls="id_'.$this->getTaxonomyTermId().'" href="">';
		echo $this->getTaxonomyName();
		echo '</a>';
		echo '</h2>';
	}

	public function montaHtmlItensTaxonomias(array $divs_externas, array $divs_internas, $classe_p, $query)
	{

		$cont = 0;


			$this->abreDivs(null,$divs_externas);


			foreach ($query as $index => $term) {

				if ($cont >= 4) {
					$this->fechaDivs($divs_externas);
					$divs_externas = array('card-deck mt-4 borda-itens borda-cinza-claro-organo');
					$this->abreDivs(null, $divs_externas);
					$cont = 0;
				}

				$this->abreDivs(null, $divs_internas);
				echo '<p class="' . $classe_p . '">' . $term->post_title . '</p>';
				$this->fechaDivs($divs_internas);

				$cont++;
			}
			$this->fechaDivs($divs_externas);



	}

	public function montaHtmlContatos($escolha_o_contato_que_deseja_exibir){
		?>
		<div id="id_<?=$this->getTaxonomyTermId()?>" class="collapse fade" aria-labelledby="<?= 'heading'.$this->getTaxonomyTermId() ?>">
			<div class="card-body bg-cinza-escuro-organo mb-3 rounded-bottom">
				<p class="card-text text-white text-center">
					<?php
					$this->exibeCamposCadastrados($escolha_o_contato_que_deseja_exibir, null, null, true);
					?>
				</p>
			</div>
		</div>
		<?php
	}
}