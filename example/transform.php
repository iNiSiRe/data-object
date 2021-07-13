#!/usr/bin/env php
<?php

use inisire\DataObject\Definition\IObject;
use inisire\DataObject\Definition\TCollection;
use inisire\DataObject\Definition\TDateTime;
use inisire\DataObject\Definition\TDictionary;
use inisire\DataObject\Definition\TObject;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TString;
use inisire\DataObject\Definition\Property;

require dirname(__DIR__) . '/../../../vendor/autoload.php';

class Foo implements IObject
{
    public ?string $a = null;

    /**
     * @var array<Bar>
     */
    public array $c;

    /**
     * @var array<float>
     */
    public array $d;

    public ?DateTime $timestamp;

    /**
     * @var array<string,string>
     */
    public array $dictionary;

    public function __construct()
    {
        $this->c = [];
        $this->d = [];
        $this->timestamp = new DateTime();
    }

    public static function getObjectProperties(): array
    {
        return [
            new Property('a', new TString()),
            new Property('b', new TObject(Bar::class)),
            new Property('c', new TCollection(new TObject(Bar::class))),
            new Property('d', new TCollection(new TNumber())),
            new Property('timestamp', new TDateTime()),
            new Property('dictionary', new TDictionary()),
        ];
    }
}

class Bar implements IObject
{
    public ?string $a;
    public ?float $b;

    public static function getObjectProperties(): array
    {
        return [
            new Property('a', new TString()),
            new Property('b', new TNumber()),
        ];
    }
}


$mapper = new \inisire\DataObject\Util\DataMapper();
$transformer = new \inisire\DataObject\Util\DataTransformer();

$data = [
    'a' => "abc",
    'b' => 1,
    'c' => [
        'c' => [
                ['a' => 'first', 'b' => 1, 'c' => ['dictionary' => ['a' => 'b']]],
                ['a' => 'second', 'b' => 2],
        ],
        'd' => [1, 2, 3],
        'timestamp' => '2019-01-01T00:00:00+00:00',
        'dictionary' => [
                'foo' => 'bar',
                'bar' => 'foo',
        ]
    ]
];

$errors = [];
$result = $mapper->object(new TObject(Foo::class), $data, $errors);

var_dump($errors);

//echo json_encode($transformer->object(new TObject(Foo::class), $result), JSON_PRETTY_PRINT) . PHP_EOL;

$generator = new \inisire\DataObject\Util\DocGenerator();

echo json_encode($generator->getObjectSchemaReference(new TObject(Foo::class)), JSON_PRETTY_PRINT) . PHP_EOL;
echo json_encode($generator->getDoc(), JSON_PRETTY_PRINT) . PHP_EOL;