<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter\Db;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Validator\AbstractValidator;

/**
 * Confirms a record exists in a table.
 */
class RecordExists extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';
    protected const ERROR_INVALID = self::ERROR_NO_RECORD_FOUND;

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::ERROR_NO_RECORD_FOUND => 'No record matching the input was found',
        self::ERROR_RECORD_FOUND    => 'A record matching the input was found',
    ];

    protected EntityManagerInterface $em;
    protected string $entity;
    protected string $field;
    protected array $criteria = [];

    protected const EXISTS = true;

    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     */
    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    /**
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @param array $criteria
     */
    public function setCriteria(array $criteria): void
    {
        $this->criteria = $criteria;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        $valid = true;
        $this->setValue($value);

        $result = $this->em->getRepository($this->entity)->count($this->criteria + [$this->field => $value]);
        if (static::EXISTS !== (bool)$result) {
            $valid = false;
            $this->error(static::ERROR_INVALID);
        }

        return $valid;
    }
}
