<?php

namespace Classes\TemplateHierarchy\Search;


class SearchForm
{
	public static function searchFormLoopSearch(){
		?>
		<form action="<?php echo home_url( '/' ); ?>" method="get" class="navbar-form navbar-left pt-2">
			<fieldset>
                <legend>Campo de Busca de informações</legend>
				<div class="input-group mb-3">
                    <label class="esconde-item-acessibilidade" for="search-front-end">Campo de Busca de informações</label>
                    <input type="text" name="s" placeholder="<?php _e('Pesquisar na Intranet',"wpbootstrap"); ?>" value="<?php the_search_query(); ?>" class="form-control" />
					
                    <div class="input-group-append mb-2">
                        <label for="enviar-outra-busca" class="esconde-item-acessibilidade">Enviar Outra Busca</label>
						<input id="enviar-outra-busca" name="enviar-outra-busca" type="submit" class="btn btn-primary bt-search-topo" value="<?php _e('Buscar novamente','wpbootstrap'); ?>"/>
					</div>

                    

				</div>
			</fieldset>
		</form>
		<?php
	}

	public static function searchFormHeader(){
		?>
        <section class="container">
        <section class="row">
            <section class="col-12 d-flex flex-row-reverse mt-3">
                <form action="<?php echo home_url( '/' ); ?>" method="get" class="navbar-form navbar-left">
                    <fieldset>
                        <legend>Campo de Busca de informações</legend>
                        <div class="input-group mb-3">
                            <label class="esconde-item-acessibilidade" for="search-front-end">Campo de Busca de informações</label>
                            <input type="text" name="s" id="search-front-end" placeholder="<?php _e('Pesquisar na Intranet',"wpbootstrap"); ?>" value="<?php the_search_query(); ?>" class="form-control" />
                            
                            <div class="input-group-append mb-2">
                                <label for="enviar-busca-home" class="esconde-item-acessibilidade">Enviar a Busca</label>
                                <input id="enviar-busca-home" name="enviar-busca-home" type="submit" class="btn btn-primary bt-search-topo" value="<?php _e('Buscar','wpbootstrap'); ?>" />
                            </div>

                        </div>
                    </fieldset>
                </form>
            </section>
        </section>
        </section>

		<?php
	}

}