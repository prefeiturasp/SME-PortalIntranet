<?php
namespace Classes\Curriculos;
require_once get_template_directory() . '/includes/oportunidades/dados/helpers.php';
require_once get_template_directory() . '/includes/oportunidades/dados/mapeamentos.php';

class ExportadorCurriculos
{
    /**
     * Estrutura do currículo utilizada para montar
     * cabeçalhos e linhas da planilha.
     *
     * @var array
     */
    
    private array $estrutura;

    /**
     * Registra as actions do WordPress.
     */
    public static function init(): void
    {
        add_action(
            'admin_post_exportar_curriculos_excel',
            [self::class, 'download']
        );
    }

    public function __construct()
    {
        $this->estrutura = require get_template_directory() . '/includes/oportunidades/dados/estrutura-curriculo.php';
    }

    /**
     * Callback da action do WordPress.
     */
    public static function download(): void
    {        
        if (empty($_GET['oportunidade_id'])) {
            wp_die('Oportunidade não informada.');
        }

        self::exportar(
            absint($_GET['oportunidade_id'])
        );
    }

    /**
     * Ponto de entrada da exportação.
     */
    public static function exportar(int $oportunidade_id): void  {
        $exportador = new self();

        $exportador->executar($oportunidade_id);
    }

    /**
     * Executa a exportação.
     *
     * @param int $oportunidade_id
     * @return void
     */
    private function executar(int $oportunidade_id): void {
        $dados = Oportunidade::obter_dados_exportacao($oportunidade_id);

        if (empty($dados)) {
            wp_die('Nenhum currículo encontrado para exportação.');
        }

        $linhas = $this->montarLinhas($dados);

        $this->gerarExcel(
            $linhas,
            $oportunidade_id
        );
    }

    /**
     * Monta todas as linhas.
     */
    private function montarLinhas(array $dados): array {

        $linhas = [];        

        foreach ($dados['inscricoes'] as $inscricao) {

            $curriculo_id = (int) $inscricao['curriculo_id'];

            $curriculo = $dados['curriculos'][$curriculo_id] ?? [];

            $vivencias = $dados['vivencias'][$curriculo_id] ?? [];

            $informatica = $dados['informatica'][$curriculo_id] ?? [];

            $comportamental = $dados['comportamental'][$curriculo_id] ?? [];

            $visualizacao = [];
            $visualizacao['visualizar_curriculo'] = $curriculo['visualizar_curriculo'] ?? '';
            $visualizacao['sugestoes'] = $curriculo['sugestoes'] ?? '';

            $etapa = $inscricao['status'] ?? '';

            $linhas[] = $this->montarLinha(
                $inscricao,
                $curriculo,
                $vivencias,
                $informatica,
                $comportamental,
                $visualizacao,
                $etapa
            );
        }

        return $linhas;
    }

    /**
     * Monta uma linha.
     */
    private function montarLinha(
        array $inscricao,
        array $curriculo,
        array $vivencias,
        array $informatica,
        array $comportamental,
        array $visualizacao,
        string $etapa
    ): array {

        $linha = [];

        foreach ($this->estrutura as $secaoKey => $secao) {

            switch ($secaoKey) {

                case 'vivencias':

                    $this->adicionarVivencias(
                        $linha,
                        $vivencias
                    );

                    continue 2;

                case 'tecnologia':

                    $this->adicionarInformatica(
                        $linha,
                        $informatica
                    );

                    continue 2;

                case 'comportamental':

                    $this->adicionarComportamental(
                        $linha,
                        $comportamental
                    );

                    continue 2;

                case 'visualizacao':

                    $this->adicionarVisualizacao(
                        $linha,
                        $visualizacao
                    );

                    continue 2;

                case 'etapa':

                    $this->adicionarEtapa(
                        $linha,
                        $etapa
                    );

                    continue 2;
            }

            foreach ($secao['subsecoes'] as $subsecao) {

                foreach ($subsecao['campos'] as $nomeCampo => $config) {

                    if ($nomeCampo === 'cargo_outro') {
                        continue;
                    }

                    $linha[] = $this->obterValorCampo(
                        $nomeCampo,
                        $curriculo
                    );

                }

            }

        }

        return $linha;

    }

