<?php

namespace SimpleConfig;

class Section
{
    public $key     = null;
    public $comment = null;
    public $title   = null;
    public $value   = null;

    public function __construct(array $options = null)
    {
        if ($options) {
            foreach (['key', 'title', 'comment', 'value'] as $item) {
                if (isset($options[$item])) {
                    $this->{$item} = $options[$item];
                }
            }
        }
    }

    public function key(string $key): Section
    {
        $this->key = $key;
        return $this;
    }


    public function title(string $title): Section
    {
        $this->title = $title;
        return $this;
    }

    public function comment(string $comment): Section
    {
        $this->comment = $comment;
        return $this;
    }

    public function value(array $value): Section
    {
        $this->value = $value;
        return $this;
    }
}