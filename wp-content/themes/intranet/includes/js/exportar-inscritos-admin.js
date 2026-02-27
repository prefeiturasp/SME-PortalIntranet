jQuery(document).ready(function($) {    
    $("#exportar-inscritos").click(function(e) {
        e.preventDefault();
        const button = $(this);
        const status = $("#export-status");
        
        button.prop("disabled", true);
        status.text("Gerando relatório...").removeClass("error");
        
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "exportar_inscritos_excel",
                post_id: button.data("post-id"),
                _ajax_nonce: exportVarsInscri.nonce
            },
            xhrFields: {
                responseType: "blob"
            },
            success: function(data) {
                const filename = `relatorio_inscritos_${exportVarsInscri.dataAtual}.xlsx`;
                const url = window.URL.createObjectURL(data);
                const a = document.createElement("a");
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                toastr.success('Relatório gerado com sucesso!');
            },
            error: function(xhr) {
                status.text("Erro ao gerar relatório").addClass("error");
                console.error("Erro na exportação:", xhr.responseText);
                toastr.warning('Erro ao gerar o relatório. Tente novamente.')
            },
            complete: function() {
                setTimeout(function() {
                    status.text("");
                    button.prop("disabled", false);
                }, 3000);
            }
        });
    });
});