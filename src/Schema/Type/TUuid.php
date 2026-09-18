<?php

namespace inisire\DataObject\Schema\Type;

use inisire\DataObject\Serializer\UuidSerializer;
use Symfony\Component\Uid\Uuid;

class TUuid implements Type, \inisire\DataObject\OpenAPI\Type
{
    /**
     * @param class-string<Uuid>
     */
    public function __construct(
        private readonly string $class = Uuid::class,
    ) {
        if (!is_a($class, Uuid::class, true)) {
            throw new \InvalidArgumentException(sprintf('$class should be a class-string of %s, %s given', Uuid::class, $class));
        }
    }

    /**
     * @return class-string<Uuid>
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function getSerializer(): string
    {
        return UuidSerializer::class;
    }

    public function getSchema(): array
    {
        return [
            'type' => 'string',
            'format' => 'uuid'
        ];
    }
}
