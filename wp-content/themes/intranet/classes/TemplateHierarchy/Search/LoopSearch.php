<?php

namespace Classes\TemplateHierarchy\Search;


class LoopSearch
{
	protected $search;
	protected $argsSearch;
	protected $querySearch;
	protected $verificaConteudoSearch = false;

	public function __construct()
	{
		$this->search = get_query_var('s');
		$this->montaTituloBusca();
		$this->montaQuerySearch();
		$this->verificaConteudoSearch();
		$this->montaHtmlSearch();

	}


	public function montaTituloBusca()
	{
		?>
        <div class="container padding-top-15 padding-bottom-30">
            <div class="row">
                <h2 style="margin-left: 15px;">
                    <i class="fa fa-search-plus"></i> Resultados da pesquisa para "<?= $this->search ?>"</h2>
            </div>
        </div>
		<?php
	}

	public function montaQuerySearch()
	{
		$this->argsSearch = array(
			'post_type' => array('post', 'page', 'card'),
			'post_parent' => 0,
			'paged' => get_query_var('paged'),
			's' => $this->search,
		);

		$this->querySearch = new \WP_Query($this->argsSearch);
	}


	public function verificaConteudoSearch()
	{

		$campo_de_busca = $_GET['s'];

		$texto_buscado_organograma = array(
			'CEDOC',
			'cedoc',
			'Centro de Documentação',
			'Centro de Documentacao',
			'centro de documentacao',
			'CENTRO DE DOCUMENTAÇÃO',
			'CENTRO DE DOCUMENTACAO',
			'DOCUMENTACAO',
			'documentacao',
			'documentação',
			'DOCUMENTAÇÃO',
			'Documentação',
			'Documentacao',
			' Centro de Documentação (CEDOC)',
			'Centro de Documentação – CEDOC',
			'Centro de Documentação – cedoc',
			'Centro de Documentação cedoc',
			'Centro de Documentação - CEDOC');
		if (in_array($campo_de_busca, $texto_buscado_organograma)) {
			$this->montaHtmlSearchPersonalizado('Centro de Documentação – CEDOC', 'centro-de-multimeios/memoria-documental/centro-de-documentacao-cedoc/');
		}

		$texto_buscado_organograma = array('organograma', 'Organograma');
		if (in_array($campo_de_busca, $texto_buscado_organograma)) {
			$this->montaHtmlSearchPersonalizado('Organograma — Secretaria Municipal de Educação', 'organograma');
		}

		$texto_buscado_mapa_dres = array('Mapa Dres', 'Mapa das Dres', 'mapa dres', 'mapa das dres', 'DRE s', 'dres');
		if (in_array($campo_de_busca, $texto_buscado_mapa_dres)) {
			$this->montaHtmlSearchPersonalizado("DRE's — Diretorias Regionais de Educação", 'mapa-dres');
		}

		$texto_buscado_curriculo_da_cidade = array('Curriculo da Cidade', 'curriculo da cidade', 'Currículo da Cidade', 'currículo da cidade');
		if (in_array($campo_de_busca, $texto_buscado_curriculo_da_cidade)) {
			$this->montaHtmlSearchPersonalizado("Currículo da Cidade", 'curriculo-da-cidade');
		}

		$texto_buscado_agenda = array('Agenda', 'agenda', 'Agenda do Secretário', 'agenda do secretário', 'Agenda do Secretario', 'agenda do secretario');
		if (in_array($campo_de_busca, $texto_buscado_agenda)) {
			$this->montaHtmlSearchPersonalizado("Agenda do Secretário de Educação", 'agenda');
		}

		$texto_buscado_busca_de_escolas = array('Escolas', 'escola', 'Busca de Escolas', 'busca de escolas', 'busca de escola', 'encontrar uma escola');
		if (in_array($campo_de_busca, $texto_buscado_busca_de_escolas)) {
			$this->montaHtmlSearchPersonalizado("Encontre a Escola Desejada", 'busca-de-escolas');
		}

		$this->verificaConteudoSearch = true;

	}

