<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\JsonSchema\Constraints;

use JsonSchema\Constraints\TypeConstraint as BaseTypeConstraint;

class TypeConstraint extends BaseTypeConstraint
{
    /**
     * @inheritdoc
     */
    protected function toBoolean($value)
    {
        if ($value === '1') {
            return true;
        }

        if ($value === '0') {
            return false;
        }

        return parent::toBoolean($value);
    }
}
