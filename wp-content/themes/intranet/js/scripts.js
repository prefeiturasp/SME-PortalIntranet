/*Anima Menu
/*Verifico o tamanho da tela, se for maior que 992 crio e add a classe que contem a animação ao hover do li do wp-nav-menu*/
var $s = jQuery.noConflict();

/*Ativacao Hamburger Menu Icons*/
$s(document).ready(function () {
    $s('#nav-icon1, #nav-icon2, #nav-icon3, #nav-icon4').click(function () {
        $s(this).toggleClass('open');
    });
});


/*Scripts para o Botao Voltar ao topo aparecer somente quando tiver rolagem para baixo*/
$s(function () {
    $s(window).scroll(function () {
        if ($s(this).scrollTop() != 0) {
            $s('#toTop').fadeIn();
        } else {
            $s('#toTop').fadeOut();
        }
    });
    $s('#toTop').click(function () {
        $s('body,html').animate({scrollTop: 0}, 800);
    });
});
///////////////////////////////////////////////////////////////////////////////
///////////////////////////icones persona home/////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
function removeBackgroundColor(id_link_atual) {
    $s('.container-a-icones-home').each(function (e) {
        var id_li_atual = this.id;
        if (id_li_atual != id_link_atual) {
            $s(this).css('background-color', '#F6F6F6')
        }
    })
}

$s(document).ready(function () {
    $s(".a-icones-home").each(function (index) {
        $s(this).click(function (e) {
            var id_link_atual = e.currentTarget.id;
            var elemento_pai = $s(this).parent();
            elemento_pai.css('background-color', '#ECECEC');
            removeBackgroundColor(id_link_atual);
			//add hover no avg
			$s(this).on('icones-home').addClass('icones-home-svg');	 		
        });
    });
});
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

// Removendo rodape Hand-Talk{
$s("._2l9ogse-9T4ParAkBl58xA").waitUntilExists(function (e) {
    var link_rodape = $s('._2l9ogse-9T4ParAkBl58xA').contents().find('.ht-ac-copy');
    link_rodape.remove();
});

// Removendo cabecalho twitter
$s("iframe#twitter-widget-0").waitUntilExists(function (e) {
    var iframeBody = $s("iframe#twitter-widget-0").contents().find('body');
    var timeline = iframeBody.find('.timeline-Widget');
    timeline.find('.timeline-Header').remove();
    timeline.find('.twitter-timeline').remove();

});

// Fechando Janela Galeria ao clicar na Tab swipebox-overlay
$s(document).ready(function () {
    $s(".gallery-item").on('click ', function (e) {
        $s("body").keydown(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
                var bt_close = $s('#swipebox-close');
                bt_close.trigger('click');
            }
        });
    });

});

/*
$s(document).ready(function(){
    $s("._2p3a").removeAttr( 'style' );
    $s("._2p3a").css({"min-width": "180px", " width:": "540px"});

});*/

// Trocando texto em ingles das midias sociais do plugin addThis
$s("span.at4-visually-hidden").waitUntilExists(function (e) {

    var texto = $s(this).text();

    if (texto === 'AddThis Sharing Buttons') {
        $s(this).remove();
    }
    if (texto === "Share to WhatsApp") {
        $s(this).text("Compartilhar com WhatsApp")
    }

    if (texto === "Share to Facebook") {
        $s(this).text("Compartilhar com Facebook")
    }

    if (texto === "Share to Twitter") {
        $s(this).text("Compartilhar com Twitter")
    }

    if (texto === "Share to Imprimir") {
        $s(this).text("Imprimir este conteúdo")
    }

    //var texto_alterado = texto.replace('Share to', 'Compartilhar com');
    //$s(this).text(texto_alterado);
});

$s("span.at-icon-wrapper > svg > title").waitUntilExists(function (e) {
    var texto = $s(this).text();
    if (texto === "Print") {
        $s(this).text("Imprimir")
    }
});

/*Ativação do Tool Tip Bootstrap*/
$s(document).ready(function () {
    $s(function () {
        $s('[data-toggle="tooltip"]').tooltip({html: true})
    });
});

$s(document).ready(function () {
    $s(function () {
        $s('img').addClass('img-fluid');
    });

    $s('#categoria').change(function() {
        //Use $option (with the "$") to see that the variable is a jQuery object
        var $option = $s(this).find('option:selected');
        //Added with the EDIT
        var value = $option.val();//to get content of "value" attrib
        //var text = $option.text();//to get <option>Text</option> content
        if(value == 'portais'){
            $s("#data-ini").prop("disabled", true);
            $s("#data-end").prop("disabled", true);
        } else {
            $s("#data-ini").removeAttr('disabled');
            $s("#data-end").removeAttr('disabled');
        }
        //alert(value);
    });
});

$s(".a-icones-home").click(function() {
    if ($s(this).hasClass('active')) {
        var href = $s(this).attr('href');
        $s(href).toggleClass('active');
    } else {
        $s(".a-icones-home").removeClass('exibe');
    }
    $s(this).toggleClass('exibe');
});

/* Ativacao Wow*/
new WOW().init();

$s('#telefone').mask('(00) 00000-0000');

$s('.check-search').click(function() {
    $s('.check-search').not(this).prop('checked', false);
});

/* Scripts da tabela de listagem dos sorteados */
$s(document).ready(function () {
    $s('table.datatables').each(function () {

        const $table = $s(this);
        const count = $table.find('tbody tr').length;

        $table.DataTable({
            pageLength: 5,
            lengthChange: false,
            ordering: false,
            paging: count >= 5,
            searching: count >= 5,
            info: false,
            stripeClasses: [],
            autoWidth: false,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
                searchPlaceholder: 'Sorteados',
            }
            
        });

        $table.removeClass('dataTable');
    });
});
