<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter\Filter;

use Laminas\Filter\AbstractFilter;

class ToString extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        if (! is_scalar($value)) {
            return $value;
        }

        return (string) $value;
    }
}
