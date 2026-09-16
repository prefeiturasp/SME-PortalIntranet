export class Menu_Agenda_Eventos_SME_Localizadores {
	// Item principal do menu Agenda de Eventos SME
	menu_agenda_eventos_sme = () => '#menu-posts-agenda'
	link_menu_agenda_eventos_sme = () => '#menu-posts-agenda > a'

	// Submenus
	submenu_todos_os_eventos = () =>
		'#menu-posts-agenda .wp-submenu-wrap a[href="edit.php?post_type=agenda"]'
	submenu_novo_item = () =>
		'#menu-posts-agenda .wp-submenu-wrap a[href="post-new.php?post_type=agenda"]'
	submenu_compromissos = () =>
		'#menu-posts-agenda .wp-submenu-wrap a[href="edit-tags.php?taxonomy=compromisso_sec&post_type=agenda"]'
	submenu_enderecos = () =>
		'#menu-posts-agenda .wp-submenu-wrap a[href="edit-tags.php?taxonomy=endereco_sec&post_type=agenda"]'
}

export class Lista_Agenda_Eventos_SME_Localizadores {
	// Cabeçalho da página
	titulo_pagina = () => '#wpbody-content .wrap > h1.wp-heading-inline'
	botao_novo_item = () => '#wpbody-content .wrap > a.page-title-action'

	// Opções de tela
	botao_opcoes_tela = () => '#show-settings-link'
	painel_opcoes_tela = () => '#screen-options-wrap'
	checkbox_coluna_autor = () => '#author-hide'
	checkbox_coluna_data = () => '#date-hide'
	checkbox_coluna_data_evento = () => '#data_evento-hide'
	checkbox_coluna_quantidade_eventos = () => '#qtd_evento-hide'
	checkbox_coluna_tipo = () => '#post_type-hide'
	campo_itens_por_pagina = () => '#edit_agenda_per_page'
	radio_visualizacao_compacta = () => '#list-view-mode'
	radio_visualizacao_ampliada = () => '#excerpt-view-mode'
	botao_aplicar_opcoes_tela = () => '#screen-options-apply'

	// Filtros por status
	filtro_todos = () => '.subsubsub .all a'
	filtro_meus = () => '.subsubsub .mine a'
	filtro_publicados = () => '.subsubsub .publish a'
	filtro_rascunhos = () => '.subsubsub .draft a'
	filtro_lixeira = () => '.subsubsub .trash a'

	// Caixa de busca
	campo_busca = () => '#post-search-input'
	botao_buscar = () => '#search-submit'

	// Ações em massa
	select_acoes_massa_top = () => '#bulk-action-selector-top'
	botao_aplicar_massa_top = () => '#doaction'
	select_acoes_massa_bot = () => '#bulk-action-selector-bottom'
	botao_aplicar_massa_bot = () => '#doaction2'

	// Filtro da listagem
	select_categoria_filtro = () =>
		'.tablenav.top .actions select.postform'
	botao_filtrar = () => '#post-query-submit'

	// Paginação
	input_pagina_atual = () => '#current-page-selector'
	info_total_paginas = () => '.total-pages'
	botao_pagina_anterior = () => '.prev-page'
	botao_primeira_pagina = () => '.first-page'
	botao_pagina_proxima = () => '.next-page'
	botao_ultima_pagina = () => '.last-page'
	contador_itens = () => '.displaying-num'

	// Seleção de eventos
	checkbox_selecionar_todos_top = () => '#cb-select-all-1'
	checkbox_selecionar_todos_bot = () => '#cb-select-all-2'
	checkbox_evento_por_id = (id) => `#cb-select-${id}`

	// Tabela de eventos
	formulario_lista = () => '#posts-filter'
	tabela_eventos = () => '.wp-list-table.posts'
	linhas_eventos = () => '#the-list > tr'
	celulas_eventos = () => '#the-list > tr > td'
	linha_por_id = (id) => `#post-${id}`
	link_titulo_por_id = (id) => `#post-${id} .row-title`

	// Colunas e cabeçalhos
	cabecalho_titulo = () => 'th#title a'
	cabecalho_autor = () => 'th#author'
	cabecalho_data = () => 'th#date a'
	cabecalho_data_evento = () => 'th#data_evento a'
	cabecalho_quantidade_eventos = () => 'th#qtd_evento'
	coluna_titulo = () => '#the-list .column-title'
	coluna_autor = () => '#the-list .column-author'
	coluna_data = () => '#the-list .column-date'
	coluna_data_evento = () => '#the-list .column-data_evento'
	coluna_quantidade_eventos = () => '#the-list .column-qtd_evento'

	// Ações de cada evento
	link_editar_por_id = (id) => `#post-${id} .row-actions .edit a`
	botao_edicao_rapida_por_id = (id) =>
		`#post-${id} .row-actions .editinline`
	link_lixeira_por_id = (id) => `#post-${id} .row-actions .trash a`
	link_visualizar_por_id = (id) => `#post-${id} .row-actions .view a`
	link_duplicar_por_id = (id) =>
		`#post-${id} .row-actions .edit_as_new_draft a`
	link_lixeira = () => '#the-list .row-actions .trash a'
	link_restaurar = () => '#the-list .row-actions .untrash a'
	link_excluir_permanentemente = () =>
		'#the-list .row-actions .delete a'

