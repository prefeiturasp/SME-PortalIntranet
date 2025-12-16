<?php

namespace Classes;

use Classes\ModelosDePaginas\PaginaContato\PaginaContatoMetabox;
use Classes\TemplateHierarchy\ArchiveAgenda\ArchiveAgendaAjaxCalendario;
use Classes\TemplateHierarchy\ArchiveAgendaNew\ArchiveAgendaAjaxCalendarioNew;
use Classes\TemplateHierarchy\ArchiveAgenda\ArchiveAgendaGetDatasEventos;
use Classes\TemplateHierarchy\ArchiveContato\ArchiveContatoMetabox;

class LoadDependences
{
	public function __construct()
	{
		$this->loadDependencesPublic();
		$this->loadDependencesAdmin();
	}

	public function loadDependencesPublic(){
		//if (!is_admin()){
			add_action('init', array($this, 'custom_formats_public'));
		//}
	}
	public function loadDependencesAdmin(){
		if (is_admin()){
			//add_action('init', array($this, 'custom_formats_admin'));
		}
	}

	public function custom_formats_public(){
		// Página Inicial
		if(!is_admin()){
			wp_register_style('pagina-inicial', STM_THEME_URL . 'classes/assets/css/pagina-inicial.css', null, null, 'all');
			wp_enqueue_style('pagina-inicial');
		}
		// Programas e Projetos
		//wp_register_style('programa-projeto', STM_THEME_URL . 'classes/assets/css/programa-projeto.css', null, null, 'all');
		//wp_enqueue_style('programa-projeto');
		
		//construtor
		if(!is_admin()){
			wp_register_style('construtor', STM_THEME_URL . 'classes/assets/css/construtor.css');
			wp_enqueue_style('construtor');
		}
		
		// Portais e Sistemas
		wp_register_script('jquery-mask',  'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js', array ('jquery'), false, false);
		wp_enqueue_script('jquery-mask');

		// Agenda do Secretário
		wp_register_style('agenda-secretario', STM_THEME_URL . 'classes/assets/css/agenda-secretario.css', null, null, 'all');
		wp_enqueue_style('agenda-secretario');
		wp_register_script('moment_with_locales',  STM_THEME_URL . 'classes/assets/js/ion.calendar-2.0.2/js/moment-with-locales.js', array ('jquery'), false, false);
		wp_register_script('ion_calendar',  STM_THEME_URL . 'classes/assets/js/ion.calendar-2.0.2/js/ion.calendar.js', array ('jquery'), false, false);
		wp_enqueue_script('moment_with_locales');
		wp_enqueue_script('ion_calendar');

		wp_register_script('ajax-agenda-secretario',  STM_THEME_URL . 'classes/assets/js/ajax-agenda-secretario.js', array ('jquery'), false, false);
		wp_enqueue_script('ajax-agenda-secretario');
		wp_localize_script('ajax-agenda-secretario', 'bloginfo', array('ajaxurl' => admin_url('admin-ajax.php')));
		add_action('wp_ajax_montaHtmlListaEventos', array(new ArchiveAgendaAjaxCalendario(), 'montaHtmlListaEventos' ));
		add_action('wp_ajax_montaHtmlListaEventos', array(new ArchiveAgendaAjaxCalendarioNew(), 'montaHtmlListaEventos' ));
		add_action('wp_ajax_nopriv_montaHtmlListaEventos', array(new ArchiveAgendaAjaxCalendario(), 'montaHtmlListaEventos'));
		add_action('wp_ajax_nopriv_montaHtmlListaEventos', array(new ArchiveAgendaAjaxCalendarioNew(), 'montaHtmlListaEventos'));

		add_action('wp_ajax_recebeDadosAjax', array(new ArchiveAgendaGetDatasEventos(), 'recebeDadosAjax' ));
		add_action('wp_ajax_nopriv_recebeDadosAjax', array(new ArchiveAgendaGetDatasEventos(), 'recebeDadosAjax'));


		// Contatos SME
		//wp_enqueue_script('jquery-ui-sortable');
		//wp_register_script('ajax-contato-sme',  STM_THEME_URL . 'classes/assets/js/ajax-contato-sme.js', array ('jquery'), false, false);
		//wp_enqueue_script('ajax-contato-sme');
		//add_action('wp_ajax_criaCamposContato', array(new ArchiveContatoMetabox(), 'criaCamposContato' ));
		//add_action('wp_ajax_nopriv_criaCamposContato', array(new ArchiveContatoMetabox(), 'criaCamposContato'));

		//wp_register_style('contatos-sme', STM_THEME_URL . 'classes/assets/css/contatos-sme.css', null, null, 'all');
		//wp_enqueue_style('contatos-sme');

		// Organograma
		//wp_register_style('organograma', STM_THEME_URL . 'classes/assets/css/organograma.css', null, null, 'all');
		//wp_enqueue_style('organograma');
		//wp_register_script('organograma',  STM_THEME_URL . 'classes/assets/js/organograma.js', array ('jquery'), false, false);
		//wp_enqueue_script('organograma');

		// Página Login
		wp_register_style('pagina-login', STM_THEME_URL . 'classes/assets/css/pagina-login.css', null, null, 'all');
		wp_enqueue_style('pagina-login');
		wp_register_script('pagina-login',  STM_THEME_URL . 'classes/assets/js/pagina-login.js', array ('jquery'), false, false);
		//wp_enqueue_script('pagina-login');

		// Breadcrumb
		wp_register_style('breadcrumb', STM_THEME_URL . 'classes/assets/css/breadcrumb.css', null, null, 'all');
		wp_enqueue_style('breadcrumb');

		// Loop Single
		wp_register_style('loop-single', STM_THEME_URL . 'classes/assets/css/loop-single.css', null, null, 'all');
		wp_enqueue_style('loop-single');

		// Mural de recados
		wp_register_script('mural-recados-sme',  STM_THEME_URL . 'classes/assets/js/mural-recados-sme.js', array ('jquery'), false, false);
		wp_enqueue_script('mural-recados-sme');
		
		// Portais e Sistemas
		wp_register_script('portais-sistemas',  STM_THEME_URL . 'classes/assets/js/portais-sistemas.js', array ('jquery'), false, false);
		wp_enqueue_script('portais-sistemas');

		wp_register_script('slick',  STM_THEME_URL . 'classes/assets/js/slick.js', array ('jquery'), false, false);

		// Inscricoes
		//if (is_single()) {
			wp_register_script('valida-inscricao',  STM_THEME_URL . 'classes/assets/js/valida-inscricao.js', array ('jquery'), '1.0.0', true);
			wp_enqueue_script('valida-inscricao');
		//}

		// CSS
		wp_register_style(
			'select2-css', 
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
			array(),
			'4.1.0-rc.0'
		);

		// JS
		wp_register_script(
			'select2-js',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
			array('jquery'), // Dependência do jQuery
			'4.1.0-rc.0',
			true // Carregar no footer
		);

		// JQUERY-UI
		wp_register_script('jquery-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js');

		// ###### CSS SORTEIOS
		wp_register_style('bootstrap-sorteio-css', STM_THEME_URL . 'css/lib/bootstrap-4-2-1.min.css');
		wp_register_style('style-sorteio-css', STM_THEME_URL . 'css/style.css?v=1.0');
		wp_register_style('toggle-sorteio-css', STM_THEME_URL . 'css/lib/bootstrap4-toggle.min.css');
		wp_register_style('sweetalert-sorteio-css', STM_THEME_URL . 'css/lib/sweetalert2.min.css');
		wp_register_style('toastr-sorteio-css', STM_THEME_URL . 'css/lib/toastr.min.css');

		// ###### JS SORTEIOS
		wp_register_script('sweetalert-sorteio-js', STM_THEME_URL . 'js/lib/sweetalert2.all.min.js');
		wp_register_script('toggle-sorteio-js', STM_THEME_URL . 'js/lib/bootstrap4-toggle.min.js');
		wp_register_script('toastr-sorteio-js', STM_THEME_URL . 'js/lib/toastr.min.js');
		wp_register_script('sorteio-js', STM_THEME_URL . 'js/sorteio.js?v=1.2');
		wp_register_script('bootstrap-sorteio-js', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js');
		
		wp_register_style('datatables-css', STM_THEME_URL . 'css/lib/jquery.dataTables.min.css', [], '1.13.6');
		wp_register_script('datatables-js', STM_THEME_URL . 'js/lib/jquery.dataTables.min.js', ['jquery'], '1.13.6', true);
		
		wp_register_style('quilljs', 'https://cdn.quilljs.com/1.3.6/quill.snow.css', [], '1.3.6');
		wp_enqueue_style('quilljs');
		
		wp_register_script('quilljs', 'https://cdn.quilljs.com/1.3.6/quill.js', [], '1.3.6');
		wp_enqueue_script('quilljs');

		
		// ###### WIDGETS
		wp_register_script('widgets', STM_THEME_URL . 'js/widgets.js?v=1.0');
		wp_register_style('widgets-dashboard', STM_THEME_URL . 'css/widgets.css?v=1.0');
	}

	public function custom_formats_admin(){


	}
}

new LoadDependences();