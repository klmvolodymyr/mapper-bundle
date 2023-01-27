<?php

namespace VolodymyrKlymniuk\MapperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class DocumentConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value "{{ string }}" is not valid.';

    /**
     * @var string
     */
    public $document;

    /**
     * @var string
     */
    public $field = '_id';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}