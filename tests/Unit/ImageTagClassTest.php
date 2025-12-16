<?php

use function Pest\Faker\fake;

it('adds img-fluid class to image classes', function () {
    $class = 'existing-class';
    $result = image_tag_class($class);
    expect($result)->toContain('img-fluid');
});