    /**
     * Aplica a estilização padrão aos cabeçalhos da planilha.
     *
     * @param string $texto
     * @return string
     */
    public static function estilizarCabecalho(string $texto): string {
        return sprintf(
            '<style font-size="11" bgcolor="#4E95D9" color="#FFFFFF" align="center" height="90" valign="center" border="thin" bordercolor="000000"><center><wraptext><middle><b>%s</b></middle></wraptext></center></style>',
            $texto
        );
    }

    /**
     * Aplica a estilização padrão as linhas da planilha.
     *
     * @param string $texto
     * @return string
     */
    public static function estilizarLinha(string $texto): string {
        return sprintf(
            '<style font-size="11" bgcolor="#A6CAEC" align="center" valign="center" border="thin" bordercolor="000000"><center><wraptext><middle>%s</middle></wraptext></center></style>',
            $texto
        );
    }

    /**
     * Monta automaticamente o cabeçalho.
     */
    private function montarCabecalho(): array {
        $cabecalho = [];

        foreach ($this->estrutura as $chaveSecao => $secao) {

            switch ($chaveSecao) {

                case 'vivencias':

                    $this->adicionarCabecalhoVivencias($cabecalho);

                    continue 2;

                case 'tecnologia':
                    
                    $this->adicionarCabecalhoInformatica($cabecalho);

                    continue 2;

                case 'comportamental':

                    $this->adicionarCabecalhoComportamental(
                        $cabecalho
                    );

                    continue 2;

                case 'visualizacao':

                    $this->adicionarCabecalhoVisualizacao(
                        $cabecalho
                    );

                    continue 2;

                case 'etapa':

                    $this->adicionarCabecalhoEtapa(
                        $cabecalho
                    );

                    continue 2;

            }

            

            foreach ($secao['subsecoes'] as $subsecao) {

                foreach ($subsecao['campos'] as $nomeCampo => $campo) {

                    // Ignora o campo cargo_outro, pois ele é tratado dentro do cargo_efetivo
                    if ($nomeCampo === 'cargo_outro') {
                        continue;
                    }

                    $cabecalho[] = $this->estilizarCabecalho(
                        $campo['label']
                    );

                }

            }

        }

        return $cabecalho;
    }

