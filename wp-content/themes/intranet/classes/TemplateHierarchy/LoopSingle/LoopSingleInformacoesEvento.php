<?php
namespace Classes\TemplateHierarchy\LoopSingle;

class LoopSingleInformacoesEvento extends LoopSingle
{
    private $tipo_evento;
    
	public function __construct()
	{
        // Tipo do evento (datas, periodo, premiacao)
		$this->tipo_evento = get_field( 'tipo_evento', get_the_ID() );
		$this->getInformacoesEvento();
	}

	public function getInformacoesEvento(){
		

		echo '<div class="col-lg-4 col-sm-12 order-2">';
            echo '<div class="informacoes-evento">';
               
                $resumo = get_field('resumo');
                $link = get_field('link_infos');
                $tituloLink = get_field('texto_do_link');
                $post_type = get_post_type(get_the_ID());

                if($resumo){
                    echo '<p>' . $resumo . '</p>';
                }		

                $dataEvento = obter_datas_evento_formatadas( get_the_ID() );
                $dataLimite = get_field('enc_inscri');
                $genero = get_field('genero_taxo', get_the_ID()); // Tipo de evento
                $duracao = get_field('duracao');
                $class_indicativa = get_field('class_indicativa');
                $local = get_field('local');
                $local_outros = get_field('local_outros');
                $endereco = get_field('endereco');
                $adm = get_field('administracao_ingressos');
		        $links = get_field('links_adicionais');
		        $validarLinks = $this->array_valido($links);

                if ( $this->tipo_evento === 'periodo' ) {
                    $info_periodo_evento = get_field( 'evento_periodo', get_the_ID() );
                }

                if( isset( $info_periodo_evento['descricao'] ) && !empty( $info_periodo_evento['descricao'] ) ){
                    $info_datas = '<strong>Período: ' . esc_html( $info_periodo_evento['descricao'] ) . '</strong>';
                }

                if( $this->tipo_evento === 'data' ){

                    $datas_disponiveis = get_field('evento_datas', get_the_ID());
                    $datas_disponiveis = array_filter($datas_disponiveis);

                    $datas_disponiveis = array_map(function($item){
                        return $item['data'];
                    }, $datas_disponiveis);

                    $datas_disponiveis = array_unique($datas_disponiveis);

                    // Tradução dos dias da semana
                    $dias_semana = [
                        'Sunday' => 'domingo',
                        'Monday' => 'segunda-feira',
                        'Tuesday' => 'terça-feira',
                        'Wednesday' => 'quarta-feira',
                        'Thursday' => 'quinta-feira',
                        'Friday' => 'sexta-feira',
                        'Saturday' => 'sábado',
                    ];

                    $datas_disponiveis = array_map(function($item) use ($dias_semana){

                        $timestamp = strtotime($item);

                        $dia_semana_en = date('l', $timestamp);
                        $dia_semana = $dias_semana[$dia_semana_en];

                        $hora = date('H', $timestamp);
                        $minuto = date('i', $timestamp);

                        // Remove minutos se for "00"
                        $hora_formatada = $minuto === '00'
                            ? "{$hora}h"
                            : "{$hora}h{$minuto}";

                        return date('d/m', $timestamp) . " às {$hora_formatada} – {$dia_semana}";

                    }, $datas_disponiveis);

                    $label = (count($datas_disponiveis) === 1) ? 'Data' : 'Datas';

                    $info_datas = '<strong>' . $label . ':<br>' . implode('<br>', $datas_disponiveis) . '</strong>';
                }

                if( $this->tipo_evento === 'premio' ){
                    $datas_disponivies = get_field('evento_premios');
                    if($datas_disponivies && $datas_disponivies != ''){
                        $info_datas = '<strong>Premiação:</strong>';
                        $info_datas .= '<ul>';
                            foreach ($datas_disponivies as $data) {
                                $info_datas .= '<li><strong>' . $data['premio'] . '</strong></li>';
                            }
                        $info_datas .= '</ul>';
                    }
                }

                $dateLimiteEvento = \DateTime::createFromFormat('Ymd', $dataLimite);
                if($dateLimiteEvento){
					$dateLimiteShow = $dateLimiteEvento->format('d/m/Y');
				}
				
                
                echo '<table>';
                    echo '<tr>';
                        echo '<td class="align-top"><i class="fa fa-question" aria-hidden="true"></i></td>';
                        echo '<td><strong>' . get_the_title() . '</strong></td>';
                    echo '</tr>';

                    echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';

                    if($genero){
                        echo '<tr>';
                            echo '<td class="align-top"><i class="fa fa-ticket" aria-hidden="true"></i></td>';
                            echo '<td><strong>Tipo de Evento: ' . $genero->name . '</strong></td>';
                        echo '</tr>';
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                    }

                    if($info_datas){
                        echo '<tr>';
                            if($this->tipo_evento === 'premio'){
                                echo '<td class="align-top"><i class="fa fa-gift" aria-hidden="true"></i></td>';
                            } else {
                                echo '<td class="align-top"><i class="fa fa-calendar-o" aria-hidden="true"></i></td>';
                            }
                            echo '<td>' . $info_datas . '</td>';
                        echo '</tr>';                        
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                    }

                    if($duracao){
                        echo '<tr>';
                            echo '<td class="align-top"><i class="fa fa-clock-o" aria-hidden="true"></i></td>';
                            echo '<td><strong>Duração: ' . $duracao . '</strong></td>';
                        echo '</tr>';
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                    }

                    if($class_indicativa){
                        echo '<tr>';
                            echo '<td class="align-top"><i class="fa fa-users" aria-hidden="true"></i></td>';
                            echo '<td><strong>Classificação Indicativa: ' . $class_indicativa . '</strong></td>';
                        echo '</tr>';
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                    }

                    if($local && $local != 'outros'){
                        $term = get_term($local);

                        if ($term && !is_wp_error($term)) {
                            echo '<tr>';
                                echo '<td class="align-top"><i class="fa fa-building-o" aria-hidden="true"></i></td>';
                                echo '<td><strong>Local: ' . $term->name . '</strong></td>';
                            echo '</tr>';
                            echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                        }
                        
                    }

                    if($local && $local == 'outros'){
                        echo '<tr>';
                            echo '<td class="align-top"><i class="fa fa-building-o" aria-hidden="true"></i></td>';
                            echo '<td><strong>Local: ' . $local_outros . '</strong></td>';
                        echo '</tr>';
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                    }

                    if($endereco){
                        echo '<tr>';
                            echo '<td class="align-top"><i class="fa fa-map-marker" aria-hidden="true"></i></td>';
                            echo '<td><strong>Endereço: ' . $endereco . '</strong></td>';
                        echo '</tr>';
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                    }

                    if($adm == 'parceiro' && $validarLinks && $link){
                        echo '<tr>';
                            echo '<td class="align-top"><i class="fa fa-link" aria-hidden="true"></i></td>';
                            echo '<td><strong>Link para mais informações:</strong>';
                                echo '<ul><strong>';
                                    echo '<li><a href="' . $link . '" target="_blank">' . ($tituloLink ? $tituloLink : 'Saiba Mais') . '</a></li>';
                                    foreach ($links as $link) {
                                        if (empty($link['link_infos']) && empty($link['texto_do_link'])) {
                                            continue;
                                        } elseif(empty($link['texto_do_link']) && !empty($link['link_infos'])) {
                                            $link['texto_do_link'] = $link['link_infos'];
                                        }
                                        echo '<li><a href="' . $link['link_infos'] . '" target="_blank">' . $link['texto_do_link'] . '</a></li>';
                                    }
                                echo '</strong></ul>';
                            echo '</td>';
                        echo '</tr>';
                       
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';

                    } else {
                        if($link){
                            echo '<tr>';
                                echo '<td class="align-top"><i class="fa fa-link" aria-hidden="true"></i></td>';
                                echo '<td><strong>Link para mais informações: <a href="' . $link . '" target="_blank">' . ($tituloLink ? $tituloLink : 'Saiba Mais') . '</a></strong></td>';
                            echo '</tr>';
                            echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                        }
                    }                    

                    if($dataLimite){
                        echo '<tr>';
                            echo '<td class="align-top"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></td>';
                            echo '<td><strong>Inscrições até: ' . $dateLimiteShow . '</strong></td>';
                        echo '</tr>';
                        echo '<tr><td colspan="2"><span class="divisor"></span></td></tr>';
                    }
                echo '</table>';

                if ( check_usuario_inscrito_evento( get_the_ID() ) ) : 
                    echo '<span class="post-type-tag badge-inscricao">';
                        echo '<i class="fa fa-check-circle" aria-hidden="true"></i> Inscrição realizada';
                    echo '</span>';
                endif;

                if($post_type === 'cortesias'){
                    echo '<span class="post-type-tag cortesia-tag">Cortesia</span>';
                } else {
                    echo '<span class="post-type-tag">Sorteio</span>';
                }
                
            echo '</div>';  
		echo '</div>';

	}	

    public function array_valido($data) {
		if (!is_array($data) || empty($data)) {
			return false;
		}

		foreach ($data as $item) {
			if (!is_array($item)) {
				continue;
			}

			if (
				!empty($item['link_infos']) ||
				!empty($item['texto_do_link'])
			) {
				return true;
			}
		}

		return false;
	}
	
}