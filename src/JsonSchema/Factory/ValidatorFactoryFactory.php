<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\JsonSchema\Factory;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use Zfegg\MezzioHelper\JsonSchema\Constraints\RemoveAddPropsObjectConstraint;
use Zfegg\MezzioHelper\JsonSchema\Constraints\TypeConstraint;
use Zfegg\MezzioHelper\JsonSchema\Constraints\UndefinedConstraint;

class ValidatorFactoryFactory
{

    public function __invoke(): Factory
    {
        $schemaStorage = new SchemaStorage();

        $factory = new Factory(
            $schemaStorage,
            null,
            // phpcs:ignore
            Constraint::CHECK_MODE_TYPE_CAST | Constraint::CHECK_MODE_COERCE_TYPES | Constraint::CHECK_MODE_APPLY_DEFAULTS
        );

        $factory->setConstraintClass('type', TypeConstraint::class);
        $factory->setConstraintClass('object', RemoveAddPropsObjectConstraint::class);
        $factory->setConstraintClass('undefined', UndefinedConstraint::class);

        return $factory;
    }
}
