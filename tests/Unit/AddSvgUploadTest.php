<?php

it('adiciona svg ao array de mimes', function () {
    $mimes = ['jpg' => 'image/jpeg'];
    $result = cc_mime_types($mimes);
    
    expect($result['svg'])->toBe('image/svg+xml');
    expect($result['jpg'])->toBe('image/jpeg'); // outros tipos permanecem
});
