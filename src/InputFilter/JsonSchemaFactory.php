<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\InputFilter;

use Laminas\Filter\Boolean;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\ArrayInput;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\OptionalInputFilter;
use Zfegg\MezzioHelper\InputFilter\Filter\ToNumber;
use Zfegg\MezzioHelper\InputFilter\Filter\ToString;
use Zfegg\MezzioHelper\InputFilter\Validator\Type;

class JsonSchemaFactory
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function createFromFile(string $file): InputFilterInterface
    {
        return $this->create(json_decode(file_get_contents($file)));
    }

    public function create(object $schema): InputFilterInterface
    {
        $type = $schema->type ?? null;

        if ($type != 'object') {
            throw new \InvalidArgumentException('Schema root need object type.');
        }

        $inputs = $this->createObject($schema);

        return $this->factory->createInputFilter($inputs);
    }

    protected function createByType(object $schema, bool $required, string $name): array
    {
        $type = $schema->type ?? null;

        if ($type == 'object') {
            $input = $this->createObject($schema, $required, $name);
        } elseif ($type == 'array') {
            $input = $this->createArrayInput($schema, $required, $name);
        } elseif (is_string($type)) {
            $input = $this->createInput($schema, $required, $name);
        } elseif (is_array($type)) {
            $input = [
                'validators' => [
                    Type::class => ['name' => Type::class, 'options' => ['type' => $type]],
                ],
                'name' => $name,
                'required' => $required,
            ];
        }

        return $input;
    }

    private function createObject(object $schema, bool $isRequired = true, ?string $name = null): array
    {
        $properties = $schema->properties ?? [];
        $required = $schema->required ?? [];
        $inputs = [
            'type' => $isRequired ? InputFilter::class : OptionalInputFilter::class,
        ];
        if ($name) {
            $inputs['name'] = $name;
        }

        foreach ($properties as $key => $subSchema) {
            $inputs[] = $this->createByType($subSchema, in_array($key, $required), $key);
        }

        return $inputs;
    }

    protected function createArrayInput(object $schema, bool $required, string $name): array
    {
        $input = ['type' => ArrayInput::class,];

        if (isset($schema->items)) {
            if (is_object($schema->items)) {
                $itemInput = $this->createByType($schema->items, $required, $name);
                $isInputFilter = (new \ReflectionClass($itemInput['type']))
                    ->implementsInterface(InputFilterInterface::class);

                unset($itemInput['name']);

                if ($isInputFilter) {
                    $input['type'] = CollectionInputFilter::class;
                    $input['input_filter'] = $itemInput;
                    if (isset($schema->minItems)) {
                        $input['count'] = $schema->minItems;
                    }
                } else {
                    $input = $input + $itemInput;
                }
            }
        } else {
            $input['required'] = $required;
        }
        $input['name'] = $name;

        return $input;
    }


    protected function createInput(object $schema, bool $required, string $name): array
    {
        $type = $schema->type ?? null;

        $input = [
            'validators' => [
                Type::class => ['name' => Type::class, 'options' => ['type' => $type]],
            ],
        ];

        switch ($type) {
            case 'integer':
                $input['filters'] = [['name' => ToInt::class],];
                break;
            case 'number':
                $input['filters'] = [['name' => ToNumber::class]];
                $input['validators'][Type::class]['options']['type'] = ['integer', 'double'];
                break;
            case 'boolean':
                $input['filters'] = [['name' => Boolean::class]];
                break;
            case 'string':
                $input['filters'] = [['name' => ToString::class]];
                break;
            default:
                break;
        }

        $input['name'] = $name;
        $input['required'] = $required;

        if (isset($schema->pattern)) {
            $input['validators'][] = ['name' => 'Regex', 'options' => ["pattern" => "/{$schema->pattern}/"]];
        }

        if (isset($schema->enum)) {
            $input['validators'][] = ['name' => 'InArray', 'options' => ["haystack" => $schema->enum]];
        }

        if (isset($schema->default)) {
            $input['fallback_value'] = $schema->default;
        }

        return $input;
    }
}
