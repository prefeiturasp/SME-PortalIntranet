<?php // @codeCoverageIgnoreStart ?>
<!DOCTYPE html>
<?php

use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;

get_header( 'conf-cancel-sorteio', ['titulo' => 'Confirme sua participação'] ); 
//###### É NECESSÁRIO ADICIONAR UMA PÁGINA NO PAINEL DO WORDPRESS COM O NOME: CONFIRMA INSCRICAO CORTESIA (Ascendente - > Sorteio)

$idInscrito = base64_decode($_REQUEST['ni']); //ID do inscrito do sorteio
$ret = Envia_Emails_Sorteio_SME::get_inscrito_by_id( $idInscrito, 'cortesias' );

$confirmacao = $ret[0]->confirmou_presenca;
$dataSelecionada = get_acf_info_by_id( $ret[0]->acf_id);
$tipo_sorteio = $dataSelecionada['tipo'];

if( $tipo_sorteio === 'periodo' ) {
    $dataSelecionada['info'] = get_field( 'enc_inscri', $dataSelecionada['post_id'] );
}

$wp_timezone = wp_timezone();
$agora = new DateTime('now', $wp_timezone);
$data = ($tipo_sorteio !== 'premio') ? new DateTime( $dataSelecionada['info'] ) : null;

$prazo = $ret[0]->prazo_confirmacao;
$prazo_confirmacao = new DateTime( $prazo, $wp_timezone );
$formatada = $prazo_confirmacao->format('d/m/Y \à\s H\hi');

if($_POST){

    if ( $data && $agora > $data ) { // Data de evento já passou
        header("Location: " .   get_site_url() . '?tipo=cortesia&conf-expirado=1');
        exit;
    } elseif( $confirmacao != '0' ) { // Inscrição já confirmada ou cancelada
        header("Location: " .   get_site_url() . '?tipo=cortesia&confirmacao=3');
        exit;
    }  elseif ( $agora > $prazo_confirmacao ) { // Prazo para confirmação expirou
        header("Location: " .   get_site_url() . '?tipo=cortesia&conf-expirado=2');
        exit;
    }

    $acao = $_POST['acao'];

    if( $acao == 'confirmar' ){
        $confirmacao = '1';
        $atualizacao = Envia_Emails_Sorteio_SME::confirma_presenca_inscrito($idInscrito, $confirmacao, 'cortesia' );        
    } else if ( $acao == 'cancelar' ){
        $confirmacao = '2';
        $atualizacao = Envia_Emails_Sorteio_SME::confirma_presenca_inscrito($idInscrito, $confirmacao, 'cortesia' );
    }

    if( $atualizacao['res'] == 1 ){
        header( "Location: " .   get_site_url() . '?tipo=cortesia&confirmacao='.$confirmacao );
        exit;
    } else {
        echo 
            '<script>
                swal({
                    title: "Erro ao processar sua solicitação!",
                    text: "Por favor, tente novamente mais tarde.",
                    icon: "error",
                    button: "OK",
                }).then((value) => {
                    window.location.href = "'.get_site_url().'";
                });
            </script>';
    }
} else {

    if( $data && $agora > $data ){
        header("Location: " .   get_site_url() . '?tipo=cortesia&conf-expirado=1');
        exit;
    } elseif( $confirmacao != '0' ){
        header("Location: " .   get_site_url() . '?tipo=cortesia&confirmacao=3');
        exit;
    }  elseif( $agora > $prazo_confirmacao ){
        header("Location: " .   get_site_url() . '?tipo=cortesia&conf-expirado=2');
        exit;
    }
}

?>
<div class="container text-center">
    <div class="logo">
      <img src="https://hom-intranet.sme.prefeitura.sp.gov.br/wp-content/uploads/2022/02/Logo_Educacao.png" alt="Logo Cidade de SP" height="70">
    </div><br>
    
    <?php if($confirmacao == '0'): ?>

        <h2>Confirme sua participação!</h2>
        <p class="description">
            Caso confirme, você receberá um <strong>novo e-mail com instruções relacionadas a esta participação.</strong>
        </p>
        <p class="description">
            Se não puder prosseguir, <strong>pedimos que cancele clicando no botão abaixo</strong>. Dessa forma, a oportunidade poderá ser disponibilizada para outro participante.
        </p>
        <p class="description">
            Caso a participação seja confirmada e as instruções não sejam seguidas conforme orientado, você poderá ficar impedido(a) de participar de novas inscrições por um período determinado.
        </p>
        <p class="description">
            <strong>Atenção:</strong> o link de confirmação é válido até <strong><?= $formatada; ?></strong>. Após esse prazo, ele expira automaticamente.
        </p>
        <form class="text-center" action="#" method="post">            
            <button type="submit" name="acao" value="confirmar" id="btn-confirma-presenca">Confirmar Participação</button>
            <button type="submit" name="acao" value="cancelar" id="btn-cancela-presenca">Cancelar Participação</button>
        </form>
        <p class="description">
            <strong>Obrigado por participar! </strong>
        </p>
        <p>&nbsp;</p>
        <p class="rodape">Equipe Intranet SME-SP</p>

    <?php endif; ?>

</div>
<?php // @codeCoverageIgnoreEnd