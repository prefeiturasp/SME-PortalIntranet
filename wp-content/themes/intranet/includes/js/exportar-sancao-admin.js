jQuery(document).ready(function($) {    
    $("#relatorio-sancoes").click(function(e) {
        e.preventDefault();
        //alert("Exportação não implementada.");
        
        const button = $(this);
        
        button.prop("disabled", true);
        toastr.info('Gerando relatório, aguarde...');
        
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "exportar_sancoes_excel",
                _ajax_nonce: exportVarsSancao.nonce
            },
            xhrFields: {
                responseType: "blob"
            },
            success: function(data) {
                const filename = `relatorio_sancoes_${exportVarsSancao.dataAtual}.xlsx`;
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
                console.error("Erro na exportação:", xhr.responseText);
                toastr.warning('Erro ao gerar o relatório. Tente novamente.')
            },
            complete: function() {
                setTimeout(function() {
                    button.prop("disabled", false);
                }, 3000);
            }
        });
        
    });
});