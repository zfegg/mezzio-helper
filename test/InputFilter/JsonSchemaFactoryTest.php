<?php declare(strict_types = 1);

namespace ZfeggTest\MezzioHelper\InputFilter;

use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use PHPUnit\Framework\TestCase;
use Zfegg\MezzioHelper\InputFilter\JsonSchemaFactory;

class JsonSchemaFactoryTest extends TestCase
{

    public function testCreate(): void
    {
        $factory = new JsonSchemaFactory(new Factory());
        $rs = $factory->createFromFile(__DIR__ . '/../rules.create.json');

        $this->assertInstanceOf(InputFilterInterface::class, $rs);
    }
}
