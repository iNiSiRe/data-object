<?php


namespace inisire\DataObject\Error;


class PropertyError
{
    private string $property;

    /**
     * @var array<ErrorInterface>
     */
    private array $errors;

    /**
     * @param string                $property
     * @param array<ErrorInterface> $errors
     */
    public function __construct(string $property, array $errors)
    {
        $this->property = $property;
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return ErrorInterface[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}