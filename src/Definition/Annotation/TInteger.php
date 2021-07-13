<?php


namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation
 */
class TInteger extends \inisire\DataObject\Definition\TInteger
{
    public function __construct(array $options)
    {
        parent::__construct(
            $options['default'] ?? null,
            $options['min'] ?? null,
            $options['max'] ?? null
        );
    }
}