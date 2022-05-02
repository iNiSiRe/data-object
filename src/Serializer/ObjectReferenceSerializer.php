<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Schema\Type\Type;
use inisire\DataObject\Schema\Type\TObjectReference;
use inisire\DataObject\Error\Errors;
use inisire\DataObject\Runtime\ObjectLoaderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ObjectReferenceSerializer implements DataSerializerInterface
{
    private PropertyAccessor $accessor;

    /**
     * @var array<ObjectLoaderInterface>
     */
    private array $loaders = [];

    public function __construct(array $loaders = [])
    {
        $this->accessor = new PropertyAccessor();
        $this->loaders = $loaders;
    }

    public function serialize(Type|TObjectReference $type, mixed $data)
    {
        if (is_object($data) && $this->accessor->isReadable($data, 'id')) {
            return $this->accessor->getValue($data, 'id');
        } else {
            return null;
        }
    }

    private function getLoader(string $name): ?ObjectLoaderInterface
    {
        foreach ($this->loaders as $loader) {
            if ($loader->getAlias() === $name) {
                return $loader;
            }
        }

        return null;
    }

    public function deserialize(Type|TObjectReference $type, mixed $data, array &$errors = [])
    {
        if (is_null($data)) {
            return null;
        }

        $loader = $this->getLoader($type->getLoaderName());

        if (!$loader) {
            throw new \RuntimeException(sprintf('The loader "%s" does not exist', $type->getLoaderName()));
        }

        $result = $loader->load($type, $data);

        if ($result === null) {
            $errors[] = Errors::create(Errors::INVALID_OBJECT_REFERENCE);
        }

        return $result;
    }

    public function isSupports(Type $type): bool
    {
        return $type instanceof TObjectReference;
    }
}