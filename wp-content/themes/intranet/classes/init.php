<?php
/* Inicialização das Classes */

require_once __ROOT__.'/classes/LoadDependences.php';
require_once __ROOT__.'/classes/Lib/Util.php';
require_once __ROOT__.'/classes/Lib/SimpleXLSXGen.php';

require_once __ROOT__.'/classes/Header/Header.php';

require_once __ROOT__.'/classes/Usuarios/Editor/Editor.php';
require_once __ROOT__.'/classes/Usuarios/Colaborador/Colaborador.php';
require_once __ROOT__.'/classes/Usuarios/Administrador/Administrador.php';
require_once __ROOT__.'/classes/Usuarios/Assessor/Assessor.php';
//require_once __ROOT__.'/classes/Usuarios/Dre/Dre.php';
require_once __ROOT__.'/classes/Usuarios/EnviarParaRevisao.php';
require_once __ROOT__.'/classes/Usuarios/CamposAdicionais.php';

//tutorial
require_once __ROOT__.'/classes/tutorial/tutorial.php';

//sancao
require_once __ROOT__.'/classes/sancao/sancao.php';

//Histórico de envio de e-mails de instrução dos sorteios
require_once __ROOT__.'/classes/HistoricoEmails/HistoricoEmails.php';

require_once __ROOT__.'/classes/Cpt/Cpt.php';
require_once __ROOT__.'/classes/Cpt/CptPosts.php';
require_once __ROOT__.'/classes/Cpt/CptNoticias.php';
require_once __ROOT__.'/classes/Cpt/CptPages.php';
//require_once __ROOT__.'/classes/Cpt/CptCard.php';
require_once __ROOT__.'/classes/Cpt/CptAgendaSecretario.php';
require_once __ROOT__.'/classes/Cpt/CptAgendaSecretarioNew.php';
//require_once __ROOT__.'/classes/Cpt/CptContato.php';
//require_once __ROOT__.'/classes/Cpt/CptOrganograma.php';
//require_once __ROOT__.'/classes/Cpt/CptAba.php';
//require_once __ROOT__.'/classes/Cpt/CptBotao.php';
//require_once __ROOT__.'/classes/Cpt/CptCurriculoDaCidade.php';
//require_once __ROOT__.'/classes/Cpt/cptProgramaProjeto.php';
//require_once __ROOT__.'/classes/Cpt/CptConcursos.php';
require_once __ROOT__.'/classes/Cpt/CptDestaques.php';
require_once __ROOT__.'/classes/Cpt/CptPortais.php';
require_once __ROOT__.'/classes/Cpt/CptFaqs.php';
require_once __ROOT__.'/classes/Cpt/CptParceiros.php';
require_once __ROOT__.'/classes/Cpt/CptSmeExplica.php';
require_once __ROOT__.'/classes/Cpt/CptMuralProf.php';
require_once __ROOT__.'/classes/Cpt/CptCortesias.php';

require_once __ROOT__.'/classes/TemplateHierarchy/Page.php';
require_once __ROOT__.'/classes/TemplateHierarchy/Tag.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopSingleCard.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopSingle/LoopSingle.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopSingle/LoopSingleCabecalho.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopSingle/LoopSingleMenuInterno.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopSingle/LoopSingleNoticiaPrincipal.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopSingle/LoopSingleMaisRecentes.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopSingle/LoopSingleRelacionadas.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopNoticias/LoopNoticias.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopNoticias/LoopNoticiasCabecalho.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopNoticias/LoopNoticiasNoticiaPrincipal.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopNoticias/LoopNoticiasMaisRecentes.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopNoticias/LoopNoticiasComentarios.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopExplica/LoopExplica.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopExplica/LoopExplicaCabecalho.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopExplica/LoopExplicaNoticiaPrincipal.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopExplica/LoopExplicaRelacionadas.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopMural/LoopMural.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopMural/LoopMuralCabecalho.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopMural/LoopMuralNoticiaPrincipal.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopMural/LoopMuralRelacionadas.php';

/** Noticias de Gratuidade e cortesias **/
require_once __ROOT__.'/classes/TemplateHierarchy/LoopCortesias/LoopCortesias.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopCortesias/LoopCortesiasCabecalho.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopCortesias/LoopCortesiasNoticiaPrincipal.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopCortesias/LoopCortesiasMaisRecentes.php';
require_once __ROOT__.'/classes/TemplateHierarchy/LoopCortesias/LoopCortesiasRelacionadas.php';

