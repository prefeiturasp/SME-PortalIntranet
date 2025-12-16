acf.addAction('ready append', function () {
  console.log('Executando bloqueio de datas ACF');

  if (typeof ACF_BLOQUEIO === 'undefined' || !Array.isArray(ACF_BLOQUEIO.datasBloqueadas)) return;

  $('.acf-repeater .acf-field[data-name="data"]').each(function (index) {
    console.log(`Repetidor encontrado: ${index}`);

    const $hiddenInput = $(this).find('input[type="hidden"]');
    const valor = $hiddenInput.val()?.trim();
    console.log('Valor do hidden input:', valor);

    if (valor && valor.length === 8) {
      const dataFormatada = `${valor.slice(0, 4)}-${valor.slice(4, 6)}-${valor.slice(6, 8)}`;
      console.log('Data formatada:', dataFormatada);

      if (ACF_BLOQUEIO.datasBloqueadas.includes(dataFormatada)) {
        console.log('Data bloqueada, aplicando bloqueio');
        const $textInput = $(this).find('input[type="text"]');
        $textInput.prop('disabled', true);
        $textInput.css({ backgroundColor: '#f5f5f5', cursor: 'not-allowed' });
      }
    }
  });
});