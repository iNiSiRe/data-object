<?php


namespace inisire\DataObject\Serializer;


use inisire\DataObject\Definition\Definition;
use inisire\DataObject\Definition\TObjectReference;
use inisire\DataObject\Error\Error;
use inisire\DataObject\Errors;
use inisire\DataObject\Util\EntityManagerObjectLoader;
use inisire\DataObject\Util\MongoDocumentLoader;
use inisire\DataObject\Util\ObjectLoaderInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ObjectReferenceSerializer implements DataSerializerInterface, ServiceSubscriberInterface
{
    private ServiceLocator|ContainerInterface $container;

    private PropertyAccessor $accessor;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->accessor = new PropertyAccessor();
    }

    public function serialize(Definition|TObjectReference $type, mixed $data)
    {
        if (is_object($data) && $this->accessor->isReadable($data, 'id')) {
            return $this->accessor->getValue($data, 'id');
        } else {
            return null;
        }
    }

    public function deserialize(Definition|TObjectReference $type, mixed $data, array &$errors = [])
    {
        if (is_null($data)) {
            return null;
        }

        foreach ($this->container->getProvidedServices() as $class) {
            /**
             * @var ObjectLoaderInterface $loader
             */
            $loader = $this->container->get($class);

            if ($loader->getAlias() !== $type->loader) {
                continue;
            }

            $result = $loader->load($type, $data);

            if ($result === null) {
                $errors[] = Errors::create(Errors::INVALID_OBJECT_REFERENCE);
            }

            return $result;
        }

        throw new \RuntimeException(sprintf('The loader "%s" not exists', $type->loader));
    }

    public function isSupports(Definition $definition): bool
    {
        return $definition instanceof TObjectReference;
    }

    public static function getSubscribedServices()
    {
        return [
            MongoDocumentLoader::class,
            EntityManagerObjectLoader::class
        ];
    }
}