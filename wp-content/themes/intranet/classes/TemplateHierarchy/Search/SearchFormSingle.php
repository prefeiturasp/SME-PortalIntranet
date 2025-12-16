<?php


namespace Classes\TemplateHierarchy\Search;


class SearchFormSingle
{
	public function __construct(){}

	public static function searchFormHeader(){
	    ?>
        <div class="col-lg-6 col-sm-6 d-flex justify-content-lg-end justify-content-center">
            <form action="<?php echo home_url( '/' ); ?>" method="get" class="navbar-form navbar-left">
                <input type="hidden" name="tipo" value="post">
                <fieldset>
                    <legend>Campo de Busca de Notícias</legend>
                    <div class="input-group mb-3">
                        <label class="esconde-item-acessibilidade" for="search-loop">Campo de Busca de informações</label>
                        <input type="text" id="search-loop"  name="s" placeholder="<?php _e(BUSCARNOTICIAS,"wpbootstrap"); ?>" value="<?php the_search_query(); ?>" class="form-control" />
                        <div class="input-group-append">
                            <label for="enviar-busca-noticias" class="esconde-item-acessibilidade">Enviar a Busca de Notícias</label>
                            <input id="enviar-busca-noticias" name="enviar-busca-noticias" type="submit" class="btn btn-outline-secondary bt-search-topo" value="<?php _e('Buscar notícias','wpbootstrap'); ?>"/>
                       </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <?php
	}


}