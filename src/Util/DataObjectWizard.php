<?php


namespace inisire\DataObject\Util;


use Symfony\Component\PropertyAccess\PropertyAccessor;

class DataObjectWizard
{
    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = new PropertyAccessor();
    }

    public function applyPatch(object $object, object $changes): array
    {
        $patch = [];

        foreach (get_object_vars($changes) as $property => $value) {
            $previousValue = $this->propertyAccessor->getValue($object, $property);
            if ($value !== $previousValue) {
                $this->propertyAccessor->setValue($object, $property, $value);
                $patch[$property] = [$previousValue, $value];
            }
        }

        return $patch;
    }
}