> Archived: use [winter/laravel-config-writer](https://github.com/wintercms/laravel-config-writer) instead.

# SimpleConfig

This package aims to provide a simple interface for creating php config files.

### Installation

Add the package to your project via:

```
composer require jaxwilko/simple-config
```

### Usage

```php
<?php

use SimpleConfig\Compiler;
use SimpleConfig\Section;

$compiler = new Compiler();
$section = new Section();
$section->title = 'Database';
$section->key = 'database';
$section->comment = 'This is where your database config goes.';
$section->value = ['mysql' => ['host' => 'localhost']];
$compiler->addSection($section);
echo $compiler->render();
```

Would return:

```php
<?php

return [
    /*
    |----------------------------------------
    | Database
    |----------------------------------------
    | This is where your database config goes.
    |
    */

    'database' => [
        'mysql' => [
            'host' => 'localhost',
        ],
    ],
];
```

A simpler way of achieving the same thing would be:

```php
<?php

$compiler = new Compiler();
$compiler->addSection(new Section([
    'title'     => 'Database',
    'key'       => 'database',
    'comment'   => 'This is where your database config goes.',
    'value'     => ['mysql' => ['host' => 'localhost']],
]));
echo $compiler->render();
```

Everything can be omitted, for example just having a comment and title:

```php
$compiler = new Compiler();
$compiler->addSection(new Section([
    'title' => 'Database',
    'comment' => 'This is where your database config goes.',
]));
echo $compiler->render();
```

Will return:

```php
<?php

return [
    /*
    |----------------------------------------
    | Database
    |----------------------------------------
    | This is where your database config goes.
    |
    */

];
```

Or, without a comment and title:

```php
<?php

$compiler = new Compiler();
$compiler->addSection(new Section([
    'key' => 'database',
    'value' => ['mysql' => ['host' => 'localhost']],
]));
echo $compiler->render();
```

Will return:

```php
<?php

return [
    'database' => [
        'mysql' => [
            'host' => 'localhost',
        ],
    ],
];
```

Multiple sections can be added via chaining:

```php
<?php

$compiler->addSection(new Section([
        'title' => 'Section one',
        'key' => 'section_one',
        'comment' => 'This is a section',
        'value' => [
            'foo' => 'bar',
            'bar' => [
                'foo'
            ]
        ]
    ]))
    ->addSection(new Section([
        'title' => 'Section two',
        'key' => 'section_two',
        'comment' => 'This is another section',
        'value' => [
            'bar' => 'foo',
            'foo' => [
                'bar'
            ]
        ]
    ]));
```

Functions can be passed in by prefixing the method with `@@`. E.g.

```php
<?php

$compiler->addSection(new Section([
    'title' => 'Section',
    'key' => 'section',
    'comment' => 'This is a section',
    'value' => [
        'foo' => '@@env(\'VALUE\')',
    ]
]));
```

Will result in:

```php
<?php

return [
    /*
    |----------------------------------------
    | Section
    |----------------------------------------
    | This is a section
    |
    */

    'section' => [
        'foo' => env('VALUE'),
    ],
];
```

Compiler options can be set via:

```php
<?php

// define the comment length
$compiler->option('length', 80);
// use spaces over tabs
$compiler->option('tabs', false);
// define the indentation length
$compiler->option('indent', 4);
```
