<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter\Filter;

use Laminas\Filter\AbstractFilter;

class ToNumber extends AbstractFilter
{

    /**
     * @inheritDoc
     */
    public function filter($value)
    {
        if (! is_numeric($value)) {
            return $value;
        }

        return $value + 0;
    }
}
