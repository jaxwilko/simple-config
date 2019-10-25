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
        $indentation = $this->option('tabs')
            ? "\t"
            : str_repeat(' ', $this->option('indent'));

        $document = sprintf('<?php%1$s%1$sreturn [%1$s', PHP_EOL);

        foreach ($this->sections as $sectionIndex => $section) {
            if ($section->title || $section->comment) {
                $document .= $indentation . '/*' . PHP_EOL;
            }
            if ($section->title) {
                $document .= $indentation . '|' . str_repeat('-', $this->option('length')) . PHP_EOL;
                $document .= $indentation . '| ' . $section->title . PHP_EOL;
                if (!$section->comment) {
                    $document .= $indentation . '|' . PHP_EOL;
                }
            }
            if ($section->comment) {
                $document .= $indentation . '|' . str_repeat('-', $this->option('length')) . PHP_EOL;
                $comment = wordwrap(str_replace(PHP_EOL, ' ', $section->comment), $this->option('length'));
                foreach ((strpos($comment, PHP_EOL) !== false ? explode(PHP_EOL, $comment) : [$comment]) as $line) {
                    $document .= $indentation . '| ' . $line . PHP_EOL;
                }
                $document .= $indentation . '|' . PHP_EOL;
            }
            if ($section->title || $section->comment) {
                $document .= $indentation . '*/' . PHP_EOL . PHP_EOL;
            }

            if ($section->value) {
                $valueLines = explode(PHP_EOL, VarExporter::export($section->value));

                foreach ($valueLines as $index => $line) {
                    $document .= $indentation . ($index === 0 && $section->key ? '\'' . $section->key . '\' => ' : '');
                    $document .= $line . ($index === count($valueLines) - 1 ? ',' : '') . PHP_EOL;
                }
                if ($sectionIndex !== count($this->sections) - 1) {
                    $document .= PHP_EOL;
                }
            }
        }

        $document .= '];' . PHP_EOL;

        return $document;
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
