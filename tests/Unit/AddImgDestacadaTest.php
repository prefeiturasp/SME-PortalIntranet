<?php

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    /** @test */
    public function it_mantem_a_thumbnail_se_o_post_ja_tiver()
    {
        // Define as funções mock (dublês)
        $hasThumbnailFn = fn() => true; // Simula que o post TEM thumbnail
        $getFirstImageFn = fn() => null; // Simula que não há primeira imagem

        // A função mock setPostThumbnailFn rastreia o que foi passado para ela.
        $setPostThumbnailFn = $this->getMockBuilder(\stdClass::class)
                                 ->addMethods(['__invoke'])
                                 ->getMock();

        $setPostThumbnailFn->expects($this->once())
                           ->method('__invoke')
                           ->with(
                                $this->anything(),
                                $this->equalTo(123) // O ID da thumbnail esperada
                            );
        
        $getPostThumbnailIdFn = fn() => 123; // ID da thumbnail existente
        
        // Crie um objeto de post falso
        $post = (object) ['ID' => 1];

        // Execute a função com as dependências mock
        fpw_post_info($post->ID, $post, $hasThumbnailFn, $getFirstImageFn, $getPostThumbnailIdFn, $setPostThumbnailFn);
    }
}