<?php


namespace inisire\DataObject\Definition\Annotation;


/**
 * @Annotation
 */
class TNumber extends \inisire\DataObject\Definition\TNumber
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