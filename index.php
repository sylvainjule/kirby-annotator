<?php

require_once __DIR__ . '/lib/functions.php';

use Kirby\Query\Query;

Kirby::plugin('sylvainjule/annotator', array(
	'sections' => array(
        'annotator' => array(
        	'props' => array(
        		'debug' => function($debug = false) {
        			return $debug;
        		},
        		'theme' => function($theme = null) {
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
                    $image = false;

                    if ($this->model()->type() == "image") {
                        $image = $this->model()->url();
                    }
                    elseif(array_key_exists('src', $this->storage)) {
                        $src = $this->storage['src'];

                        if(Url::isAbsolute($src)) {
                            $image = $src;
                        }
                        else {
                            $query  = Query::factory($src);

                            try {
                                $result = $query->resolve([
                                    'kirby' => kirby(),
                                    'site'  => site(),
                                    'page'  => $this->model(),
                                ]);

                                if($result) {
                                    if(Url::isAbsolute($result)) {
                                        $image = $result;
                                    }
                                    elseif($result instanceof Kirby\Cms\File) {
                                        $image = $result->url();
                                    }
                                }
                            }
                            catch (Exception $e) {}
                        }
                    }

                    return $image;
                },
            )
        ),
    ),
    'fieldMethods' => require_once __DIR__ . '/lib/fieldMethods.php',
));