require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveAgenda/ArchiveAgendaGetDatasEventos.php';
//require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveContato/ArchiveContatoMetabox.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveContato/ArchiveContato.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveContato/ExibirContatosTodasPaginas.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveAgenda/ArchiveAgenda.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveAgenda/ArchiveAgendaAjaxCalendario.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveAgenda/ArchiveAgendaAjaxCalendarioNew.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveAgendaNew/ArchiveAgendaGetDatasEventosNew.php';


require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveAgendaNew/ArchiveAgendaNew.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveAgendaNew/ArchiveAgendaAjaxCalendarioNew.php';

require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganogramaDetectMobile.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganograma.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganogramaConselhos.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganogramaSecretario.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganogramaAssessorias.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganogramaCoordenadorias.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganogramaDres.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/ArchiveOrganogramaRodape.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/Mobile/ArchiveOrganogramaMobile.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/Mobile/ArchiveOrganogramaConselhosMobile.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/Mobile/ArchiveOrganogramaSecretarioMobile.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/Mobile/ArchiveOrganogramaAssessoriasMobile.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/Mobile/ArchiveOrganogramaCoordenadoriasMobile.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/Mobile/ArchiveOrganogramaDresMobile.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveOrganograma/Mobile/ArchiveOrganogramaRodape.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveCurriculoDaCidade/ArchiveCurriculoDaCidade.php';
require_once __ROOT__.'/classes/TemplateHierarchy/ArchiveProgramaProjeto/ArchiveProgramaProjeto.php';

require_once __ROOT__.'/classes/TemplateHierarchy/Search/GetTipoDePost.php';
require_once __ROOT__.'/classes/TemplateHierarchy/Search/SearchForm.php';
require_once __ROOT__.'/classes/TemplateHierarchy/Search/LoopSearch.php';
require_once __ROOT__ .'/classes/TemplateHierarchy/Search/SearchFormSingle.php';
require_once __ROOT__ .'/classes/TemplateHierarchy/Search/LoopSearchSingle.php';


require_once __ROOT__.'/classes/ModelosDePaginas/Login/Login.php';
require_once __ROOT__.'/classes/ModelosDePaginas/Login/LoginForm.php';
require_once __ROOT__.'/classes/ModelosDePaginas/Login/LoginRecuperar.php';

require_once __ROOT__.'/classes/ModelosDePaginas/Layout/construtor.php';


require_once __ROOT__.'/classes/BuscaDeEscolas/BuscaDeEscolasRewriteUrl.php';
require_once __ROOT__.'/classes/BuscaDeEscolas/BuscaDeEscolas.php';

require_once __ROOT__.'/classes/Breadcrumb/Breadcrumb.php';

require_once __ROOT__.'/classes/ModelosDePaginas/ModelosDePaginaRemoveThemeSupport.php';

require_once __ROOT__.'/classes/Cpt/CptMediaImages.php';

/* Inicialização CPTs */
$cptPostsExtend = new \Classes\Cpt\CptPosts();
$cptPagessExtend = new \Classes\Cpt\CptPages();

$cptNoticias = new \Classes\Cpt\Cpt('noticia', 'noticia', 'Notícias', 'Todos as Notícias', 'Notícias', 'Notícia', 'categorias-noticias', 'Categorias', 'Categoria', 'dashicons-welcome-write-blog', true);
$cptNoticiasExtend = new \Classes\Cpt\CptNoticias();

$cptAgendaSecretario = new \Classes\Cpt\Cpt('agenda', 'agenda', 'Agenda de Eventos SME', 'Todos os Eventos', 'Eventos', 'Eventos', null, null, null, 'dashicons-calendar-alt', true);
$cptAgendaSecretarioExtend = new \Classes\Cpt\CptAgendaSecretario();

$cptAgendaSecretarioNew = new \Classes\Cpt\Cpt('agendanew', 'agendanew', 'Calendário Escolar', 'Todos os Eventos', 'Eventos', 'Eventos', null, null, null, 'dashicons-calendar-alt', true);
$cptAgendaSecretarioNewExtend = new \Classes\Cpt\CptAgendaSecretarioNew();

//$cptContatoSme = new \Classes\Cpt\Cpt('contato', 'contato', 'Contatos SME', 'Todos os Contatos', 'Contatos', 'Contato', null, null, null ,'dashicons-email-alt', true);
//$cptContatoSmeExtend = new \Classes\Cpt\CptContato();
//$cptOrganograma = new \Classes\Cpt\Cpt('organograma', 'organograma-sec', 'Organograma', 'Todos os Itens', 'Organogramas', 'Organograma', 'categorias-organograma', 'Categorias de Organograma', 'Categoria de Organograma', 'dashicons-networking', true );

