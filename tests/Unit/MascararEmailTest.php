<?php

it('retorna vazio para email invalido', function () {
    expect(mascarar_email('invalido'))->toBe('');
});

it('mascara nome mantendo dominio padrao', function () {
    $email = 'john.doe@example.com';
    // nome: john.doe (8 chars) => joh + **** + e
    expect(mascarar_email($email))->toBe('joh****e@example.com');
});

it('mascara nome com 4 caracteres', function () {
    $email = 'abcd@example.com';
    // nome: abcd (4 chars) => abc + *
    expect(mascarar_email($email))->toBe('abc*@example.com');
});

it('mascara nome curto de 3 caracteres', function () {
    $email = 'abc@example.com';
    // nome: abc (3 chars) => ab + *
    expect(mascarar_email($email))->toBe('ab*@example.com');
});

it('mascara nome com 2 caracteres', function () {
    $email = 'ab@example.com';
    // nome: ab (2 chars) => a + *
    expect(mascarar_email($email))->toBe('a*@example.com');
});

it('mascara nome com 1 caractere', function () {
    $email = 'a@example.com';
    // nome: a (1 char) => a + *
    expect(mascarar_email($email))->toBe('a@example.com');
});
