<!DOCTYPE html>
<?php 

use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;

get_header('conf-cancel-sorteio'); 
//###### É NECESSÁRIO ADICIONAR UMA PÁGINA NO PAINEL DO WORDPRESS COM O NOME: CONFIRMA INSCRICAO SORTEIOS (Ascendente - > Sorteio)

$idInscrito = base64_decode($_REQUEST['ni']); //ID do inscrito do sorteio
//$nomeEvento = base64_decode($_REQUEST['ne']); //ID do inscrito do sorteio

$ret = Envia_Emails_Sorteio_SME::get_inscrito_by_id($idInscrito);
$confirmacao = $ret[0]->confirmou_presenca;
$dataSorteada = $ret[0]->data_sorteada;

$wp_timezone = wp_timezone();
$agora = new DateTime('now', $wp_timezone);
$data = new DateTime($dataSorteada);

$prazo = $ret[0]->prazo_confirmacao;
$prazo_confirmacao = new DateTime($prazo, $wp_timezone);
$formatada = $prazo_confirmacao->format('d/m/Y \à\s H\hi');


//dd($ret);

if($_POST){

    if($agora > $data){ // Data de evento já passou
        header("Location: " .   get_site_url() . '?conf-expirado=1');
        exit;
    } elseif($confirmacao != '0'){ // Inscrição já confirmada ou cancelada
        header("Location: " .   get_site_url() . '?confirmacao=3');
        exit;
    }  elseif($agora > $prazo_confirmacao){ // Prazo para confirmação expirou
        header("Location: " .   get_site_url() . '?conf-expirado=2');
        exit;
    }

    $acao = $_POST['acao'];
    $idInscrito = base64_decode($_REQUEST['ni']); //ID do inscrito do sorteio

    if($acao == 'confirmar'){
        $confirmacao = '1';
        $atualizacao = Envia_Emails_Sorteio_SME::confirma_presenca_inscrito($idInscrito, $confirmacao);        
    } else if ($acao == 'cancelar'){
        $confirmacao = '2';
        $atualizacao = Envia_Emails_Sorteio_SME::confirma_presenca_inscrito($idInscrito, $confirmacao);
    }

    if($atualizacao['res'] == 1){
        header("Location: " .   get_site_url() . '?confirmacao='.$confirmacao);
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

    if($agora > $data){
        header("Location: " .   get_site_url() . '?conf-expirado=1');
        exit;
    } elseif($confirmacao != '0'){
        header("Location: " .   get_site_url() . '?confirmacao=3');
        exit;
    }  elseif($agora > $prazo_confirmacao){
        header("Location: " .   get_site_url() . '?conf-expirado=2');
        exit;
    }
}

?>
<div class="container text-center">
    <div class="logo">
      <img src="https://hom-intranet.sme.prefeitura.sp.gov.br/wp-content/uploads/2022/02/Logo_Educacao.png" alt="Logo Cidade de SP" height="70">
    </div><br>
    
    <?php if($confirmacao == '0'): ?>

        <h2>Confirme sua presença para receber seu ingresso!</h2>
        <p class="description">
            Caso confirme sua presença, você receberá um <strong>novo e-mail com instruções para utilização</strong> do seu ingresso.
        </p>
        <p class="description">
            Se não puder comparecer, <strong>pedimos que cancele sua participação clicando no botão abaixo</strong>. Assim, seu ingresso pode ser disponibilizado para outra pessoa aproveitar o evento.
        </p>
        <p class="description">
            Se você confirmar presença e não comparecer ao evento, poderá ficar impedido de participar de novos sorteios por um determinado período.
        </p>
        <p class="description">
            <strong>Atenção:</strong> o link de confirmação é válido até <strong><?= $formatada; ?></strong>. Após esse prazo, ele expira automaticamente.
        </p>
        <form class="text-center" action="#" method="post">            
            <button type="submit" name="acao" value="confirmar" id="btn-confirma-presenca">Confirmar Presença</button>
            <button type="submit" name="acao" value="cancelar" id="btn-cancela-presenca">Cancelar Participação</button>
        </form>
        <p class="description">
            <strong>Parabéns e obrigado por participar deste sorteio! </strong>
        </p>
        <p>&nbsp;</p>
        <p class="rodape">Equipe Intranet SME-SP</p>

    <?php endif; ?>

</div>