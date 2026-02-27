<form id="demo-2" action="<?php echo home_url( '/' ); ?>" method="get" class="form-inline " style="padding-top: 12px;">
    <fieldset>
		<div class="input-group">
            <input type="search" name="s" id="search" placeholder="<?php _e(BUSCAR,"wpbootstrap"); ?>" value="<?php the_search_query(); ?>" class="input-search-topo" />
		</div>
    </fieldset>
</form>




