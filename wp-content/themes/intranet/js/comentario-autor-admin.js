jQuery(function ($) {
    // 1. Inserir o campo ao clicar em responder (Painel Admin)
    $(document).on('click', '.vim-r', function () {
        setTimeout(function () {
            const $replyRow = $('#replyrow');
            
            // Verifica se o container existe, se o campo já não foi criado e se há dados do ACF
            if (!$replyRow.length || $('#atribuir_autor_id').length || !window.ComentarioAutor) return;

            let options = '<option value="">— Responder como eu —</option>';
            window.ComentarioAutor.users.forEach(function(user) {
                options += `<option value="${user.ID}">${user.display_name}</option>`;
            });

            let selectHtml = `
                <div id="container-atribuir-autor" style="margin: 10px; padding: 10px; border: 1px solid #ccd0d4; background: #f6f7f7;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Responder como:</label>
                    <select id="atribuir_autor_id" style="width:100%;">
                        ${options}
                    </select>
                </div>
            `;

            // Local de inserção confirmado por você
            $replyRow.find('#replysubmit').prepend(selectHtml);
        }, 300);
    });

    // 2. Interceptar o AJAX do WP para incluir o valor do select
    $.ajaxSetup({
        beforeSend: function (xhr, settings) {
            // Verifica se é a ação de resposta do Admin
            if (settings.data && typeof settings.data === 'string' && settings.data.indexOf('action=replyto-comment') !== -1) {
                const selectedAuthor = $('#atribuir_autor_id').val();
                if (selectedAuthor) {
                    settings.data += '&atribuir_autor_id=' + encodeURIComponent(selectedAuthor);
                }
            }
        }
    });
});

jQuery(function ($) {

    function inserirSeletorAutorDashboard() {

        const $replyRow = $('#dashboard_activity #replyrow');

        if (!$replyRow.length) return;

        // Evita duplicar
        if ($replyRow.find('#atribuir_autor_id').length) return;

        let options = '<option value="">— Responder como eu —</option>';

        if (window.ComentarioAutor && window.ComentarioAutor.users) {
            window.ComentarioAutor.users.forEach(function (user) {
                options += `<option value="${user.ID}">${user.display_name}</option>`;
            });
        }

        const selectHtml = `
            <div class="inside atribuir-autor-dashboard" style="margin:10px 0;">
                <label for="atribuir_autor_id" style="display:block;font-weight:600;">
                   Responder como:
                </label>
                <select name="atribuir_autor_id" id="atribuir_autor_id" style="width:100%;max-width:250px;">
                    ${options}
                </select>
            </div>
        `;

        // Insere ANTES dos botões
        $replyRow.find('#replysubmit').before(selectHtml);
    }

    // Quando clicar em "Responder" no painel
    $(document).on('click', '#dashboard_activity .vim-r', function () {
        setTimeout(inserirSeletorAutorDashboard, 100);
    });

});