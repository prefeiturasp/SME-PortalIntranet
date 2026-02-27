jQuery(document).ready(function ($) {
// Organograma

$('.borda-conexao').each(function (i, borda) {
    $(borda).parent().addClass(`borda-itens ${filterItems($(borda).attr('class').split(' '), 'bg').replace('bg', 'borda')}`);
});

$('.coordenadoria').each(function (i, coord) {
    const card = $(coord).children('.borda-itens:last-child').children('.card');
    if (card.length == 1) {
        $(card).parent('.borda-itens').removeClass('justify-content-center');
    }
});

function filterItems(elementos, termo) {
    return elementos.filter(function (el) {
        return el.toLowerCase().indexOf(termo.toLowerCase()) > -1;
    }).toString();
}

});