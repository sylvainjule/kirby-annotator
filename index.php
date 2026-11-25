<?php

require_once __DIR__ . '/lib/functions.php';

use Kirby\Blueprint\Normalizer;
use SylvainJule\Annotator\AnnotatorField;

load([
    AnnotatorField::class => 'lib/fields/AnnotatorField.php'
], __DIR__);


Kirby::plugin('sylvainjule/annotator', [
    'fields' => [
        'annotator' => AnnotatorField::class
    ],
    'fieldMethods' => require __DIR__ . '/lib/fieldMethods.php',
]);


if(class_exists(Normalizer::class) && property_exists(Normalizer::class, 'sectionFields')) {
    Normalizer::$sectionFields['annotator'] ??= 'annotator';
}