<?php

use function array_flatten;

// Função a ser testada (caso não esteja disponível globalmente)
if (!function_exists('array_flatten')) {
    function array_flatten($array) { 
        if (!is_array($array)) { 
            return false; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
                $result = array_merge($result, array_flatten($value)); 
            } else { 
                $result[$key] = $value; 
            } 
        } 
        return $result; 
    }
}

describe('array_flatten', function () {
    
    it('retorna false para entrada não array', function () {
        expect(array_flatten(null))->toBeFalse();
        expect(array_flatten('string'))->toBeFalse();
        expect(array_flatten(123))->toBeFalse();
        expect(array_flatten(true))->toBeFalse();
        expect(array_flatten(new stdClass()))->toBeFalse();
    });

    it('retorna array vazio para array vazio', function () {
        expect(array_flatten([]))->toBe([]);
    });

    it('mantém array unidimensional inalterado', function () {
        $array = [1, 2, 3, 4, 5];
        expect(array_flatten($array))->toBe($array);
        
        $arrayAssoc = ['a' => 1, 'b' => 2, 'c' => 3];
        expect(array_flatten($arrayAssoc))->toBe($arrayAssoc);
    });

    it('achata array bidimensional', function () {
        $array = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        
        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        expect(array_flatten($array))->toBe($expected);
    });

    it('achata array multidimensional complexo', function () {
        $array = [
            'level1' => [
                'level2a' => [
                    'level3a' => 'value1',
                    'level3b' => 'value2'
                ],
                'level2b' => 'value3',
                'level2c' => [
                    'level3c' => [
                        'level4' => 'value4'
                    ]
                ]
            ],
            'direct' => 'value5'
        ];
        
        $expected = [
            'level3a' => 'value1',
            'level3b' => 'value2',
            'level2b' => 'value3',
            'level4' => 'value4',
            'direct' => 'value5'
        ];
        
        expect(array_flatten($array))->toBe($expected);
    });

    it('achata array com chaves numéricas mistas', function () {
        $array = [
            [1, 2],
            'a' => [3, 4],
            [5, 6]
        ];
        
        $expected = [1, 2, 3, 4, 5, 6];
        expect(array_flatten($array))->toBe($expected);
    });

    it('achata array com valores mistos', function () {
        $array = [
            'string' => 'hello',
            'number' => 42,
            'array' => [
                'nested' => 'world',
                'another' => [true, false]
            ],
            'boolean' => true
        ];
        
        $expected = [
            'string' => 'hello',
            'number' => 42,
            'nested' => 'world',
            true,
            false,
            'boolean' => true
        ];
        
        expect(array_flatten($array))->toBe($expected);
    });

    it('preserva chaves string quando possível', function () {
        $array = [
            'a' => 1,
            'b' => [
                'c' => 2,
                'd' => 3
            ],
            'e' => 4
        ];
        
        $expected = [
            'a' => 1,
            'c' => 2,
            'd' => 3,
            'e' => 4
        ];
        
        expect(array_flatten($array))->toBe($expected);
    });

    it('lida com arrays profundamente aninhados', function () {
        $array = [
            [
                [
                    [
                        'final' => 'value'
                    ]
                ]
            ]
        ];
        
        $expected = ['final' => 'value'];
        expect(array_flatten($array))->toBe($expected);
    });

    it('mantém ordem dos elementos', function () {
        $array = [
            ['a', 'b'],
            ['c', 'd'],
            ['e', 'f']
        ];
        
        $expected = ['a', 'b', 'c', 'd', 'e', 'f'];
        expect(array_flatten($array))->toBe($expected);
    });

});