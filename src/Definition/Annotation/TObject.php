<?php


namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation()
 */
class TObject extends \inisire\DataObject\Definition\TObject
{
    public string $class;

    public function __construct(array $options)
    {
        parent::__construct($options['class'] ?? $options['value']);
    }
}