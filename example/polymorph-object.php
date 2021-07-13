<?php

use inisire\DataObject\Definition\IObject;
use inisire\DataObject\Definition\TPolymorphObject;
use inisire\DataObject\Definition\TNumber;
use inisire\DataObject\Definition\TString;
use inisire\DataObject\Definition\Property;
use inisire\DataObject\VO\Discriminator;
use UserSubscriptionService\Application\Automation\Handler\TriggerListHandler;

require dirname(__DIR__) . '/../../../vendor/autoload.php';

$mapper = new \inisire\DataObject\Util\DataMapper();
$transformer = new \inisire\DataObject\Util\DataTransformer();
$generator = new \inisire\DataObject\Util\DocGenerator();

class Base implements IObject
{
    public ?string $discr;

    public static function getObjectProperties(): array
    {
        return [
            new Property('discr', new TString())
        ];
    }
}

class Foo extends Base
{
    public ?string $prop;

    public static function getObjectProperties(): array
    {
        return array_merge(parent::getObjectProperties(), [
            new Property('prop', new TString()),
        ]);
    }
}

class Bar extends Base
{
    public ?float $prop;

    public static function getObjectProperties(): array
    {
        return [
            new Property('prop', new TNumber()),
        ];
    }
}

$definition = new TPolymorphObject(
    Base::class,
    new Discriminator('discr', ['Foo' => Foo::class, 'Bar' => Bar::class])
);

$object = $mapper->object($definition, [
    'discr' => 'Foo',
    'prop' => 'value'
]);

var_dump($object);
var_dump($transformer->object($definition, $object));
var_dump($generator->createSchema($definition));