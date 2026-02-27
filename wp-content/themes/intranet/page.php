<?php

use Classes\TemplateHierarchy\Page;

get_header();

$pagina_page = new Page();
//contabiliza visualizações de noticias
setPostViews(get_the_ID());  //echo getPostViews(get_the_ID());
get_footer();