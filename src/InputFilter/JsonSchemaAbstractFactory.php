<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter;

use Interop\Container\ContainerInterface;
use Laminas\InputFilter\InputFilterAbstractServiceFactory;

class JsonSchemaAbstractFactory extends InputFilterAbstractServiceFactory
{
    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return isset($container->get('config')['json_schema_specs'][$requestedName]);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $file = $container->get('config')['json_schema_specs'][$requestedName];

        $factory = new JsonSchemaFactory($this->getInputFilterFactory($container));

        if (is_string($file)) {
            return $factory->createFromFile($file);
        } else {
            return $factory->create($file);
        }
    }
}
