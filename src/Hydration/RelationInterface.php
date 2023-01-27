<?php

namespace VolodymyrKlymniuk\MapperBundle\DependencyInjection;

interface RelationInterface
{
    /**
     * @return string[]
     */
    public function getRelations(): array;
}