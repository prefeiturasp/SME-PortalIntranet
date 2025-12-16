<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdicionarNavClassTest extends TestCase
{
    public function test_adicionar_nav_class_adiciona_classes_ao_a()
    {
        $html_original = '<ul><li><a href="#section1">Section 1</a></li></ul>';

        $html_esperado = '<ul><li><a class="nav-link scroll" href="#section1">Section 1</a></li></ul>';

        // Chama a função que você quer testar
        $resultado = adicionar_nav_class($html_original);

        // Verifica se o retorno está correto
        $this->assertEquals($html_esperado, $resultado);
    }

    public function test_adicionar_nav_class_nao_altera_outras_tags()
    {
        $html_original = '<div><a href="#link">Link</a></div>';

        $html_esperado = '<div><a class="nav-link scroll" href="#link">Link</a></div>';

        $resultado = adicionar_nav_class($html_original);

        $this->assertEquals($html_esperado, $resultado);
    }
}