<?php

namespace VolodymyrKlymniuk\MapperBundle\DependencyInjection;

use GeneratedHydrator\Configuration;
use VolodymyrKlymniuk\StdLib\Helper\Helper;
use Zend\Hydrator\HydratorInterface;

class Hydrator
{
    /**
     * @param array  $source
     * @param string $class
     *
     * @return array|object
     */
    public function hydrate(array $source, string $class)
    {
        if (empty($source)) {
            return $this->hydrateObject($source, $class);
        }

        return true === $this->isCollection($source)
            ? $this->hydrateCollection($source, $class)
            : $this->handleObject($source, $class);
    }

    /**
     * @param array $source
     *
     * @return bool
     */
    private function isCollection(array $source): bool
    {
        return isset($source[0]) && is_array($source[0]);
    }

    /**
     * @param array  $source
     * @param string $class
     *
     * @return array
     */
    private function hydrateCollection(array $source, string $class): array
    {
        $result = [];
        foreach ($source as $item) {
            $result[] = $this->hydrate($item, $class);
        }

        return $result;
    }

    /**
     * @param array  $source
     * @param string $class
     *
     * @return object
     */
    private function handleObject(array $source, string $class)
    {
        $dto = new $class();
        $relationMap = $dto instanceof RelationInterface ? $dto->getRelations() : [];

        $result = [];
        foreach ($source as $key => &$value) {
            $key = Helper::underscoreToCamelCase($key);
            $result[$key] = isset($relationMap[$key])
                ? $this->hydrate($value, $relationMap[$key])
                : $value;
        }

        return $this->hydrateObject($result, $class);
    }

    /**
     * @param array  $source
     * @param string $class
     *
     * @return object
     */
    private function hydrateObject(array $source, string $class)
    {
        $dto = new $class();

        $config = new Configuration($class);
        $hydratorClass = $config->createFactory()->getHydratorClass();

        /* @var HydratorInterface $hydrator */
        $hydrator = new $hydratorClass();
        $hydrator->hydrate($source, $dto);

        return $dto;
    }
}