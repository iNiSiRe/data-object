<?php

namespace inisire\DataObject\Error;

class ErrorMessage implements \JsonSerializable
{
    private string $template;

    private array $parameters;

    /**
     * @param string               $template
     * @param array<string,string> $parameters
     */
    public function __construct(string $template, array $parameters = [])
    {
        $this->template = $template;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getValue(): string
    {
        $template = $this->template;

        foreach ($this->parameters as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }

    public function serialize(): string|array
    {
        return $this->parameters
            ? ['value'      => $this->getValue(),
               'template'   => $this->getTemplate(),
               'parameters' => $this->getParameters()]
            : $this->getValue();
    }

    public function jsonSerialize()
    {
        return $this->serialize();
    }
}