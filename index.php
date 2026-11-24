<?php

require_once __DIR__ . '/lib/functions.php';

use Kirby\Query\Query;
use Kirby\Cms\File;
use Kirby\Http\Url;

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

                    if($this->model() instanceof File && $this->model()->type() === 'image') {
                        $image = $this->model()->url();
                    }
                    elseif(array_key_exists('src', $this->storage)) {
                        $src = $this->storage['src'];

                        if(!is_string($src) || $src === '') {
                            return false;
                        }

                        if(Url::isAbsolute($src)) {
                            $image = $src;
                        }
                        else {
                            try {
                                $query = Query::factory($src);
                                $result = $query->resolve([
                                    'kirby' => kirby(),
                                    'site'  => site(),
                                    'page'  => $this->model(),
                                ]);

                                if($result) {
                                    if($result instanceof File) {
                                        $image = $result->url();
                                    }
                                    elseif(is_string($result) && Url::isAbsolute($result)) {
                                        $image = $result;
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
