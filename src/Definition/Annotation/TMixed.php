<?php


namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation
 */
class TMixed extends \inisire\DataObject\Definition\TMixed
{
    public function __construct(array $options)
    {
        parent::__construct($options['value'] ?? null);
    }
}