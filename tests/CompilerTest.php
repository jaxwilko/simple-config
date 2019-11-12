<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CompilerTest extends TestCase
{
    protected $compiler;

    protected function setUp(): void
    {
        $this->compiler = new \SimpleConfig\Compiler();
    }

    public function testCanAddSection(): void
    {
        $this->compiler->addSection(new \SimpleConfig\Section());
        $this->assertIsArray($this->compiler->getSections());
        $this->assertEquals(1, count($this->compiler->getSections()));
    }

    public function testCanCompile(): void
    {
        $this->compiler->reset()->addSection(new \SimpleConfig\Section([
            'title' => 'Section test',
            'key' => 'section',
            'comment' => 'This is a section',
            'value' => [
                'foo' => 'bar',
                'bar' => [
                    'foo'
                ]
            ]
        ]));

        $expected = <<<PHP
<?php

return [
    /*
    |----------------------------------------
    | Section test
    |----------------------------------------
    | This is a section
    |
    */

    'section' => [
        'foo' => 'bar',
        'bar' => [
            'foo',
        ],
    ],
];

PHP;

        $this->assertEquals($expected, $this->compiler->render());
    }

    public function testNoTitle(): void
    {
        $this->compiler->reset()->addSection(new \SimpleConfig\Section([
            'key' => 'section',
            'comment' => 'This is a section',
            'value' => [
                'foo' => 'bar',
                'bar' => [
                    'foo'
                ]
            ]
        ]));

        $expected = <<<PHP
<?php

return [
    /*
    |----------------------------------------
    | This is a section
    |
    */

    'section' => [
        'foo' => 'bar',
        'bar' => [
            'foo',
        ],
    ],
];

PHP;

        $this->assertEquals($expected, $this->compiler->render());
    }

    public function testNoComment(): void
    {
        $this->compiler->reset()->addSection(new \SimpleConfig\Section([
            'title' => 'Section test',
            'key' => 'section',
            'value' => [
                'foo' => 'bar',
                'bar' => [
                    'foo'
                ]
            ]
        ]));

        $expected = <<<PHP
<?php

return [
    /*
    |----------------------------------------
    | Section test
    |
    */

    'section' => [
        'foo' => 'bar',
        'bar' => [
            'foo',
        ],
    ],
];

PHP;

        $this->assertEquals($expected, $this->compiler->render());
    }

    public function testNoKey(): void
    {
        $this->compiler->reset()->addSection(new \SimpleConfig\Section([
            'title' => 'Section test',
            'comment' => 'This is a section',
            'value' => [
                'foo' => 'bar',
                'bar' => [
                    'foo'
                ]
            ]
        ]));

        $expected = <<<PHP
<?php

return [
    /*
    |----------------------------------------
    | Section test
    |----------------------------------------
    | This is a section
    |
    */

    [
        'foo' => 'bar',
        'bar' => [
            'foo',
        ],
    ],
];

PHP;

        $this->assertEquals($expected, $this->compiler->render());
    }

    public function testAllowFunctions(): void
    {
        $this->compiler->reset()->addSection(new \SimpleConfig\Section([
            'title' => 'Section test',
            'key' => 'section',
            'comment' => 'This is a section',
            'value' => [
                'foo' => 'bar',
                'bar' => [
                    '@@foo(\'abc\')'
                ]
            ]
        ]));

        $expected = <<<PHP
<?php

return [
    /*
    |----------------------------------------
    | Section test
    |----------------------------------------
    | This is a section
    |
    */

    'section' => [
        'foo' => 'bar',
        'bar' => [
            foo('abc'),
        ],
    ],
];

PHP;

        $this->assertEquals($expected, $this->compiler->render());
    }

    public function testAllowAtSymbol(): void
    {
        $this->compiler->reset()->addSection(new \SimpleConfig\Section([
            'title' => 'Section test',
            'key' => 'section',
            'comment' => 'This is a section',
            'value' => [
                'foo' => 'bar',
                'bar' => [
                    '@foo(\'abc\')'
                ]
            ]
        ]));

        $expected = <<<PHP
<?php

return [
    /*
    |----------------------------------------
    | Section test
    |----------------------------------------
    | This is a section
    |
    */

    'section' => [
        'foo' => 'bar',
        'bar' => [
            '@foo(\'abc\')',
        ],
    ],
];

PHP;

        $this->assertEquals($expected, $this->compiler->render());
    }
}
