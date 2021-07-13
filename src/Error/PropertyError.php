<?php


namespace inisire\DataObject\Error;


class PropertyError implements ErrorInterface
{
    private string $property;
    private ErrorInterface $error;

    public function __construct(string $property, ErrorInterface $error)
    {
        $this->property = $property;
        $this->error = $error;
    }

    public function toArray(): array
    {
        return [
            'property' => $this->property,
            'error' => $this->error->toArray()
        ];
    }
}