<?php
use Classes\ModelosDePaginas\PaginaAgendaSecretario\PaginaAgendaSecretario;

use Classes\TemplateHierarchy\ArchiveAgendaNew\ArchiveAgendaNew;

get_header();
?>
    <style>
        .agenda{
            display: none;
        }

        .agenda.agenda-new{
            display: block;
        }
    </style>
<?php
$pagina_agenda_secretario = new ArchiveAgendaNew();

get_footer();
