<?php


namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation
 */
class TPartialObject extends \inisire\DataObject\Definition\TPartialObject
{
    public string $class;

    public function __construct(array $options)
    {
        parent::__construct($options['class'] ?? $options['value'] ?? null);
    }
}