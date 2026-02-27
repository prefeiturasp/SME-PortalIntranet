<?php

class WP_Query_Mock {
    public $paged;
    public $max_num_pages;

    public function __construct($paged, $max_num_pages) {
        $this->paged = $paged;
        $this->max_num_pages = $max_num_pages;
    }

    public function get($key) {
        if ($key === 'paged') {
            return $this->paged;
        }
        return null;
    }
}

it('gera html da paginacao', function () {
    global $wp_query;

    $wp_query = new WP_Query_Mock(2, 5);

    ob_start();
    paginacao();
    $html = ob_get_clean();

    expect($html)->toContain('<nav id="pagination"');
    expect($html)->toContain('fa-chevron-left');
    expect($html)->toContain('fa-chevron-right');
});