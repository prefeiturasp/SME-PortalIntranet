export class Adicionar_Pagina_Localizadores {
	// Opções de Tela
	botao_opcoes_de_tela = () => '#show-settings-link'
	checkbox_atributos_pagina = () => '#pageparentdiv-hide'
	checkbox_imagem_destacada = () => '#postimagediv-hide'
	checkbox_discussao = () => '#commentstatusdiv-hide'
	checkbox_slug = () => '#slugdiv-hide'
	checkbox_autor = () => '#authordiv-hide'
	radio_uma_coluna = () => 'input[name="screen_columns"][value="1"]'
	radio_duas_colunas = () => 'input[name="screen_columns"][value="2"]'
	checkbox_editor_tela_cheia = () => '#editor-expand-toggle'

	// Formulário de Página
	campo_titulo = () => '#title'
	iframe_conteudo_visual = () => '#content_ifr'
	textarea_conteudo_codigo = () => '#content'
	botao_adicionar_midia = () => '#insert-media-button'

	// Atributos da Página
	select_ascendente = () => '#parent_id'
	select_modelo = () => '#page_template'
	campo_ordem = () => '#menu_order'

	// Imagem Destacada
	botao_definir_imagem_destacada = () => '#set-post-thumbnail'

	// Discussão
	checkbox_permitir_comentarios = () => '#comment_status'
	checkbox_permitir_pingbacks = () => '#ping_status'

	// Slug e Autor
	campo_slug = () => '#post_name'
	select_autor = () => '#post_author_override'

	// Ações de Publicação
	botao_salvar_rascunho = () => '#save-post'
	botao_visualizar = () => '#post-preview'
	botao_publicar = () => '#publish'
	botao_mover_para_lixeira = () => '.submitdelete'

	// Mensagens
	mensagem_conexao_perdida = () => '#lost-connection-notice'
	mensagem_sucesso = () => '#message'
	mensagem_local_storage = () => '#local-storage-notice'

	// Ajuda contextual
	botao_ajuda = () => '#contextual-help-link'
	aba_sobre_paginas = () => '#tab-link-about-pages'
	aba_inserir_midia = () => '#tab-link-inserting-media'
	aba_atributos_pagina = () => '#tab-link-page-attributes'
	conteudo_aba_ativa = () => '.help-tab-content.active'

	// Agendamento de Publicação
	botao_editar_data = () => '.edit-timestamp'
	select_mes = () => '#mm'
	campo_dia = () => '#jj'
	campo_ano = () => '#aa'
	campo_hora = () => '#hh'
	campo_minuto = () => '#mn'
	botao_ok_data = () => '.save-timestamp'

	//Link Permanente
	link_permanente = () => '#sample-permalink a'
}

export class Visualizar_Pagina_Publicada_Localizadores {
	// ===== Cabeçalho / Acessibilidade =====
	link_ir_conteudo = () => 'a[accesskey="1"]#1[href="#aer-fugiat"]'
	link_ir_menu = () => 'a[accesskey="2"]#2[href="#irmenu"]'
	link_ir_busca = () => 'a[accesskey="3"]#3[href="#search-front-end"]'
	link_ir_rodape = () => 'a[accesskey="4"]#4[href="#irrodape"]'

	// ===== Branding / Logo =====
	logo_topo = () => '.logo-principal .logo-topo a.brand img[alt*="Logotipo"]'

	// ===== Busca (topo) =====
	campo_busca_topo = () => '#search-front-end'
	botao_busca_topo = () => '#enviar-busca-home'
	form_busca_topo = () =>
		'form[action="https://hom-intranet.sme.prefeitura.sp.gov.br/"][method="get"]'

	// ===== Menu principal =====
	botao_toggle_menu_desktop = () =>
		'.menu-topo .navbar-toggler[aria-controls="irmenu"]'
	container_menu_principal = () => '#irmenu'
	itens_menu_principal = () => '#menu-menu-superior-parceiras > li > a.nav-link'

	// ===== Notificações / Perfil =====
	botao_notificacoes = () => '#navbarDropdownNotificacoes'
	dropdown_notificacoes = () =>
		'.dropdown-menu[aria-labelledby="navbarDropdownNotificacoes"]'
	perfil_menu = () => '.profile-menu .profile-menus .dropdown .user-action'
	perfil_nome = () => '.profile-menu .profile-menus .dropdown .user-action span'
	perfil_avatar = () =>
		'.profile-menu .profile-menus .dropdown .user-action img[alt="Imagem de perfil"]'

	// ===== Título e Conteúdo da Página =====
	// h1 principal da página tem id "#aer-fugiat" e classe "mb-4"
	titulo_principal = () => 'section.container h1.mb-4'
	// Conteúdo principal na coluna de 9 colunas dentro do #conteudo
	conteudo_principal = () => '#conteudo .col-lg-9.col-xs-12'
	// Primeiro parágrafo de conteúdo (se precisar validar texto exato)
	primeiro_paragrafo_conteudo = () =>
		'#conteudo .col-lg-9.col-xs-12 p:first-of-type'

