<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter;

use Laminas\InputFilter\InputFilter as LaminasInputFilter;
use Laminas\InputFilter\InputFilterInterface;

class InputFilter extends LaminasInputFilter
{
    /**
     * @inheritDoc
     */
    public function getValues()
    {
        $inputs = $this->validationGroup ?: array_keys($this->inputs);
        $values = [];
        foreach ($inputs as $name) {
            $input = $this->inputs[$name];

            if (! isset($this->data[$name]) && method_exists($input, 'hasFallback') && ! $input->hasFallback()) {
                continue;
            }

            if ($input instanceof InputFilterInterface) {
                $values[$name] = $input->getValues();
                continue;
            }

            $values[$name] = $input->getValue();
        }
        return $values;
    }
}
