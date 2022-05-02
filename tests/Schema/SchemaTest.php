<?php

namespace inisire\DataObject\Test\Schema;

use inisire\DataObject\DataObjectWizard;
use inisire\DataObject\DataSerializerProvider;
use inisire\DataObject\OpenAPI\RequestSchema;
use inisire\DataObject\OpenAPI\ResponseSchema;
use inisire\DataObject\OpenAPI\SpecificationBuilder;
use inisire\DataObject\Schema\Schema;
use inisire\DataObject\Schema\Type\TCollection;
use inisire\DataObject\Schema\Type\TInteger;
use inisire\DataObject\Schema\Type\TNumber;
use inisire\DataObject\Schema\Type\TObject;
use inisire\DataObject\Schema\Type\TString;
use inisire\DataObject\Serializer\CollectionSerializer;
use inisire\DataObject\Serializer\DateTimeSerializer;
use inisire\DataObject\Serializer\DictionarySerializer;
use inisire\DataObject\Serializer\FileSerializer;
use inisire\DataObject\Serializer\ObjectSerializer;
use inisire\DataObject\Serializer\ScalarSerializer;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    public function testSchema()
    {
        $expected = [
            'text' => new TString(),
            'number' => new TInteger(),
            'object' => new TObject(Bar::class),
            'public' => new TNumber(),
            'nullable' => new TString(),
            'collection' => new TCollection(new TObject(Bar::class)),
            'manual' => new TString(),
            'view' => new TString()
        ];

        $actual = [];
        $schema = Schema::ofClassName(Foo::class);
        foreach ($schema->getProperties() as $property) {
            $actual[$property->getName()] = $property->getType();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testTransform()
    {
        $expected = [
            'text' => 'test',
            'number' => 1,
            'object' => ['name' => 'bar#1'],
            'public' => 1,
            'nullable' => null,
            'collection' => [['name' => 'bar#2'], ['name' => 'bar#3']],
            'manual' => 'manual',
            'view' => 'test'
        ];

        $foo = new Foo();
        $foo->setText('test');
        $foo->number = 1;
        $foo->object = new Bar('bar#1');
        $foo->public = 1;
        $foo->collection = [new Bar('bar#2'), new Bar('bar#3')];
        $foo->manual = 'manual';

        $provider = new DataSerializerProvider();
        $provider->add([
            new ScalarSerializer(),
            new DictionarySerializer(),
            new DateTimeSerializer(),
            new FileSerializer(),
            new ObjectSerializer($provider),
            new CollectionSerializer($provider),
        ]);
        $serializer = new DataObjectWizard($provider);

        $actual = $serializer->transform(new TObject(Foo::class), $foo);

        $this->assertEquals($expected, $actual);

        $errors = [];
        $actual = $serializer->map(new TObject(Foo::class), $actual, $errors);

        $this->assertEquals($foo, $actual);

        $builder = new SpecificationBuilder();
        $builder->addPath('get', '/test',
            null,
            [new ResponseSchema(200, 'application/json', new TObject(Foo::class))]
        );

        $builder->addPath('post', '/test',
            new RequestSchema('application/json', new TObject(Bar::class)),
            [new ResponseSchema(200, 'application/json', new TObject(Foo::class))]
        );

        $specification = $builder->getSpecification()->toArray();
    }
}