<?php
namespace Classes\TemplateHierarchy\LoopSingle;

class LoopSingleFormInscricao extends LoopSingle
{
	private $tipo_evento;

	public function __construct()
	{
		// Tipo do evento (datas, periodo, premiacao)
		$this->tipo_evento = get_field( 'tipo_evento', get_the_ID() );
		
	}

    public function getFormInscri() {

        $user_id = get_current_user_id();
        $current_date = obter_data_com_timezone('Ymd', 'America/Sao_Paulo');
        $dataLimite = get_field('enc_inscri');
        $parceira = get_field('parceira', 'user_' . $user_id);
        $this->tipo_evento = get_field('tipo_evento');

        if($dataLimite >= $current_date){

            if ($_SERVER["REQUEST_METHOD"] === "POST") { //requisição POST
                global $wpdb;

                // Sanitização e captura dos dados do formulário
                $user_id = get_current_user_id(); // Pega o ID do usuário logado (se aplicável)
                $cpf = isset($_POST['cpf']) ? preg_replace('/\D/', '', $_POST['cpf']) : ''; // Remove caracteres não numéricos
                $post_id = get_the_ID(); // ID do post atual
                $nome_completo = sanitize_text_field($_POST['nomeComp']);
                $email_institucional = sanitize_email($_POST['emailInsti']);
                $email_secundario = sanitize_email($_POST['emailSec']);
                $celular = sanitize_text_field($_POST['celular']);
                $dre = sanitize_text_field($_POST['dre']);
                $telefone_comercial = sanitize_text_field($_POST['telCom']);
                $cargo_principal = sanitize_text_field($_POST['cargo_principal']);
                $unidade_setor = sanitize_text_field($_POST['uniSetor']);
                $disciplina = sanitize_text_field($_POST['disciplina']);
                $ciente = isset($_POST['ciente']) ? 1 : 0;
                $remanescentes = isset($_POST['remanescentes']) ? 1 : 0;
                $datas_escolhidas = ( isset( $_POST['datas'] ) && is_array( $_POST['datas'] ) ) ?  $_POST['datas'] : [];

                // Verifica se o CPF já está inscrito para este post/evento
                $tabela = $wpdb->prefix . "inscricoes";
                $existe = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $tabela WHERE post_id = %d AND cpf = %s",
                    $post_id, $cpf
                ));

                if ($existe) {
                    echo "<script>
                        jQuery(document).ready(function ($) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Você já está inscrito neste sorteio!',
                                text: 'Agora é só aguardar e torcer. Boa sorte!',
                                confirmButtonText: 'Fechar',
                            });
                        });
                    </script>";
                    //return;
                } else {

                    // Insere os dados na tabela
                    $insert = $wpdb->insert(
                        $tabela,
                        [
                            'user_id' => $user_id ?: null,
                            'cpf' => $cpf,
                            'post_id' => $post_id,
                            'nome_completo' => $nome_completo,
                            'email_institucional' => $email_institucional,
                            'email_secundario' => $email_secundario,
                            'celular' => $celular,
                            'dre' => $dre,
                            'telefone_comercial' => $telefone_comercial ?: null,
                            'cargo_principal' => $cargo_principal,
                            'unidade_setor' => $unidade_setor,
                            'disciplina' => $disciplina ?: null,
                            'ciente' => $ciente,
                            'remanescentes' => $remanescentes,
                            'data_inscricao' => current_time('mysql')
                        ],
                        [
                            '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s'
                        ]
                    );

                    if ($insert) {

                        $inscricao_id = $wpdb->insert_id;

                        if ( $datas_escolhidas ) {
                            $datas = array_map( 'sanitize_text_field', $datas_escolhidas );							

                            foreach ( $datas as $data ) {
                                $wpdb->insert(
                                    $wpdb->prefix . 'inscricao_datas',
                                    [
                                        'inscricao_id' => $inscricao_id,
                                        'data_evento'  => $data,
                                    ],
                                    ['%d', '%s']
                                );
                            }
                        }
                        
                        $tipo_usuario = $parceira ? 'parceira' : 'servidor';
                        echo $this->exibe_mensagem_por_tipo_usuario( 'cadastro_realizado', $tipo_usuario );

                    } else {
                        echo "<script>
                            jQuery(document).ready(function ($) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro ao salvar a inscrição',
                                    text: 'Houve um erro ao salvar a inscrição. Tente novamente, caso o problema persista entre em contato com o intranet.beneficios@sme.prefeitura.sp.gov.br.',
                                    confirmButtonText: 'Fechar',
                                });
                            });
                        </script>";
                    }

                }

                
            } // fim requisição POST

            
            //if(!$parceira){
                $post_id = get_the_ID();

                // Verifica se o usuário já está cadastrado no sorteio atual
                global $wpdb;
                $tabela_inscricoes = $wpdb->prefix . 'inscricoes'; // Substitua pelo nome da sua tabela
                $usuario_cadastrado = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $tabela_inscricoes WHERE user_id = %s AND post_id = %d",
                    $user_id, $post_id
                ));

                $ativo = false;
                $tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes';
                $cpf = get_field('cpf', 'user_' . $user_id);
                $cpf = preg_replace('/\D/', '', $cpf);
                
                $sancao_usuario = $wpdb->get_var($wpdb->prepare(
                    "SELECT data_validade FROM $tabela_sancoes WHERE cpf = %s",
                    $cpf
                ));

                if ($sancao_usuario) {
                    $hoje = new \DateTime('today', new \DateTimeZone('America/Sao_Paulo')); 
                    $validade = new \DateTime($sancao_usuario, new \DateTimeZone('America/Sao_Paulo'));

                    if ($validade >= $hoje) {
                        $ativo = true;
                        $validadeMaisUm = clone $validade;
                        $validadeMaisUm->modify('+1 day');
                        $dataPermissao = $validadeMaisUm->format('d/m/Y');
                    }
                }

                // Retorna a resposta
                if ($usuario_cadastrado && !$parceira && !$ativo) { // usuario ja cadastrado
                    
                    $dataSorteio = obter_proxima_data_sorteio( get_the_ID() );		
                    $dateTime = \DateTime::createFromFormat('Ymd', $dataSorteio);
                    
                    
                    //if(!$this->verificaExibicaoListaSorteados()){ ?>
                        <div class="msg-encerrado text-center">
                            <div class="icone-sucesso">
                                <i class="fa fa-check-square" aria-hidden="true"></i>
                            </div>
                            <h3>Sua inscrição foi realizada com sucesso!</h3>
                            <p>Agora é só aguardar e torcer, o sorteio será realizado <strong><?= $dataSorteio ?>,</strong><br>
                            e a lista de ganhadores será divulgada nesta página e por e-mail. Fique atento!</p>
                            <button id="cancelarInscricao" class="btn btn-cancela-inscri mb-4">Cancelar Inscrição</button>
                        </div>
                    <?php //}
                } elseif($ativo && !$parceira) { // usuario com sação
                    ?>
                        <div class="msg-encerrado text-center">
                            <div class="icone-alerta">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            </div>
                            <h3>Atenção!</h3>
                            <p>Você está impedido de se inscrever em qualquer sorteio, devido à sua ausência em um evento anterior. Você poderá participar de novos sorteios a partir de <?= $dataPermissao; ?></p>
                        </div>
                    <?php					
                } else { ?>

                    <div class="form-inscri">
                        <div class="form-title">
                            <h3>Preencha o formulário abaixo com seus dados:</h3>
                        </div>

                        <form action="<?= get_the_permalink(); ?>" method="post" id="form-inscri">				

                            <div class="form-row">
                                <?php
                                if( !$parceira ) {
                                    $nome = esc_html(get_user_meta($user_id, 'first_name', true)) . ' ' . esc_html(get_user_meta($user_id, 'last_name', true));
                                    $user_data = get_userdata($user_id);
                                    $user_email = $user_data->user_email;
                                }
                                ?>
                                <div class="form-group col-12 col-md-6">
                                    <label for="nomeComp">Nome completo <span>*</span></label>
                                    <?php if(!$parceira): ?>
                                        <input type="text" class="form-control" id="nomeComp" value="<?= $nome; ?>" disabled>
                                        <input type="hidden" name="nomeComp" value="<?= $nome; ?>">
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="nomeComp" id="nomeComp" placeholder="Insira seu nome completo">
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group col-12 col-md-6">
                                    <?php if(!$parceira): ?>
                                        <label for="emailInsti">E-mail Institucional ou de Uso Principal <span>*</span></label>
                                        <?php if ($user_email && strpos($user_email, '@sme.prefeitura') !== false) : ?>								
                                            <input type="email" name="emailInstiDisa" class="form-control" id="emailInsti" value="<?= $user_email; ?>" disabled>
                                            <input type="hidden" name="emailInsti" value="<?= $user_email; ?>">
                                        <?php else: ?>
                                            <input type="email" name="emailInsti" class="form-control" id="emailInsti" placeholder="email@sme.prefeitura.sp.gov.br">
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <label for="emailInsti">E-mail Principal <span>*</span></label>
                                        <input type="email" name="emailInsti" class="form-control" id="emailInsti" placeholder="Insira seu e-mail principal">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <?php
                                if( !$parceira ){
                                    $cpf = get_field( 'cpf', 'user_' . $user_id );
                                    $celular = get_field('celular', 'user_' . $user_id);

                                    if( $cpf ){
                                        $cpf = preg_replace('/[^0-9]/', '', $cpf);
                                        $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
                                    }
                                }
                                ?>
                                <div class="form-group col-12 col-md-6">
                                    <label for="emailSec">E-mail Secundário <span>*</span></label>
                                    <?php if ($user_email && strpos($user_email, '@sme.prefeitura') == false && !$parceira) : ?>								
                                        <input type="email"  name="emailSec" class="form-control" id="emailSec" value="<?= $user_email; ?>">
                                    <?php else: ?>
                                        <input type="email"  name="emailSec" class="form-control" id="emailSec" placeholder="email@provedor.com.br">
                                    <?php endif; ?>
                                </div>

                                <div class="form-group col-12 col-md-3">
                                    <label for="cpf">CPF <span>*</span></label>
                                    <?php if ( ($cpf && !$parceira) && strlen($cpf) < 14 ): ?>
                                        <input type="text" name="cpf" class="form-control" id="cpf" value="<?= $cpf; ?>">
                                    <?php elseif($cpf && !$parceira): ?>
                                        <input type="text" name="cpfDisa" class="form-control" id="cpf" value="<?= $cpf; ?>" disabled>
                                        <input type="hidden" name="cpf" value="<?= $cpf; ?>">
                                    <?php else: ?>
                                        <input type="text" name="cpf" class="form-control" id="cpf" placeholder="000.000.000-00">
                                    <?php endif; ?>
                                </div>

                                <div class="form-group col-12 col-md-3">
                                    <label for="celular">Telefone Celular <span>*</span></label>
                                    <?php if($celular && !$parceira): ?>
                                        <input type="text" name="celular" class="form-control" id="celular" placeholder="(00) 0000-0000" value="<?= $celular; ?>">
                                    <?php else: ?>
                                        <input type="text" name="celular" class="form-control" id="celular" placeholder="(00) 0000-0000">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="telCom">Telefone Comercial</label>
                                    <input type="text" name="telCom" class="form-control" id="telCom" placeholder="(00) 0000-0000">
                                </div>

                                <?php
                                
                                    $dre = '';
                                    
                                    if(!$parceira)
                                        $dre = get_field('dre', 'user_' . $user_id);
                                    
                                    $dres = array(
                                        "SME",
                                        "DRE Butantã",
                                        "DRE Campo Limpo",
                                        "DRE Capela do Socorro",
                                        "DRE Freguesia/Brasilândia",
                                        "DRE Guaianases",
                                        "DRE Ipiranga",
                                        "DRE Itaquera",
                                        "DRE Jaçanã/Tremembé",
                                        "DRE Penha",
                                        "DRE Pirituba",
                                        "DRE Santo Amaro",
                                        "DRE São Mateus",
                                        "DRE São Miguel",
                                        "Outros"
                                    );
                                ?>
                                <div class="form-group col-12 col-md-6">
                                    <label for="dre">DRE/SME <span>*</span></label>

                                    <select class="form-control" name="dre" name="dre" id="dre">
                                        <option value="">-- Selecione --</option>
                                        <?php foreach ($dres as $opcao) : ?>
                                            <option value="<?php echo esc_attr($opcao); ?>" <?php selected($opcao, $dre); ?>>
                                                <?php echo esc_html($opcao); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <?php 
                                    if(!$parceira)
                                        $cargo = get_field('cargo_principal', 'user_' . $user_id);
                                ?>
                                <div class="form-group col-12 col-md-6">
                                    <label for="cargo_principal">Cargo atual <span>*</span></label>
                                    <input type="text" name="cargo_principal" class="form-control" id="cargo_principal" placeholder="Cargo Atual" value="<?= $cargo; ?>">
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <?php 
                                        if(!$parceira)
                                            $local = get_field('local', 'user_' . $user_id);
                                    ?>
                                    <label for="uniSetor">Unidade Escolar ou Setor <span>*</span></label>
                                    <input type="text" name="uniSetor" class="form-control" id="uniSetor" placeholder="Nome da Unidade Escolar ou Setor" value="<?= $local; ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="disciplina">Se professor indicar a disciplina que leciona</label>
                                    <input type="text" name="disciplina" class="form-control" id="disciplina" placeholder="Insira o nome da disciplina que leciona">
                                </div>							
                            </div>

                            <?php if ( $this->tipo_evento === 'data' && $datas_disponivies = get_field('evento_datas') ) : ?>
                                <div class="form-row">
                                    <div class="form-group col-12" id="grupo-datas">

                                        <?php if ($this->tipo_evento == 'premio') : ?>
                                            <label for="datas">Selecione os prêmios que deseja participar do sorteio: <span>*</span></label>
                                        <?php else : ?>
                                            <label for="datas">Selecione a(s) data(s) que deseja participar: <span>*</span></label>
                                        <?php endif; ?>
                                        
                                        <div class="datas-select my-3">
                                            <?php
                                            $dias_semana = [
                                                'Sunday' => 'domingo',
                                                'Monday' => 'segunda-feira',
                                                'Tuesday' => 'terça-feira',
                                                'Wednesday' => 'quarta-feira',
                                                'Thursday' => 'quinta-feira',
                                                'Friday' => 'sexta-feira',
                                                'Saturday' => 'sábado',
                                            ];
                                            ?>
                                            <?php foreach ( $datas_disponivies as $data ) : ?>
                                                <?php
                                                $data_hora = $data['data']; // formato Y-m-d H:i:s
                                                $disponivel = verifica_disponibilidade_data_inscricao(get_the_id(), $data_hora);

                                                if ( $disponivel ) :
                                                    $id_input = 'data-' . preg_replace('/[^0-9]/', '', $data_hora); // remove caracteres não numéricos
                                                    $label_formatada = date('d/m/Y H\hi', strtotime($data_hora)); // dd/mm/aaaa hh(h)mm
                                                ?>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            name="datas[]"
                                                            value="<?php echo esc_attr( $data_hora ); ?>"
                                                            id="<?php echo esc_attr( $id_input ); ?>"
                                                        >
                                                        <?php
                                                            $timestamp = strtotime($data_hora);
                                                            $hora = date('H', $timestamp);
                                                            $minutos = date('i', $timestamp);
                                                            $dia_semana = $dias_semana[date('l', $timestamp)] ?? '';

                                                            if ($minutos === '00') {
                                                                $label_formatada = date('d/m', $timestamp) . " às {$hora}h, {$dia_semana}";
                                                            } else {
                                                                $label_formatada = date('d/m', $timestamp) . " às {$hora}h{$minutos}, {$dia_semana}";
                                                            }
                                                        ?>
                                                        <label class="form-check-label" for="<?php echo esc_attr( $id_input ); ?>">
                                                            <?php echo esc_html( $label_formatada ); ?>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>							
                                </div>
                            <?php endif; ?>

                            <?php if ( $this->tipo_evento === 'premio' && $datas_disponivies = get_field('evento_premios') ) : ?>
                                <div class="form-row">
                                    <div class="form-group col" id="grupo-datas">

                                        <?php if ($this->tipo_evento == 'premio') : ?>
                                            <label for="datas">Selecione os prêmios que deseja participar do sorteio: <span>*</span></label>
                                        <?php else : ?>
                                            <label for="datas">Selecione a(s) data(s) que deseja participar: <span>*</span></label>
                                        <?php endif; ?>

                                        <div class="datas-select my-3">
                                            <?php foreach ( $datas_disponivies as $data ) : ?>
                                                <?php
                                                $data_hora = $data['data']; // formato Y-m-d H:i:s
                                                $disponivel = verifica_disponibilidade_data_inscricao(get_the_id(), $data_hora, true);

                                                if ( $disponivel ) :
                                                    $id_input = 'data-' . preg_replace('/[^0-9]/', '', $data_hora); // remove caracteres não numéricos												
                                                ?>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            name="datas[]"
                                                            value="<?php echo esc_attr( $data_hora ); ?>"
                                                            id="<?php echo esc_attr( $id_input ); ?>"
                                                        >													
                                                        <label class="form-check-label" for="<?php echo esc_attr( $id_input ); ?>">
                                                            <?php echo $data['premio']; ?>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>							
                                </div>
                            <?php endif; ?>

                            <?php if ( $this->tipo_evento === 'periodo' ) : ?>
                                <div class="form-row px-1 pt-2 pb-4">
                                    <span class="texto-apoio">Participe do sorteio informando os dados acima e, caso seja sorteado(a), poderá utilizar seu ingresso durante o período destacado na descrição do evento.</span>
                                </div>
                            <?php endif; ?>

                            <div class="form-row">
                                <div class="form-group col">

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="ciente" value="1" id="ciente">
                                        <label class="form-check-label termos" for="ciente">
                                            Declaro estar ciente de que, em caso de contemplação, poderei ser contatado(a) por e-mail para confirmar minha participação ou a retirada do benefício, dentro do prazo estabelecido pelo parceiro. A não confirmação dentro do prazo poderá resultar na perda do direito ao benefício.
                                        </label>
                                    </div>
                                    
                                    <?php /*
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remanescentes" value="1" id="remanescentes">
                                        <label class="form-check-label" for="remanescentes">
                                            Tenho disponibilidade para concorrer a eventuais ingressos remanescentes de outros sorteios que ocorrem essa semana para os quais não fiz a inscrição.
                                        </label>
                                    </div>
                                    */ ?>

                                </div>							
                            </div>

                            <div class="buttons-group text-right">
                                <a href="javascript:history.back()" class="btn btn-outline mr-4">Voltar</a> 
                                <input type="submit" value="Enviar" class="btn btn-principal" id="botaoEnviar">
                            </div>
                            

                        </form>
                        
                    </div>
                    
                <?php
                    

                } // fim usuario ja cadastrado

            //}				

        } else {
                
            if(!$this->verificaExibicaoListaSorteados()){

                $dataSorteio = obter_ultima_data_sorteio( get_the_ID() );
                echo '<div class="msg-encerrado text-center">';
                    echo '<div class="icone-alerta">';
                        echo '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
                    echo '</div>';
                    echo '<h3>Inscrições Encerradas!</h3>';
                    echo '<p>O sorteio será realizado ' . $dataSorteio . ', <br>a lista de ganhadores será divulgada nesta página. Fique atento!</p>';
                echo '</div>';

            }

        };
    }

    private function exibe_mensagem_por_tipo_usuario( string $tipo_mensagem, string $tipo_usuario ) {
		$mensagem = '';
		switch ( $tipo_mensagem ) {
			case 'cadastro_realizado':

				if ( $tipo_usuario === 'parceira' ) {

					$mensagem = "<script>
						jQuery(document).ready(function ($) {
			
							if (window.history.replaceState) {
								window.history.replaceState(null, null, window.location.href);
							}
			
							Swal.fire({
								icon: 'success',
								title: 'Inscrição realizada com sucesso!',
								html: '<p>Agora é só aguardar e torcer. Boa sorte!</p><p>Caso deseje cancelar sua inscrição, acesse novamente a mesma notícia do sorteio, informe o <strong>CPF</strong> no formulário de inscrição e siga as instruções exibidas.</p>',
								confirmButtonText: 'Fechar',
                                confirmButtonColor: '#14447C'
							});
						});
					</script>";

				} else {
					$mensagem = "<script>
						jQuery(document).ready(function ($) {
			
							if (window.history.replaceState) {
								window.history.replaceState(null, null, window.location.href);
							}
			
							Swal.fire({
								icon: 'success',
								title: 'Inscrição realizada com sucesso!',
								html: '<p>Agora é só aguardar e torcer. Boa sorte!</p><p>Caso deseje cancelar sua inscrição, acesse novamente a mesma notícia do sorteio, siga as instruções exibidas e faça o cancelamento.</p>',
								confirmButtonText: 'Fechar',
                                confirmButtonColor: '#14447C'
							});
						});
					</script>";
				}
				
				break;
			
			default:
				# code...
				break;
		}

		return $mensagem;
		
	}

    public function verificaExibicaoListaSorteados(){
		$post_id = get_the_id();
		$exibicaoPagina = get_post_meta($post_id, 'exibe_resultado_pagina', true);
		if ($exibicaoPagina == '1') {
			return true;
		} else {
			return false;
		}
	}
}