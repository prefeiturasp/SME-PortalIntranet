<?php

use Classes\TemplateHierarchy\ArchiveOrganograma\ArchiveOrganogramaDetectMobile;

get_header();

$archive_organograma_detect_mobile = new ArchiveOrganogramaDetectMobile();
$archive_organograma_detect_mobile->init();

get_footer();
