<?php 

require_once __DIR__ . '/lib/functions.php';

Kirby::plugin('sylvainjule/annotator', array(
	'sections' => array(
        'annotator' => array(
        	'props' => array(
        		'debug' => function($debug = false) {
        			return $debug;
        		},
        		'theme' => function($theme = 'light') {
        			return $theme;
        		},
        		'buttons' => function($buttons = ['pin', 'rect', 'circle']) {
        			return $buttons;
        		},
        		'colors' => function($colors = ['orange', 'yellow', 'green', 'blue', 'purple', 'pink']) {
        			return $colors;
        		},
        		'storage' => function($storage = []) {
                    return $storage;
                },
        	),
        	'computed' => array(
                'image' => function() {
                    if ($this->model()->type() == "image") {
                        return $this->model()->url();
                    }
                    else {
                        return false;
                    }
                }
            )
        ),
    ),
    'fieldMethods' => array(
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
    ),
));