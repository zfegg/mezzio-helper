<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter\Validator;

use Laminas\Validator\AbstractValidator;

class Type extends AbstractValidator
{
    const ERROR_TYPE    = 'typeError';

    protected array $options = [
        'type' => ['string'],
    ];

    protected array $messageTemplates = [
        self::ERROR_TYPE => 'Invalid value type',
    ];

    /**
     * @param string|array $type
     */
    public function setType($type): void
    {
        $this->options['type'] = (array)$type;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        $this->setValue($value);

        if (! in_array(gettype($value), $this->options['type'])) {
            $this->error(self::ERROR_TYPE);
            return false;
        }

        return true;
    }
}
