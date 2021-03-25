<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter\Db;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Validator\ValidatorInterface;
use Psr\Container\ContainerInterface;

class RecordFactory
{
    public function __invoke(ContainerInterface $container, string $name, array $options = []): ValidatorInterface
    {
        if (! (isset($options['em']) && $options['em'] instanceof EntityManagerInterface)) {
            $options['em'] = $container->get($options['em'] ?? EntityManagerInterface::class);
        }

        return new $name($options);
    }
}
