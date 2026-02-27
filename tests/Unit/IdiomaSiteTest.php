<?php

use function PHPUnit\Framework\assertEquals;

it('retorna en.php quando lang é en', function () {
    expect(load_lang_file('en'))->toBe('includes/en.php');
});

it('retorna pt.php quando lang não é en', function () {
    expect(load_lang_file('pt'))->toBe('includes/pt.php');
    expect(load_lang_file(null))->toBe('includes/pt.php');
});