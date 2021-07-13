<?php


namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation
 */
class TString extends \inisire\DataObject\Definition\TString
{
    public function __construct(array $options)
    {
        parent::__construct(
            $options['default'] ?? null,
            $options['minLength'] ?? 0,
            $options['maxLength'] ?? null
        );
    }
}