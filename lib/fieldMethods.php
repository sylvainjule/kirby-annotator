<?php

return array(
	'stuff' => function() {
		return 'ok';
	},
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
    	return floatval($field->value) * 100 . '%';
    },
);