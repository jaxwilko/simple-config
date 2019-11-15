<?php

namespace SimpleConfig;

use Symfony\Component\VarExporter\VarExporter;

class Compiler
{
    protected $sections = [];

    protected $options = [
        'tabs'      => false,
        'indent'    => 4,
        'length'    => 40
    ];

    public function __construct(array $options = null)
    {
        if ($options) {
            foreach ($this->options as $option) {
                if (isset($options[$option])) {
                    $this->options[$option] = $options[$option];
                }
            }
        }
    }

    public function option()
    {
        $args = func_get_args();
        if (count($args) === 1 && is_array($args[0])) {
            $this->options = array_merge($this->options, $args[0]);
        } elseif (count($args) === 1 && is_string($args[0])) {
            return $this->options[$args[0]] ?? null;
        } elseif (count($args) === 2) {
            $this->options[$args[0]] = $args[1];
        } else {
            throw new \InvalidArgumentException('invalid args passed');
        }
        return $this;
    }

    public function addSection(Section $section): Compiler
    {
        $this->sections[] = $section;
        return $this;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function reset(): Compiler
    {
        $this->sections = [];
        return $this;
    }

    public function render(): string
    {
        $document = new Document(
            $this->option('tabs') ? "\t" : str_repeat(' ', $this->option('indent')),
            $this->option('length')
        );

        foreach ($this->sections as $sectionIndex => $section) {
            $document->writeSection($section, $sectionIndex === count($this->sections) - 1);
        }

        $document->end();

        return $document->getContent();
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
