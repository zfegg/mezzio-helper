<?php declare(strict_types = 1);

namespace Zfegg\MezzioHelper\JsonSchema\Constraints;

use JsonSchema\Constraints\ObjectConstraint;
use JsonSchema\Entity\JsonPointer;

class RemoveAddPropsObjectConstraint extends ObjectConstraint
{

    /**
     * @inheritdoc
     */
    public function check(
        &$element,
        $schema = null,
        JsonPointer $path = null,
        $properties = null,
        $additionalProp = null,
        $patternProperties = null,
        $appliedDefaults = []
    ) {
        if ($additionalProp === false) {
            foreach ($element as $key => $value) {
                $definition = $this->getProperty($properties, $key);

                if (! $definition) {
                    unset($element[$key]);
                }
            }
        }
        parent::check(
            $element,
            $schema,
            $path,
            $properties,
            $additionalProp,
            $patternProperties,
            $appliedDefaults
        );
    }
}
