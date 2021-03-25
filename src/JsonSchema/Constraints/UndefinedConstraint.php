<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\JsonSchema\Constraints;

use JsonSchema\Constraints\UndefinedConstraint as BaseUndefinedConstraint;
use JsonSchema\Entity\JsonPointer;

class UndefinedConstraint extends BaseUndefinedConstraint
{
    /**
     * @inheritdoc
     */
    public function check(&$value, $schema = null, JsonPointer $path = null, $i = null, $fromDefault = false): void
    {
        parent::check($value, $schema, $path, $i, $fromDefault);
    }
}
