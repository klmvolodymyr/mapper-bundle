<?php

namespace VolodymyrKlymniuk\MapperBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DocumentConstraintValidator extends ConstraintValidator
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
     * @param string|integer $value
     * @param Constraint     $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $this->dm) {
            throw new \DomainException('Service @doctrine_mongodb.odm.document_manager doesn\'t exist');
        }

        if (!$constraint instanceof DocumentConstraint) {
            throw new UnexpectedTypeException($constraint, DocumentConstraint::class);
        }

        if (!empty($value)) {
            foreach ((array) $value as $entry) {
                $document = $this->findDocuments($entry, $constraint->field, $constraint->document);
                if (null === $document) {
                    $this->context
                        ->buildViolation($constraint->message)
                        ->setParameter('{{ string }}', $entry)
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
     * @return object
     */
    private function findDocuments(string $value, string $field, string $repository): ?object
    {
        return $this->dm->getRepository($repository)->findOneBy([$field => $value]);
    }
}