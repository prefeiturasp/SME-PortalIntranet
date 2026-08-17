<?php

namespace Classes\Curriculos;
use Classes\Lib\SimpleXLSXGenExp;
use ExportadorCurriculos;
use Inscricao;

class ExportarBuscaAtiva
{

    private $busca;

    public function __construct( BuscaAtiva $busca ) {
        $this->busca = $busca;
    }

    private function montarCabecalho(): array {

        return [
            'Nome',
            'Nome Social',
            'RF',
            'Whatsapp',
            'E-mail',
            'Cargo',
            'DRE de Exercício',
            'Currículo atualizado em',
            'Em Processo Seletivo',
        ];

    }

    private function montarLinhas(array $curriculos): array {

        $curriculos = Inscricao::adicionar_processo_ativo( $curriculos );

        require get_template_directory()
            . '/includes/oportunidades/dados/mapeamentos.php';

        $linhas = [];

        foreach ( $curriculos as $curriculo ) {

            if ( isset( $curriculo['cargo_outro'] ) && !empty( $curriculo['cargo_outro'] ) ) {
                $outro_cargo = ucfirst( strtolower( $curriculo['cargo_outro'] ) );
                $curriculo['cargo_efetivo'] = str_replace( "Outro", $outro_cargo, $curriculo['cargo_efetivo'] );
            }

            $cargos = json_decode(
                $curriculo['cargo_efetivo'],
                true
            );

            $linhas[] = [

                ExportadorCurriculos::estilizarLinha($curriculo['nome_completo']),
                ExportadorCurriculos::estilizarLinha($curriculo['nome_social'] ?: '-'),
                ExportadorCurriculos::estilizarLinha($curriculo['rf']),
                ExportadorCurriculos::estilizarLinha(formatarTelefone($curriculo['telefone_whatsapp'])),
                ExportadorCurriculos::estilizarLinha($curriculo['email_principal']),

                !empty($cargos)
                    ? ExportadorCurriculos::estilizarLinha(implode(', ', $cargos))
                    : ExportadorCurriculos::estilizarLinha('-'),                

                ExportadorCurriculos::estilizarLinha(
                    $MAPEAMENTO_OPCOES_DRES[
                        $curriculo['dre_exercicio']
                    ] ?? $curriculo['dre_exercicio']
                ),

                ExportadorCurriculos::estilizarLinha(
                    date(
                        'd/m/Y H:i',
                        strtotime($curriculo['atualizado_em'])
                    )
                ),

                ExportadorCurriculos::estilizarLinha( !is_null( $curriculo['processo_ativo'] ) ? $curriculo['processo_ativo']['descricao'] : 'Não' ),

            ];

        }

        return $linhas;

    }

    public function exportar() {

        require_once get_template_directory() . '/classes/Lib/SimpleXLSXGen.php';

        $resultado = $this->busca->obterCurriculos(
            [],
            1,
            20,
            true
        );

        $curriculos = $resultado['dados'];

        $dados = [];     
        
        $infoCabecalho = 'Relatório de Candidatos da Busca Ativa | Extraído em ' . (new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')))->format('d/m/Y \à\s H:i');

        $linhaTitulo = [
            sprintf(
                '<style font-size="22" bgcolor="#0E2841" height="60" color="#FFFFFF" align="center" valign="center"><center><middle><b>%s</b></middle></center></style>',
                $infoCabecalho
            )
        ];

        /*
        * Completa a linha até o número de colunas
        */
        $linhaTitulo = array_pad(
            $linhaTitulo,
            count($this->montarCabecalho()),
            ''
        );

        $dados[] = $linhaTitulo;

        $dados[] = array_map(
            fn ($valor) => ExportadorCurriculos::estilizarCabecalho($valor, 40),
            $this->montarCabecalho()
        );

        $dados = array_merge(
            $dados,
            $this->montarLinhas($curriculos)
        );     

        $xlsx = SimpleXLSXGenExp::fromArray($dados);

        $xlsx->setDefaultFont('Aptos Narrow');

        $totalColunas = count($dados[1]);

        $ultimaColuna = $xlsx->num2name($totalColunas);

        $xlsx->mergeCells(
            "A1:{$ultimaColuna}1"
        );

        $agora = obter_data_com_timezone( 'd_m_Y_H_i', 'America/Sao_Paulo' );

        $xlsx->downloadAs(
            sprintf(
                'Relatório_de_Candidatos_da_Busca Ativa_Extraído_em_%s.xlsx',
                $agora
            )
        );

        exit;

    }

}