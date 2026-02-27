<?php

uses()->group('thumb-alt');

it('define o atributo alt com base no post_excerpt do attachment', function () {
    $attachment = (object) [
        'post_excerpt' => 'Texto alternativo da imagem'
    ];

    $attr = getAltTitleImagesThePostThumbnail([], $attachment);

    expect($attr)->toHaveKey('alt', 'Texto alternativo da imagem');
});

it('remove tags HTML e espaços do post_excerpt', function () {
    $attachment = (object) [
        'post_excerpt' => '  <b>Com <i>HTML</i></b>  '
    ];

    $attr = getAltTitleImagesThePostThumbnail([], $attachment);

    expect($attr['alt'])->toBe('Com HTML');
});