<?php

namespace VolodymyrKlymniuk\MapperBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\Regex;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueConstraintValidator extends ConstraintValidator
{
    /**
     * @var ObjectManager
     */
    private $dm;

    /**
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     */
    public function __construct(?DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param object     $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if (null === $this->dm) {
            throw new \DomainException('Service @doctrine_mongodb.odm.document_manager doesn\'t exist');
        }

        if (!$constraint instanceof UniqueConstraint) {
            throw new UnexpectedTypeException($constraint, UniqueConstraint::class);
        }

        if (null === $constraint->fields || null === $constraint->entity) {
            throw new \LogicException('Field and entity parameters are requred');
        }

        foreach ($constraint->fields as $field) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $value = $accessor->getValue($entity, $field);
            if ($value !== null && $value !== '') {
                $existing = $this->findEntity($value, $field, $constraint->entity);
                if (null !== $existing && $existing !== $entity) {
                    $this->context
                        ->buildViolation($constraint->message)
                        ->setParameter('{{ string }}', $value)
                        ->atPath($field)
                        ->addViolation();
                }
            }
        }
    }

    /**
     * @param string $value
     * @param string $field
     * @param string $repository
     *
     * @return null|object
     */
    private function findEntity(string $value, string $field, string $repository)
    {
        return $this->dm->getRepository($repository)->findOneBy([$field => new Regex($value, 'i')]);
    }
}