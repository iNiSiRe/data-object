<?php

namespace inisire\DataObject\Schema\Type;

class TBuiltinEnum extends TPrimitive
{
    private readonly TString|TInteger $type;
    /**
     * @var array<int|string>
     */
    private readonly array $options;

    /**
     * @param class-string<\BackedEnum> $enum
     */
    public function __construct(
        private readonly string $enum,
    ) {
        $reflection = new \ReflectionEnum($enum);

        $this->type = match ($reflection->getBackingType()?->getName()) {
            'string' => new TString(),
            'int' => new TInteger(),
            default => throw new \InvalidArgumentException(sprintf('Unsupported enum %s backing type', $enum)),
        };

        $this->options = array_map(
            fn (\BackedEnum $case): int|string => $case->value,
            $enum::cases(),
        );
    }

    public function getEnum(): string
    {
        return $this->enum;
    }

    public function getType(): TString|TInteger
    {
        return $this->type;
    }

    /**
     * @return array<int|string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
