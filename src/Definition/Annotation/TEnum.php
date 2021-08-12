<?php

namespace inisire\DataObject\Definition\Annotation;

/**
 * @Annotation
 */
class TEnum extends \inisire\DataObject\Definition\TEnum
{
    public function __construct(array $options)
    {
        if (isset($options['value'])) {
            $type = $options['value'][0];
            $enum = $options['value'][1];
            $keyAsLabel = $options['value'][2] ?? true;
        } else {
            $type = $options['type'] ?? null;
            $enum = $options['options'] ?? null;
            $keyAsLabel = $options['keyAsLabel'] ?? true;
        }

        parent::__construct($type, $enum, $keyAsLabel);
    }
}