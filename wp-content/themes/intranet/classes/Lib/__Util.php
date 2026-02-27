<?php

namespace Classes\Lib;


use Classes\TemplateHierarchy\ArchiveContato\ArchiveContato;

class Util
{
	protected $page_id;
	protected $deseja_exibir_subtitulo;
	protected $insira_o_subtitulo;
	protected $valor_campo_personalizado;

	public function __construct($page_id){
		$this->page_id = $page_id;
		$this->page_slug = get_queried_object()->post_name;

	}

	public function montaHtmlLoopPadrao()
	{

		echo '<section class="container">';
		if (have_posts()) : while (have_posts()) : the_post();
			?>
			<article class="row">
				<article class="col-lg-12 col-xs-12">
					<h1 class="mb-4" id="<?= $this->page_slug ?>"><?php the_title(); ?></h1>
				</article>
			</article>


			<article class="row">
				<article class="col-lg-9 col-xs-12">
					<?php echo $this->getSubtitulo($this->page_id)?>
					<?php the_content(); ?>
				</article>
			</article>
		<?php
		endwhile;
		endif;
		wp_reset_query();
		echo '</section>'; //container
	}

	public function montaHtmlLoopPadraoCard()
	{
		echo '<section class="container">';

		if (have_posts()) : while (have_posts()) : the_post();
			?>
            <article class="row">
                <article class="col-lg-12 col-xs-12">
                    <h1 class="mb-4" id="<?= $this->page_slug ?>"><?php the_title(); ?></h1>
                    <p>Atualizado em: <?php the_modified_date('d/m/Y'); ?></p>
                </article>
            </article>

            <article class="row">
                <article class="col-lg-12 col-xs-12 mb-5">
					<?php echo $this->getSubtitulo($this->page_id)?>
					<?php the_content(); ?>
                </article>
            </article>

		<?php
		endwhile;
		endif;
		wp_reset_query();
		echo '</section>'; //container
	}

	public function getSubtitulo($page_id, $tag_html = 'h2', $tag_css=null){
		//echo get_field('deseja_exibir_subtitulo', $page_id);
		$this->deseja_exibir_subtitulo = get_field('deseja_exibir_subtitulo', $page_id);
		$this->insira_o_subtitulo = get_field('insira_o_subtitulo', $page_id);

		if ($this->deseja_exibir_subtitulo == 'sim' && trim($this->insira_o_subtitulo != '')){
			return '<'.$tag_html.' class="mb-3 '.$tag_css.'">'.$this->insira_o_subtitulo.'</'.$tag_html.'>';
		}
		
		/*if($this->insira_o_subtitulo != ''){
			return '<'.$tag_html.' class="mb-3 '.$tag_css.'">'.$this->insira_o_subtitulo.'</'.$tag_html.'>';
		}*/
		
		if($this->deseja_exibir_subtitulo == 'nao'){
			return false;
		}

	}

	public function getCamposPersonalizados($nome_do_campo){
		$this->valor_campo_personalizado = get_field($nome_do_campo, $this->page_id);

		return $this->valor_campo_personalizado;

	}

	public function abreContainer(array $tags, array $css){

		foreach ($tags as $index => $tag){
			$array_tags[] = $tag.'_'.$index;
		}

		foreach ($css as $classe){
			$array_css[] = $classe;
		}

		$array_tags_e_css = array_combine($array_tags, $array_css);

		foreach ($array_tags_e_css as $index => $valor){
			$posicao = strpos($index, "_");
			$tag = substr($index,0,$posicao);

			echo '<'.$tag.' class="'.$valor.'">';
		}
	}

	public function fechaContainer($tags){
		foreach ($tags as $index => $tag){
			echo '</'.$tag.'>';
		}

	}

	public function montaHtmlBotaoMaisNoticias(){
		?>

        <section class="row">
            <article class="col-lg-12 col-xs-12 container-btn-mais-noticias">
                <form>
                    <fieldset>
                        <legend>Ir para notícias</legend>
                        <a href="<?= STM_URL.'/noticias' ?>" class="btn btn-primary btn-sm btn-block bg-azul-escuro font-weight-bold text-white">Notícias</a>
                    </fieldset>
                </form>
            </article>
        </section>

		<?php
	}




	public static function randString($size){
		//Essa função gera um valor de String aleatório do tamanho recebendo por parametros
		//String com valor possíveis do resultado, os caracteres pode ser adicionado ou retirados conforme sua necessidade
		$basic = 'abcdefghijklmnopqrstuvwxyz0123456789';

		$return= "";

		for($count= 0; $size > $count; $count++){
			//Gera um caracter aleatorio
			$return.= $basic[rand(0, strlen($basic) - 1)];
		}

		return $return;
	}

}