	public function montaHtmlSearchPersonalizado($titulo, $url)
	{

		?>
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-12 pb-4 border-bottom">
                    <h4 class="fonte-dezoito font-weight-bold mb-2">
                        <a class="text-decoration-none text-dark" href="<?= STM_URL . '/' . $url . '/' ?>">
							<?= $titulo ?>
                        </a>
                    </h4>
                    <div class="col-12">
                        <a class="btn btn-primary" href="<?= STM_URL . '/' . $url . '/' ?>"><?php echo VEJAMAIS ?></a>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	public function montaHtmlSearch()
	{
		?>

        <div class="container">

			<?php if ($this->querySearch->have_posts() || $this->verificaConteudoSearch) :

				while ($this->querySearch->have_posts()) : $this->querySearch->the_post();
					?>
                    <div class="row mb-4">
                        <div class="col-lg-12 pb-4 border-bottom">
							<?php

							if (has_post_thumbnail()) {
								echo '<figure class=" m-0">';
								the_post_thumbnail('medium', array('class' => 'img-fluid rounded float-left mr-4 w-25'));
								echo '</figure>';
							}
							?>
                            <h4 class="fonte-dezoito font-weight-bold mb-2">
                                <a class="text-decoration-none text-dark" href="<?php the_permalink() ?>">
									<?= get_the_title(); ?>
                                </a>
                            </h4>
                            <p class="fonte-dezesseis mb-2">
								<?php the_excerpt(); ?>
                            </p>

                            <div class="col-12">
                                <a class="btn btn-primary" href="<?php the_permalink(); ?>"><?php echo VEJAMAIS ?></a>
                            </div>

                        </div>
                    </div>

					<?php

					if (get_post_type(get_the_ID()) === 'page') {
						$this->montaQueryPaginasFilhas(get_the_ID());
					}

				endwhile;
			else:
				$this->montaHtmlNenhumPostEncontrado();
			endif;

			wp_reset_postdata();
			?>
            <br/>

			<?php paginacao($this->querySearch); ?>

            <div class="row container-taxonomias padding-bottom-15">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
                    <a class="btn btn-primary" href="javascript:history.back();"><< Voltar</a>
                </div>
            </div>

        </div>

		<?php
	}

	public function montaQueryPaginasFilhas($id_page)
	{

		$childArgs = array(
			'sort_order' => 'ASC',
			'sort_column' => 'menu_order',
			'child_of' => $id_page,
			'posts_per_page' => -1
		);

		$childList = get_pages($childArgs);

		if ($childList) {
			?>

			<?php
			foreach ($childList as $child) {
				$img_destacada = get_the_post_thumbnail_url($child->ID);
				$alt = get_post_meta(get_post_thumbnail_id($child->ID), '_wp_attachment_image_alt', true);
				?>
                <div class="row mb-4">
                    <div class="col-lg-12 pb-4 border-bottom">
						<?php
						if ($img_destacada) {
							echo '<figure class=" m-0">';
							echo '<img class="img-fluid rounded float-left mr-4 w-25" src="' . $img_destacada . '" alt="' . $alt . '">';
							echo '</figure>';
						}
						?>
                        <h4 class="fonte-dezoito font-weight-bold mb-2">
                            <a class="text-decoration-none text-dark" href="<?php the_permalink() ?>">
								<?= $child->post_title ?>
                            </a>
                        </h4>
                        <p class="fonte-dezesseis mb-2">
							<?= get_the_excerpt($child->ID) ?>
                        </p>

                        <div class="col-12">
                            <a class="btn btn-primary" href="<?= $child->guid ?>"><?php echo VEJAMAIS ?></a>
                        </div>

                    </div>
                </div>

			<?php }
		}
	}

	public function montaHtmlNenhumPostEncontrado()
	{
		?>

        <div class="container">
            <div class="row">
                <h3 class="cem-porcento"><i class="fa fa-exclamation-triangle"></i> Nenhum resultado encontrado para
                    "<?= $this->search ?>".</h3>
                <h3 class="cem-porcento">Tente uma pesquisa diferente ou utilize o menu acima para navegar.</h3>
                <br/>

				<?php SearchForm::searchFormLoopSearch() ?>

            </div>
        </div>

		<?php
	}


}