//$cptAbas = new \Classes\Cpt\Cpt('aba', 'aba', 'Cadastro de Abas', 'Todos as Abas', 'Abas', 'Cadastro de Abas', 'categorias-aba', 'Categorias de Abas', 'Categoria de Aba', 'dashicons-index-card' , true);
//$cptAbasExtend = new \Classes\Cpt\CptAba();

//$cptBotao = new \Classes\Cpt\Cpt('botao', 'botao', 'Cadastro de Botões', 'Todos os Botões', 'Botões', 'Cadastro de Botões', 'categorias-botao', 'Categorias de Botões', 'Categoria de Botão', 'dashicons-external' , true);
//$cptBotaoExtend = new \Classes\Cpt\CptBotao();

$taxonomiaMediaImages = new \Classes\Cpt\CptMediaImages();

//$cptCurriculoDaCidade = new \Classes\Cpt\Cpt('curriculo-da-cidade', 'curriculo-da-cidade', 'Currículo da Cidade', 'Todos os Currículos', 'Currículos da Cidade', 'Currículo da Cidade', 'categorias-curriculo-da-cidade', 'Categorias de Currículos', 'Categoria de Currículo', 'dashicons-format-image', true);
//$cptCurriculoDaCidadeExtende = new \Classes\Cpt\CptCurriculoDaCidade();

// Concursos
//$cptConcursos = new \Classes\Cpt\Cpt('concurso', 'concurso', 'Cadastro de Concurso', 'Todos os Concursos', 'Concursos', 'Cadastro de Concurso', '', '', '', 'dashicons-external' , true);
//$cptConcursosExtend = new \Classes\Cpt\CptConcursos();

//$cptProgramasEProjetos = new \Classes\Cpt\Cpt('programa-projeto', 'programa-projeto', 'Programas e Projetos', 'Todos os Programas e Projetos', 'Programas e Projetos', 'Programas e Projetos', 'categorias-programa-projeto', 'Categorias de Programas e Projetos', 'Categoria de Programas e Projetos', 'dashicons-format-image', true);
//$cptProgramasEProjetosExtende = new \Classes\Cpt\CptProgramasEProjetos();

$cptDestaques = new \Classes\Cpt\Cpt('destaque', 'destaque', 'Recado', 'Todos os Recados', 'Recados', 'Recados', null, null, null, 'dashicons-external', true);
$cptDestaquesExtend = new \Classes\Cpt\CptDestaques();

$cptPortais = new \Classes\Cpt\Cpt('portais', 'portais', 'Portais e Sistemas', 'Todos os Portais', 'Portais e Sitema', 'Portais e Sitema', null, null, null, 'dashicons-external', true);
$cptPortaisExtend = new \Classes\Cpt\CptPortais();

$cptFaq = new \Classes\Cpt\Cpt('intranet-faq', 'intranet-faq', 'FAQs', 'Todos as FAQs', 'FAQ', 'FAQ', null, null, null, 'dashicons-external', true);
$cptFaqExtend = new \Classes\Cpt\CptFaqs();

$cptParceiros = new \Classes\Cpt\Cpt('parceiros', 'parceiros', 'Parceiros', 'Todos os Parceiros', 'parceiros', 'parceiros', null, null, null, 'dashicons-external', true);
$cptParceirosExtend = new \Classes\Cpt\CptParceiros();

$cptSmeExplica = new \Classes\Cpt\Cpt('info-sme-explica', 'info-sme-explica', 'SME Explica', 'Todos os Informativos', 'info-sme-explica', 'info-sme-explica', null, null, null, 'dashicons-external', true);
$cptSmeExplicaExtend = new \Classes\Cpt\CptSmeExplica();

$cptMuralProf = new \Classes\Cpt\Cpt('mural-professores', 'mural-professores', 'Mural dos Professores', 'Todas as Publicações', 'mural-professores', 'mural-professores', null, null, null, 'dashicons-external', true);
$cptMuralProfExtend = new \Classes\Cpt\CptMuralProf();

$cptCortesias = new \Classes\Cpt\Cpt('cortesias', 'cortesias', 'Gratuidade e cortesias', 'Gratuidade e cortesias', 'cortesias', 'cortesias', null, null, null, 'dashicons-tickets', true);
$cptCortesiasExtend = new \Classes\Cpt\CptCortesias();