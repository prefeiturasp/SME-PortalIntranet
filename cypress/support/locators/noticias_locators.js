export class Adicionar_Noticia_Localizadores {
	// Tela de Opções
	botao_opcoes_de_tela = () => '#show-settings-link'
	checkbox_categorias = () => '#categorias-noticiasdiv-hide'
	checkbox_imagem_destacada = () => '#postimagediv-hide'
	checkbox_resumo = () => '#postexcerpt-hide'
	checkbox_discussao = () => '#commentstatusdiv-hide'
	checkbox_slug = () => '#slugdiv-hide'
	checkbox_autor = () => '#authordiv-hide'
	radio_uma_coluna = () => 'input[name="screen_columns"][value="1"]'
	radio_duas_colunas = () => 'input[name="screen_columns"][value="2"]'
	checkbox_editor_tela_cheia = () => '#editor-expand-toggle'

	// Formulário de Post
	campo_titulo = () => '#title'
	campo_subtitulo = () => '#acf-field_653180a042e9c'
	iframe_conteudo_visual = () => '#content_ifr'
	textarea_conteudo_codigo = () => '#content'
	botao_adicionar_midia = () => '#insert-media-button'

	// Categorias
	aba_todas_categorias = () => '#categorias-noticias-all'
	aba_mais_usadas = () => '#categorias-noticias-pop'
	toggle_adicionar_categoria = () => '#categorias-noticias-add-toggle'
	campo_nova_categoria = () => '#newcategorias-noticias'
	botao_submit_categoria = () => '#categorias-noticias-add-submit'

	// Imagem destacada
	botao_definir_imagem_destacada = () => '#set-post-thumbnail'

	// Resumo e Discussão
	campo_resumo = () => '#excerpt'
	checkbox_permitir_comentarios = () => '#comment_status'
	checkbox_permitir_pingbacks = () => '#ping_status'

	// Slug e Autor
	campo_slug = () => '#post_name'
	select_autor = () => '#post_author_override'

	// Ações de publicação
	botao_salvar_rascunho = () => '#save-post'
	botao_visualizar = () => '#post-preview'
	botao_publicar = () => '#publish'

	//Mensagens
	mensagem_sucesso = () => '#message'
	mensagem_obrigatoriedade = () => '.acf-error-message'

	//Link Permanente
	link_permanente = () => '#sample-permalink a'
}

export class Menu_Noticias_Localizadores {
	// Item principal do menu Notícias
	menu_noticias = () => '#menu-posts-noticia'

	// Link que abre a lista de submenus
	link_menu_noticias = () => '#menu-posts-noticia > a'

	// Submenu: Todos as Notícias
	submenu_todas_as_noticias = () =>
		'#menu-posts-noticia .wp-submenu-wrap a.wp-first-item'

	// Submenu: Adicionar Notícia
	submenu_adicionar_noticia = () =>
		'#menu-posts-noticia .wp-submenu-wrap a[href="post-new.php?post_type=noticia"]'

	// Submenu: Categorias de Notícias
	submenu_categorias = () =>
		'#menu-posts-noticia .wp-submenu-wrap a[href^="edit-tags.php?taxonomy=categorias-noticias"]'
}

export class Visualizar_Noticia_Publicada_Localizadores {
	// Banner da página de notícia
	banner_imagem = () => '.bn_fx_banner'
	titulo_banner = () => '.bn_fx_banner h1'

	// Conteúdo principal da notícia
	data_publicacao = () => '.content-article .data .display-autor'
	titulo_principal = () => '.content-article .titulo-noticia-principal'
	subtitulo = () => '.content-article .sub-titulo'
	conteudo_principal = () => '.content-article p:nth-of-type(2)'

	// Erro
	erro_404 = () => '.error'

	// Lista de notícias recentes (seção lateral)
	titulo_noticias_recentes = () => '.news-recents h3'
	lista_noticias_recentes = () => '.noticias-recentes .recado'

	// Seção de comentários
	titulo_comentarios = () => '.news-comment .rel-title h2'
	campo_comentario = () => '#comment'
	botao_enviar_comentario = () => '#submit'
	mensagem_obrigatoria_comentario = () => '.comment-form .acf-error-message'
}

export class Lista_Noticias_Localizadores {
	// Barra superior / Opções de tela
	botao_opcoes_tela = () => '#show-settings-link'
	aba_opcoes_tela = () => '#screen-options-wrap'
	botao_aplicar_opcoes_tela = () => '#screen-options-apply'

	// Caixa de busca
	campo_busca = () => '#post-search-input'
	botao_buscar = () => '#search-submit'

	// Ações em massa (topo)
	select_acoes_massa_top = () => '#bulk-action-selector-top'
	botao_aplicar_massa_top = () => '#doaction'

	// Ações em massa (rodapé)
	select_acoes_massa_bot = () => '#bulk-action-selector-bottom'
	botao_aplicar_massa_bot = () => '#doaction2'

	// Filtros por categoria
	select_categoria_filtro = () => '#categorias-noticias'
	botao_filtrar = () => '#post-query-submit'
	acessar_lixos = () => '.subsubsub .trash a'

	// Paginação
	input_pagina_atual = () => '#current-page-selector'
	info_total_paginas = () => '.total-pages'
	botao_pagina_proxima = () => '.next-page'
	botao_pagina_ultima = () => '.last-page'
	contador_itens = () => '.displaying-num'

	// Seletor "marcar todos"
	checkbox_selecionar_todos_top = () => '#cb-select-all-1'
	checkbox_selecionar_todos_bot = () => '#cb-select-all-2'

	// Tabela de posts
	tabela_posts = () => '.wp-list-table.posts'
	linhas_posts = () => '#the-list > tr'
	celula_posts = () => '#the-list > tr > td'
	linha_por_id = (id) => `#post-${id}`

	// Colunas / cabeçalhos clicáveis
	cabecalho_titulo = () => 'th#title a'
	cabecalho_autor = () => 'th#author'
	cabecalho_categorias = () => 'th#categorias'
	cabecalho_comentarios = () => 'th#comments a'
	cabecalho_data = () => 'th#date a'

	// Checkbox individual de cada post
	checkbox_post_por_id = (id) => `#cb-select-${id}`

	// Ações de linha (hover)
	link_editar_por_id = (id) => `#post-${id} .row-actions .edit a`
	link_visualizar_por_id = (id) => `#post-${id} .row-actions .view a`
	link_lixeira_por_id = (id) => `#post-${id} .row-actions .trash a`
	link_lixeira = () => `.row-actions .trash a`
	excluir_permanentemente = () => `.delete .submitdelete`

	// Inline-edit / Quick edit
	linha_edicao_rapida = () => '#inline-edit'
	campo_titulo_quick_edit = () => '#inline-edit input[name="post_title"]'
	campo_slug_quick_edit = () => '#inline-edit input[name="post_name"]'
	select_autor_quick_edit = () => '#inline-edit select[name="post_author"]'
	select_status_quick_edit = () => '#inline-edit select[name="_status"]'
	botao_salvar_quick_edit = () => '#inline-edit .save'
	botao_cancelar_quick_edit = () => '#inline-edit .cancel'

	// Bulk-edit
	form_bulk_edit = () => '#bulk-edit'
	botao_salvar_bulk_edit = () => '#bulk_edit'
	botao_cancelar_bulk_edit = () => '#bulk-edit .cancel'

	//Mensagens
	mensagem_sucesso = () => '#message'
}
