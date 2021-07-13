<?php


namespace inisire\DataObject\Error;


class BulkError implements ErrorInterface, \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var ErrorInterface[]
     */
    private array $errors = [];

    /**
     * @param array<ErrorInterface> $errors
     */
    public function __construct(array $errors = [])
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    public function addError(ErrorInterface $error)
    {
        if ($error instanceof BulkError) {
            foreach ($error->getErrors() as $child) {
                $this->errors[] = $child;
            }
        } else {
            $this->errors[] = $error;
        }
    }

    /**
     * @return ErrorInterface[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->errors as $key => $error) {
            $data[] = $error->toArray();
        }

        return $data;
    }

    public function offsetExists($offset)
    {
        return isset($this->errors[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->errors[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->errors[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->errors[$offset]);
    }

    public function count()
    {
        return count($this->errors);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->errors);
    }
}