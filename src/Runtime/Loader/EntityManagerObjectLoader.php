<?php

namespace inisire\DataObject\Runtime\Loader;

use Doctrine\ORM\EntityManagerInterface;
use inisire\DataObject\Runtime\ObjectLoaderInterface;
use inisire\DataObject\Schema\Type\TObjectReference;

class EntityManagerObjectLoader implements ObjectLoaderInterface
{
    public function __construct(
        private EntityManagerInterface $manager
    )
    {
    }

    public function load(TObjectReference $type, mixed $id): ?object
    {
        return $this->manager->getRepository($type->getObject()->getClass())->find($id);
    }

    public function getAlias(): string
    {
        return 'orm';
    }
}