<?php

namespace Classes\Curriculos;
use Classes\Lib\SimpleXLSXGenExp;

class ExportarBuscaAtiva
{

    private $busca;

    public function __construct(
        BuscaAtiva $busca
    ) {
        $this->busca = $busca;
    }

    private function montarCabecalho(): array {

        return [
            'Nome',
            'RF',
            'Whatsapp',
            'E-mail',
            'Cargo',
            'DRE de Exercício',
            'Currículo atualizado em',
            'Status',
        ];

    }

    private function montarLinhas(array $curriculos): array {

        require get_template_directory()
            . '/includes/oportunidades/dados/mapeamentos.php';

        $linhas = [];

        foreach ($curriculos as $curriculo) {

            $cargos = json_decode(
                $curriculo['cargo_efetivo'],
                true
            );

            $linhas[] = [

                ExportadorCurriculos::estilizarLinha($curriculo['nome_completo']),
                ExportadorCurriculos::estilizarLinha($curriculo['rf']),
                ExportadorCurriculos::estilizarLinha($curriculo['telefone_whatsapp']),
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

                ExportadorCurriculos::estilizarLinha('-'), // Status

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
            [ExportadorCurriculos::class, 'estilizarCabecalho'],
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

        $xlsx->downloadAs(
            sprintf(
                'Busca Ativa - %s.xlsx',
                date('d-m-Y')
            )
        );

        exit;

    }

}