<?php

it('retorna primeira letra de uma única palavra', function () {
    expect(firstLetter('Hello'))->toBe('H');
    expect(firstLetter('Mundo'))->toBe('M');
});

it('retorna primeiras letras de múltiplas palavras', function () {
    expect(firstLetter('Hello World'))->toBe('HW');
    expect(firstLetter('São Paulo'))->toBe('SP');
    expect(firstLetter('João Silva Santos'))->toBe('JSS');
});

it('preserva maiúsculas e minúsculas', function () {
    expect(firstLetter('JavaScript PHP'))->toBe('JP');
    expect(firstLetter('javaScript php'))->toBe('jp');
    expect(firstLetter('JAVASCRIPT PHP'))->toBe('JP');
});

it('lida com string vazia', function () {
    expect(firstLetter(''))->toBe('');
});

it('lida com múltiplos espaços entre palavras', function () {
    expect(firstLetter('A    B    C'))->toBe('ABC');
    expect(firstLetter('Teste    Outro'))->toBe('TO');
});

it('lida com espaços no início e fim', function () {
    expect(firstLetter('  Hello World  '))->toBe('HW');
    expect(firstLetter('   Teste   '))->toBe('T');
});

it('lida com apenas espaços', function () {
    expect(firstLetter('   '))->toBe('');
    expect(firstLetter(' '))->toBe('');
});

it('funciona com palavras de um caractere', function () {
    expect(firstLetter('A B C'))->toBe('ABC');
});

it('funciona com números no início das palavras', function () {
    expect(firstLetter('123 ABC'))->toBe('1A');
    expect(firstLetter('2test 3test'))->toBe('23');
});

it('funciona com caracteres especiais no início', function () {
    expect(firstLetter('@test #test'))->toBe('@#');
});

it('gera siglas de nomes completos ignorando artigos e preposições', function () {
    expect(firstLetter('João da Silva'))->toBe('JS');
    expect(firstLetter('Maria de Oliveira Santos'))->toBe('MOS');
    expect(firstLetter('José do Nascimento'))->toBe('JN');
});

it('gera siglas de títulos ignorando stop words', function () {
    expect(firstLetter('Secretaria Municipal de Educação'))->toBe('SME');
    expect(firstLetter('Diretoria Regional de Educação'))->toBe('DRE');
    expect(firstLetter('Secretaria de Educação do Estado'))->toBe('SEE');
});

it('preserva maiúsculas mesmo ignorando stop words', function () {
    expect(firstLetter('JavaScript de PHP'))->toBe('JP');
    expect(firstLetter('javaScript de php'))->toBe('jp');
});

it('funciona quando stop word está no início ou fim', function () {
    expect(firstLetter('de Teste Exemplo'))->toBe('TE');
    expect(firstLetter('Teste Exemplo de'))->toBe('TE');
    expect(firstLetter('de Teste Exemplo da'))->toBe('TE');
});

