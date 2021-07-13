<?php


namespace inisire\DataObject\Definition\Annotation;

use inisire\DataObject\Definition\Definition;

/**
 * @Annotation()
 */
class TCollection extends \inisire\DataObject\Definition\TCollection
{
    public function __construct(array $values)
    {
        parent::__construct(
            $values['entry'] ?? null,
            $values['container'] ?? [],
            $values['min'] ?? 0,
            $values['max'] ?? null
        );
    }
}