<?php

namespace SylvainJule\Annotator;

use Kirby\Cms\File;
use Kirby\Form\Field\DisplayField;
use Kirby\Http\Url;
use Kirby\Query\Query;
use Kirby\Reflection\Attributes\Prop;
use Exception;

class AnnotatorField extends DisplayField {
    #[Prop]
    protected bool $debug = false;

    #[Prop]
    protected string|null $theme = null;

    #[Prop]
    protected array $tools = ['pin', 'rect', 'circle'];

    #[Prop]
    protected array $colors = ['orange', 'yellow', 'green', 'blue', 'purple', 'pink'];

    #[Prop]
    protected array $storage = [];

    #[Prop]
    protected int|false $max = false;

    #[Prop]
    protected bool $zoom = false;

    #[Prop]
    protected bool $dblclick = false;

    public function props(): array {
        return [
            ...parent::props(),
            'translate' => $this->translate(),
            'storage'   => (object)$this->storage,
            'image'     => $this->image(),
        ];
    }

    public function image(): string|false {
        $model = $this->model();

        if($model instanceof File && $model->type() === 'image') {
            return $model->url();
        }

        $src = $this->storage['src'] ?? null;
        if(!is_string($src) || $src === '') {
            return false;
        }

        if(Url::isAbsolute($src)) {
            return $src;
        }

        try {
            $result = Query::factory($src)->resolve([
                'kirby' => $this->kirby(),
                'site'  => $this->kirby()->site(),
                'page'  => $model,
            ]);

            if($result instanceof File) {
                return $result->url();
            }
            if(is_string($result) && Url::isAbsolute($result)) {
                return $result;
            }
        } 
        catch(Exception $e) {}

        return false;
    }
}
