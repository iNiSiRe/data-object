<?php

namespace inisire\DataObject\Test\Schema;

use inisire\DataObject\Schema\Attribute\IgnoreProperty;
use inisire\DataObject\Schema\Attribute\CalculatedProperty;
use inisire\DataObject\Schema\Attribute\Property;
use inisire\DataObject\Schema\Type\TCollection;
use inisire\DataObject\Schema\Type\TObject;
use inisire\DataObject\Schema\Type\TString;

class Foo
{
    private string $text;

    public int $number;

    public Bar $object;

    public float $public;

    public ?string $nullable = null;

    /**
     * @var array<Bar>
     */
    #[Property(new TCollection(new TObject(Bar::class)))]
    public array $collection = [];

    #[IgnoreProperty]
    private int $ignore;

    #[Property(new TString())]
    public mixed $manual;

    private int $private;

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    #[CalculatedProperty('view', new TString())]
    public function someViewProperty(): string
    {
        return $this->text;
    }
}