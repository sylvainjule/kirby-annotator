<?php

return array(
    'isPin' => function($field) {
        return $field->value == 'pin';
    },
    'isNotPin' => function($field) {
        return $field->value != 'pin';
    },
    'isCircle' => function($field) {
        return $field->value == 'circle';
    },
    'isNotCircle' => function($field) {
        return $field->value != 'circle';
    },
    'isRect' => function($field) {
        return $field->value == 'rect';
    },
    'isNotRect' => function($field) {
        return $field->value != 'rect';
    },
    'toPercent' => function($field) {
    	return floatval($field->value) * 100;
    },
    'toPercentString' => function($field) {
        // Floatval isn't locale aware and outputs commas instead of dots for decimal separator
        // This doesn't work in CSS, which expects a dot
    	return str_replace(',','.', floatval($field->value) * 100) . '%';
    },
);
