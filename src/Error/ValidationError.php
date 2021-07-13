<?php


namespace inisire\DataObject\Error;


use Symfony\Component\Validator\ConstraintViolationInterface;

class ValidationError extends BulkError
{
    /**
     * @param array<ConstraintViolationInterface> $violations
     */
    private iterable $violations;

    /**
     * @param array<ConstraintViolationInterface> $violations
     */
    public function __construct(iterable $violations)
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = new PropertyError($violation->getPropertyPath(), new Error($violation->getMessage()));
        }

        parent::__construct($errors);
        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationInterface[]
     */
    public function getViolations(): iterable
    {
        return $this->violations;
    }
}