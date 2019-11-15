<?php

namespace SimpleConfig;

use Symfony\Component\VarExporter\VarExporter;

class Document
{
    protected $content;
    protected $indentation;
    protected $length;

    public function __construct(string $indentation, int $length)
    {
        $this->content = sprintf('<?php%1$s%1$sreturn [%1$s', PHP_EOL);
        $this->indentation = $indentation;
        $this->length = $length;
    }

    protected function write(string $str): void
    {
        $this->content .= $this->indentation . $str . PHP_EOL;
    }

    protected function writeNl(): void
    {
        $this->content .= PHP_EOL;
    }

    public function writeSection(Section $section, bool $isLast): void
    {
        if ($section->title || $section->comment) {
            $this->write('/*');
        }
        if ($section->title) {
            $this->write('|' . str_repeat('-', $this->length));
            $this->write('| ' . $section->title);
            if (!$section->comment) {
                $this->write('|');
            }
        }
        if ($section->comment) {
            $this->write('|' . str_repeat('-', $this->length));
            $comment = wordwrap(str_replace(PHP_EOL, ' ', $section->comment), $this->length);
            foreach ((strpos($comment, PHP_EOL) !== false ? explode(PHP_EOL, $comment) : [$comment]) as $line) {
                $this->write('| ' . $line);
            }
            $this->write('|');
        }
        if ($section->title || $section->comment) {
            $this->write('*/');
            $this->writeNl();
        }

        if ($section->value) {
            $valueLines = explode(PHP_EOL, VarExporter::export($section->value));
            foreach ($valueLines as $index => $line) {
                preg_match_all('/\'\@[@](.*.?)\'/', $line, $matches);
                if ((isset($matches[0]) && $matches[0]) && (isset($matches[1]) && $matches[1])) {
                    $line = str_replace($matches[0][0], stripslashes($matches[1][0]), $line);
                }
                $this->write(
                    ($index === 0 && $section->key ? '\'' . $section->key . '\' => ' : '') . $line .
                    ($index === count($valueLines) - 1 ? ',' : '')
                );
            }
            if (!$isLast) {
                $this->writeNl();
            }
        }
    }

    public function end(): void
    {
        $this->content .= '];' . PHP_EOL;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}