    /**
     * Gera o arquivo Excel.
     *
     * @param array $linhas
     * @param int   $oportunidade_id
     *
     * @return void
     */
    private function gerarExcel(
        array $linhas,
        int $oportunidade_id
    ): void {

        $dados = [];

        /*
        * Cabeçalho do relatório
        */
        $infoCabecalho = $this->montarTituloRelatorio($oportunidade_id);

        $linhaTitulo = [
            sprintf(
                '<style font-size="22" bgcolor="#215F9A" height="60" color="#FFFFFF" align="left" valign="center"><middle><b>%s</b></middle></style>',
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

        /*
        * Cabeçalho das colunas
        */
        $dados[] = $this->montarCabecalho();

        /*
        * Dados
        */
        foreach ($linhas as $linha) {
            $dados[] = $linha;
        }

        $xlsx = Classes\Lib\SimpleXLSXGenExp::fromArray($dados);
        $xlsx->setDefaultFont('Aptos Narrow');
        $xlsx->setColWidth(1, 30);
        $xlsx->setColWidth(2, 30);
        $xlsx->setColWidth(3, 18);

        $totalColunas = count($dados[1]);

        $ultimaColuna = $xlsx->num2name($totalColunas);

        $xlsx->mergeCells(
            "A1:{$ultimaColuna}1"
        );

        $xlsx->downloadAs(
            'curriculos-oportunidade-' . $oportunidade_id . '.xlsx'
        );

        exit;
    }

    /**
     * Monta o título do relatório.
     */
    private function montarTituloRelatorio(
        int $oportunidade_id
    ): string {

        $post = get_post($oportunidade_id);
        $titulo = $post->post_title;

        return sprintf(
            '%s | Extraído em %s',
            $titulo,
            (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))
                ->format('d/m/Y \à\s H:i')
        );

    }


    /**
     * Retorna o valor formatado de um campo do currículo.
     *
     * @param string $campo
     * @param array  $curriculo
     *
     * @return string
     */
    private function obterValorCampo(
        string $campo,
        array $curriculo
    ): string {

        switch ($campo) {

            case 'data_nascimento':

                if (empty($curriculo[$campo])) {
                    return $this->estilizarLinha('—');
                }

                return $this->estilizarLinha(date('d/m/Y', strtotime($curriculo[$campo])));

            case 'identificacao_racial':
                global $MAPEAMENTO_IDENTIFICACAO_RACIAL;

                return $this->estilizarLinha(traduzir( $curriculo[$campo] ?? '', $MAPEAMENTO_IDENTIFICACAO_RACIAL ));

            case 'identidade_genero':
                global $MAPEAMENTO_IDENTIFICACAO_GENERO;

                return $this->estilizarLinha(traduzir(
                    $curriculo[$campo] ?? '',
                    $MAPEAMENTO_IDENTIFICACAO_GENERO
                ));

            case 'telefone_whatsapp':
            case 'telefone_opcional':

                if (empty($curriculo[$campo])) {
                    return $this->estilizarLinha('—');
                }

                return $this->estilizarLinha(formatarTelefone($curriculo[$campo] ?? ''));

            case 'cargo_efetivo':

                $cargos = json_decode($curriculo[$campo] ?? '[]', true);

                if (!is_array($cargos) || empty($cargos)) {
                    return $this->estilizarLinha('—');
                }

                foreach ($cargos as &$cargo) {

                    if (
                        $cargo === 'Outro'
                        && !empty($curriculo['cargo_outro'])
                    ) {
                        $cargo .= ' (' . $curriculo['cargo_outro'] . ')';
                    }

                }

                unset($cargo);

                return $this->estilizarLinha(implode(', ', $cargos));

            case 'possui_deficiencia':
            case 'necessita_adaptacao':
            case 'servidor_readaptado':
            case 'readaptado_necessita':
            case 'concluiu_estagio':
            case 'outra_graduacao':
            case 'acumula_cargo':

                return $this->estilizarLinha(!empty($curriculo[$campo]) ? 'Sim' : 'Não');

            case 'escolaridade':
                global $CURRICULO_MAPA_ESCOLARIDADE;

                return $this->estilizarLinha(traduzir(
                    $curriculo[$campo] ?? '',
                    $CURRICULO_MAPA_ESCOLARIDADE
                ));

            case 'duracao':
                global $MAPEAMENTO_DURACAO;

                return $this->estilizarLinha(traduzir(
                    $curriculo[$campo] ?? '',
                    $MAPEAMENTO_DURACAO
                ));

            case 'dre_lotacao':
            case 'dre_exercicio':
                global $MAPEAMENTO_OPCOES_DRES;

                return $this->estilizarLinha(traduzir(
                    $curriculo[$campo] ?? '',
                    $MAPEAMENTO_OPCOES_DRES
                ));

            case 'etapa':
                global $MAPEAMENTO_OPCOES_ETAPAS;

                return $this->estilizarLinha(traduzir(
                    $curriculo[$campo] ?? '',
                    $MAPEAMENTO_OPCOES_ETAPAS
                ));

            default:

                return $this->estilizarLinha(valor(
                    $curriculo[$campo] ?? ''
                ));
        }

    }

    /**
     * Adiciona os cabeçalhos das vivências.
     */
    private function adicionarCabecalhoVivencias(array &$cabecalho): void
    {
        $campos = $this->estrutura['vivencias']['subsecoes']['vivencia']['campos'];

        for ($i = 1; $i <= 4; $i++) {

            foreach ($campos as $config) {

                $cabecalho[] = $this->estilizarCabecalho("Vivência {$i} - {$config['label']}");

            }

        }
    }

    /**
     * Adiciona as vivências na linha.
     */
    private function adicionarVivencias(
        array &$linha,
        array $vivencias
    ): void {

        global $MAPEAMENTO_DURACAO;

        for ($i = 0; $i < 4; $i++) {

            $vivencia = $vivencias[$i] ?? null;

            if (!$vivencia) {

                $linha[] = $this->estilizarLinha('<center>-</center>'); // Organização
                $linha[] = $this->estilizarLinha('<center>-</center>'); // Cargo
                $linha[] = $this->estilizarLinha('<center>-</center>'); // Duração
                $linha[] = $this->estilizarLinha('<center>-</center>'); // Atividades

                continue;
            }

            $linha[] = $this->estilizarLinha(valor($vivencia['organizacao_empresa'] ?? ''));

            $linha[] = $this->estilizarLinha(valor($vivencia['cargo_funcao'] ?? ''));

            $linha[] = $this->estilizarLinha(traduzir(
                $vivencia['duracao'] ?? '',
                $MAPEAMENTO_DURACAO
            ));

            $linha[] = $this->estilizarLinha(valor($vivencia['atividades_competencias'] ?? ''));
        }
    }    

    /**
     * Adiciona os cabeçalhos das competências.
     */
    private function adicionarCabecalhoInformatica(
        array &$cabecalho
    ): void {

        global $CURRICULO_MAPA_COMPETENCIAS;

        foreach ($CURRICULO_MAPA_COMPETENCIAS as $competencia) {

            $cabecalho[] = $this->estilizarCabecalho($competencia);

        }

    }

    /**
     * Adiciona as competências de informática.
     */
    private function adicionarInformatica(
        array &$linha,
        array $informatica
    ): void {

        global $CURRICULO_MAPA_COMPETENCIAS;
        global $CURRICULO_MAPA_NIVEL_COMPETENCIA;

        /*
        * Organiza os níveis pela chave da competência.
        */
        $dados = [];

        foreach ($informatica as $item) {

            $dados[$item['competencia']] = traduzir(
                $item['nivel'],
                $CURRICULO_MAPA_NIVEL_COMPETENCIA
            );

        }

        /*
        * Percorre todas as competências existentes.
        */
        foreach ($CURRICULO_MAPA_COMPETENCIAS as $slug => $label) {

            $linha[] = $this->estilizarLinha($dados[$slug] ?? '—');

        }

    }

    /**
     * Adiciona os cabeçalhos do perfil comportamental.
     */
    private function adicionarCabecalhoComportamental(
        array &$cabecalho
    ): void {

        global $CURRICULO_PERFIL_COMPORTAMENTAL;

        foreach ($CURRICULO_PERFIL_COMPORTAMENTAL as $afirmacao) {

            $cabecalho[] = $this->estilizarCabecalho($afirmacao);

        }

    }

    /**
     * Adiciona as respostas do perfil comportamental.
     */
    private function adicionarComportamental(
        array &$linha,
        array $comportamental
    ): void {

        global $CURRICULO_PERFIL_COMPORTAMENTAL;
        global $CURRICULO_MAPA_NIVEL_COMPORTAMENTAL;

        $dados = [];

        foreach ($comportamental as $item) {

            $dados[$item['pergunta']] = traduzir(
                (int) $item['nivel'],
                $CURRICULO_MAPA_NIVEL_COMPORTAMENTAL
            );

        }

        foreach ($CURRICULO_PERFIL_COMPORTAMENTAL as $slug => $afirmacao) {

            $linha[] = $this->estilizarLinha($dados[$slug] ?? '—');

        }

    }

    /**
     * Adiciona os cabeçalhos das visualizações.
     */

    private function adicionarCabecalhoVisualizacao(
        array &$cabecalho
    ): void
    {
        $cabecalho[] = $this->estilizarCabecalho($this->estrutura['visualizacao']['subsecoes']['visualizarCurriculo']['titulo']);
        $cabecalho[] = $this->estilizarCabecalho($this->estrutura['visualizacao']['subsecoes']['sugestoes']['titulo']);
    }

    /**
     * Adiciona as respostas de visualização.
     */
    private function adicionarVisualizacao(
        array &$linha,
        array $visualizacao
    ): void {
        global $MAPEAMENTO_OPCOES_VISUALIZACAO;

        $linha[] = $this->estilizarLinha(traduzir($visualizacao['visualizar_curriculo'] ?? '', $MAPEAMENTO_OPCOES_VISUALIZACAO));
        $linha[] = $this->estilizarLinha($visualizacao['sugestoes'] ?? '—');
    }

    /**
     * Adiciona o cabeçalho da etapa.
     */
    private function adicionarCabecalhoEtapa(
        array &$cabecalho
        ): void
    {
        $cabecalho[] = $this->estilizarCabecalho($this->estrutura['etapa']['titulo']);
    }

    /**
     * Adiciona as etapas na linha.
     */
    private function adicionarEtapa(
        array &$linha,
        string $etapa
    ): void {
        $etapas = Inscricao::get_etapas_processo();
        $linha[] = $this->estilizarLinha($etapas[$etapa]['descricao'] ?? '—');
    }
    
}

ExportadorCurriculos::init();