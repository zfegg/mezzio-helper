<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter\Db;

/**
 * Confirms a record does not exist in a table.
 */
class NoRecordExists extends RecordExists
{
    protected const ERROR_INVALID = self::ERROR_RECORD_FOUND;
    protected const EXISTS = false;
}
