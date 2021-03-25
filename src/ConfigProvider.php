<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper;

use JsonSchema\Constraints\Factory;
use Zfegg\MezzioHelper\JsonSchema\Factory\ValidatorFactoryFactory;
use Zfegg\MezzioHelper\InputFilter\Db\NoRecordExists;
use Zfegg\MezzioHelper\InputFilter\Db\RecordExists;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    Factory::class => ValidatorFactoryFactory::class,
                ],
            ],
            'input_filters' => [
                'abstract_factories' => [
                    InputFilter\JsonSchemaAbstractFactory::class,
                ],
            ],
            'validators' => [
                'factories' => [
                    RecordExists::class => InputFilter\Db\RecordFactory::class,
                    NoRecordExists::class => InputFilter\Db\RecordFactory::class,
                ],
            ]
        ];
    }
}
