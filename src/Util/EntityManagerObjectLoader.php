<?php

namespace inisire\DataObject\Util;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use inisire\DataObject\Definition\TObjectReference;

class EntityManagerObjectLoader implements ObjectLoaderInterface
{
    public function __construct(
        private EntityManager $manager)
    {
    }

    public function load(TObjectReference $definition, mixed $id): ?object
    {
        return $this->manager->getRepository($definition->object->getClass())->find($id);
    }

    public function getAlias(): string
    {
        return 'orm';
    }
}