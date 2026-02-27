<?php
use Classes\ModelosDePaginas\PaginaAgendaSecretario\PaginaAgendaSecretario;

use Classes\TemplateHierarchy\ArchiveAgenda\ArchiveAgenda;

get_header();

?>
    <style>
        .agenda.agenda-new{
            display: none;
        }
    </style>
<?php

$pagina_agenda_secretario = new ArchiveAgenda();

get_footer();