<?php
namespace Classes\TemplateHierarchy\ArchiveAgendaNew;

class ArchiveAgendaGetDatasEventosNew
{
    const CPTAGENDA = 'agendanew';
    private $args_ids;
    private $query_ids;
    private $array_ids;
    private $array_datas;

    public function __construct()
    {
        // Só registra os hooks, não executa nada
        $this->init();
    }

    public function init()
    {
        // Usa o hook template_redirect (antes do template, mas depois dos posts)
        add_action('template_redirect', [$this, 'load_data']);
        
        // Output apenas no footer (depois de tudo carregado)
        add_action('wp_footer', [$this, 'render_hidden_field']);
    }

    public function load_data()
    {
        // Verifica se é a página correta
        if (is_admin()) {
            return;
        }

        $current_url = $_SERVER['REQUEST_URI'];
        $partes = explode("/", trim($current_url, '/'));
        
        // Verifica se estamos em uma página relacionada à agenda
        if (isset($partes[1]) && $partes[1] === 'agendanew' || 
            (isset($partes[2]) && $partes[2] === 'home')) {
            $this->getTodosIdCtpAgenda();
            $this->getDatasCptAgenda();
        }
    }

    public function getTodosIdCtpAgenda()
    {
        $this->args_ids = array(
            'post_type' => 'agendanew',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_key' => 'data_do_evento',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );

        $this->query_ids = get_posts($this->args_ids);

        if ($this->query_ids) {
            foreach ($this->query_ids as $item) {
                $this->array_ids[] = $item->ID;
            }
        }
    }

    public function getDatasCptAgenda()
    {
        if (empty($this->array_ids)) {
            return;
        }

        foreach ($this->array_ids as $id) {
            $data = get_field('data_do_evento', $id);
            if ($data) {
                $this->array_datas[] = $data;
            }
        }

        // Não dá echo, apenas armazena
        $this->array_datas = json_encode($this->array_datas);
    }

    public function render_hidden_field()
    {
        // Verifica as condições corretas para mostrar o campo
        if (empty($this->array_datas)) {
            return;
        }

        $current_url = $_SERVER['REQUEST_URI'];
        $partes = explode("/", trim($current_url, '/'));

        // Apenas na home da agenda
        if (!isset($partes[2]) || $partes[2] !== 'home') {
            return;
        }

        // Agora sim, output no lugar certo
        ?>
        <input type="hidden" 
               name="array_datas_agenda" 
               id="array_datas_agenda" 
               value="<?php echo esc_attr($this->array_datas); ?>">
        <?php
    }
}

// Instancia a classe - apenas registra hooks, sem output imediato
new ArchiveAgendaGetDatasEventosNew();