	// ===== Rodapé =====
	rodape = () => 'footer.mt-3'
	rodape_ancora = () => '#irrodape'
	rodape_titulo_secretaria = () => 'footer .footer-title'
	rodape_telefone = () => 'footer .fa-phone ~ a[href^="tel:"]'
	rodape_email = () => 'footer .fa-envelope ~ a[href^="mailto:"]'
	rodape_redes = () => 'footer .redes-footer .rede-rodape a'

	// ===== Subfooter e Voltar ao topo =====
	subfooter_texto = () => '.subfooter .container .row .col-sm-12.text-center p'
	voltar_topo = () => '#toTop'

	// ===== Admin bar (WordPress logado) =====
	wp_admin_bar = () => '#wpadminbar'
	wp_admin_editar_pagina = () =>
		'#wp-admin-bar-edit > a[href*="post.php?post="]'
	wp_admin_duplicar_pagina = () => '#wp-admin-bar-new-draft > a'

	// ===== Fallback de Erro (caso página 404/erro genérico) =====
	erro_404 = () => '.error, .error-404, .page-404'

	// ===== Mídias sociais do topo (header cinza claro) =====
	header_midias_sociais = () => '.cabecalho-cinza-claro .midias-sociais a'

	// ===== Menu mobile =====
	botao_menu_mobile = () => '.mobile-menu .btn.btn-menu[data-target="#menu"]'
	modal_menu_mobile = () => '#menu.modal.left.fade .modal-content'
	itens_menu_mobile = () =>
		'#menu .menu-menu-superior-container #menu-menu-superior > li > a.nav-link'
}

export class Categorias_Localizadores {
	// Opcoes de tela
	botao_opcoes_de_tela = () => '#show-settings-link'
	checkbox_descricao = () => '#description-hide'
	checkbox_slug = () => '#slug-hide'
	checkbox_contagem = () => '#posts-hide'
	campo_itens_por_pagina = () => '#edit_categorias-noticias_per_page'
	botao_aplicar_opcoes_de_tela = () => '#screen-options-apply'

	// Busca de categorias
	campo_busca = () => '#tag-search-input'
	botao_pesquisar = () => '#search-submit'

	// Formulario para adicionar categoria
	formulario_adicionar_categoria = () => '#addtag'
	campo_nome = () => '#tag-name'
	campo_slug = () => '#tag-slug'
	select_categoria_ascendente = () => '#parent'
	campo_descricao = () => '#tag-description'
	botao_adicionar_categoria = () => '#submit'
	mensagem_retorno = () => '#ajax-response'

	// Formulario para editar categoria
	botao_editar_categoria = () => '[type="submit"]'

	// Lista de categorias
	formulario_lista = () => '#posts-filter'
	tabela_categorias = () => '.wp-list-table.tags'
	lista_categorias = () => '#the-list'
	linhas_categorias = () => '#the-list > tr'
	linha_categoria = (id) => `#tag-${id}`
	link_nome_categoria = () => '#the-list .row-title'
	coluna_descricao = () => '.column-description'
	coluna_slug = () => '.column-slug'
	coluna_contagem = () => '.column-posts'
	checkbox_selecionar_todas = () => '#cb-select-all-1'
	checkbox_categoria = (id) => `#cb-select-${id}`

	// Acoes de cada categoria
	link_editar = () => '.row-actions .edit a'
	botao_edicao_rapida = () => '.row-actions .editinline'
	link_excluir = () => '.row-actions .delete-tag'
	link_visualizar = () => '.row-actions .view a'

	// Formulario de edicao da categoria
	campo_nome_edicao = () => '#name'
	campo_descricao_edicao = () => '#description'
	botao_salvar_categoria = () => '#submit'

	// Acoes em massa e paginacao
	select_acao_em_massa = () => '#bulk-action-selector-top'
	botao_aplicar_acao_em_massa = () => '#doaction'
	campo_pagina_atual = () => '#current-page-selector'
	total_de_itens = () => '.tablenav.top .displaying-num'
	total_de_paginas = () => '.tablenav.top .total-pages'

	// Edicao rapida
	formulario_edicao_rapida = () => 'tr.inline-edit-row:visible'
	campo_nome_edicao_rapida = () =>
		'tr.inline-edit-row:visible input[name="name"]'
	campo_slug_edicao_rapida = () =>
		'tr.inline-edit-row:visible input[name="slug"]'
	botao_atualizar_categoria = () => 'tr.inline-edit-row:visible .save'
	botao_cancelar_edicao_rapida = () => 'tr.inline-edit-row:visible .cancel'
	mensagem_erro_edicao_rapida = () => 'tr.inline-edit-row:visible .notice-error'
}
