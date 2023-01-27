<?php

namespace VolodymyrKlymniuk\MapperBundle\Mapper;

use VolodymyrKlymniuk\MapperBundle\DependencyInjection\Hydrator;
use VolodymyrKlymniuk\ExceptionHandlerBundle\Exception\ValidationException;
use DataMapper\Mapper as ObjectMapper;
//use NSM\Mapper\Mapper as ObjectMapper;
//use VolodymyrKlymniuk\Std\Helper\Helper;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use VolodymyrKlymniuk\StdLib\Helper\Helper;

class Mapper
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ObjectMapper
     */
    private $objectMapper;

    /**
     * @param ValidatorInterface $validator
     * @param ObjectMapper       $objectMapper
     */
    public function __construct(ValidatorInterface $validator, ObjectMapper $objectMapper)
    {
        $this->validator = $validator;
        $this->objectMapper = $objectMapper;
    }

    /**
     * @param array|object  $source
     * @param string|object $destination
     * @param array         $validationGroups
     *
     * @return object|mixed
     *
     * @throws \UnexpectedValueException
     * @throws ValidationException
     */
    public function handle($source, $destination, array $validationGroups = null)
    {
        if (is_array($source) && is_string($destination)) {
            $dto = (new Hydrator())->hydrate($source, $destination);
        } elseif (is_array($source) && is_object($destination)) {
            // TODO: need implement
        } elseif (is_object($source)) {
            $dto = $this->objectMapper->convert($source, $destination);
        } else {
            throw new \UnexpectedValueException(sprintf('Source type of: "%s" cannot be handled', Helper::getType($source)));
        }

        $errors = $this->validator->validate($dto, null, $validationGroups);
        if (count($errors) > 0) {
            throw new ValidationException((array) $errors->getIterator());
        }

        return $dto;
    }
}