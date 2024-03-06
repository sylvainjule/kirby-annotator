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
        		'tools' => function($tools = ['pin', 'rect', 'circle']) {
        			return $tools;
        		},
        		'colors' => function($colors = ['orange', 'yellow', 'green', 'blue', 'purple', 'pink']) {
        			return $colors;
        		},
        		'storage' => function($storage = []) {
                    return $storage;
                },
                'max' => function($max = false) {
                	return $max;
                },
                'zoom' => function($zoom = false) {
                	return $zoom;
                },
                'translate' => function($translate = true) {
                    return $translate;
                },
                'dblclick' => function($dblclick = false) {
                    return $dblclick;
                }
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
    'fieldMethods' => require_once __DIR__ . '/lib/fieldMethods.php',
));