	// Edição rápida
	linha_edicao_rapida = () => '#inline-edit'
	campo_titulo_edicao_rapida = () =>
		'#inline-edit input[name="post_title"]'
	campo_slug_edicao_rapida = () => '#inline-edit input[name="post_name"]'
	campo_dia_edicao_rapida = () => '#inline-edit input[name="jj"]'
	select_mes_edicao_rapida = () => '#inline-edit select[name="mm"]'
	campo_ano_edicao_rapida = () => '#inline-edit input[name="aa"]'
	campo_hora_edicao_rapida = () => '#inline-edit input[name="hh"]'
	campo_minuto_edicao_rapida = () => '#inline-edit input[name="mn"]'
	select_autor_edicao_rapida = () =>
		'#inline-edit select[name="post_author"]'
	checkbox_privado_edicao_rapida = () =>
		'#inline-edit input[name="keep_private"]'
	checkbox_comentarios_edicao_rapida = () =>
		'#inline-edit input[name="comment_status"]'
	select_status_edicao_rapida = () => '#inline-edit select[name="_status"]'
	select_tipo_post_edicao_rapida = () => '#inline-edit #pts_post_type'
	botao_salvar_edicao_rapida = () => '#inline-edit .save'
	botao_cancelar_edicao_rapida = () => '#inline-edit .cancel'

	// Edição em massa
	formulario_edicao_massa = () => '#bulk-edit'
	select_autor_edicao_massa = () =>
		'#bulk-edit select[name="post_author"]'
	select_comentarios_edicao_massa = () =>
		'#bulk-edit select[name="comment_status"]'
	select_status_edicao_massa = () => '#bulk-edit select[name="_status"]'
	select_tipo_post_edicao_massa = () => '#bulk-edit #pts_post_type'
	botao_salvar_edicao_massa = () => '#bulk_edit'
	botao_cancelar_edicao_massa = () => '#bulk-edit .cancel'

	// Mensagens
	mensagem_sucesso = () => '#message'
}

export class Adicionar_Agenda_Eventos_SME_Localizadores {
	// Opções de tela
	botao_opcoes_de_tela = () => '#show-settings-link'
	painel_opcoes_de_tela = () => '#screen-options-wrap'
	checkbox_imagem_destacada = () => '#postimagediv-hide'
	checkbox_agenda_eventos_sme = () => '#acf-group_67d9b9ce49fc6-hide'
	checkbox_resumo = () => '#postexcerpt-hide'
	checkbox_discussao = () => '#commentstatusdiv-hide'
	checkbox_slug = () => '#slugdiv-hide'
	checkbox_autor = () => '#authordiv-hide'
	radio_uma_coluna = () => 'input[name="screen_columns"][value="1"]'
	radio_duas_colunas = () => 'input[name="screen_columns"][value="2"]'
	checkbox_editor_tela_cheia = () => '#editor-expand-toggle'

	// Painéis
	painel_publicar = () => '#submitdiv'
	painel_imagem_destacada = () => '#postimagediv'
	painel_agenda_eventos_sme = () => '#acf-group_67d9b9ce49fc6'
	painel_resumo = () => '#postexcerpt'
	painel_discussao = () => '#commentstatusdiv'
	painel_slug = () => '#slugdiv'
	painel_autor = () => '#authordiv'

	// Formulário principal
	campo_titulo = () => '#title'
	checkbox_tipo_de_data = () => '#acf-field_67d9b9ce73be3'
	select_tipo_de_evento = () => '#acf-field_67e546bb44fbe'
	campo_data_do_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73c67"] input.hasDatepicker'
	campo_data_final = () =>
		'.acf-field[data-key="field_67d9b9ce73ca8"] input.hasDatepicker'

	// Eventos do dia
	campo_eventos_do_dia = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"]'
	botao_adicionar_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] .acf-actions a[data-event="add-row"]'
	linhas_eventos = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] table.acf-table > tbody > tr.acf-row:not(.acf-clone)'
	botao_adicionar_linha_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) a[data-event="add-row"]'
	botao_remover_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) a[data-event="remove-row"]'

	// Campos dos eventos adicionados
	select_compromisso = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4c66"] select'
	campo_nome_compromisso = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4ccf"] input[type="text"]'
	campo_hora_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4d21"] input.input'
	campo_fim_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4d6c"] input.input'
	campo_descricao_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4db7"] textarea'
	select_endereco_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4e33"] select'
	campo_endereco_manual = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4ea1"] input[type="text"]'
	botao_adicionar_midia_participantes = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4ee7"] .insert-media'
	iframe_participantes_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4ee7"] iframe'
	textarea_participantes_evento = () =>
		'.acf-field[data-key="field_67d9b9ce73ce7"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_67d9b9ceb4ee7"] textarea.wp-editor-area'

	// Visão dos grupos de usuários
	painel_grupos_usuarios = () => '#acf-group_620bb83997f6c'
	toggle_servidores_publicos = () => '#acf-field_620bb843d5755'
	toggle_profissionais_rede_parceira = () => '#acf-field_620bb86cd5757'

	// Imagem destacada
	botao_definir_imagem_destacada = () => '#set-post-thumbnail'

	// Resumo, discussão, slug e autor
	campo_resumo = () => '#excerpt'
	checkbox_permitir_comentarios = () => '#comment_status'
	checkbox_permitir_pingbacks = () => '#ping_status'
	campo_slug = () => '#post_name'
	select_autor = () => '#post_author_override'

	// Ações de publicação
	botao_salvar_rascunho = () => '#save-post'
	botao_visualizar = () => '#post-preview'
	botao_publicar = () => '#publish'

	// Mensagens e link permanente
	mensagem_sucesso = () => '#message'
	mensagem_obrigatoriedade = () => '.acf-error-message'
	link_permanente = () => '#sample-permalink a'
}
