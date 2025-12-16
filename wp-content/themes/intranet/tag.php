<?php

use Classes\TemplateHierarchy\PaginaTag;

get_header();

$pagina_tag = new PaginaTag();

//contabiliza visualizações
setPostViews(get_the_ID());  //echo getPostViews(get_the_ID());

get_footer();