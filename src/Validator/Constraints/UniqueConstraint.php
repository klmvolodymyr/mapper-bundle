<?php

namespace VolodymyrKlymniuk\MapperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value "{{ string }}" is already used.';

    /**
     * @var string
     */
    public $fields;

    /**
     * @var string
     */
    public $entity;

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
        return self::CLASS_CONSTRAINT;
    }
}