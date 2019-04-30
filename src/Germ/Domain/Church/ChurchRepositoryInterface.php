<?php

namespace Germ\Domain\Church;


interface ChurchRepositoryInterface
{
    public function findAll(): \Traversable;

    public function choiceSlug(): array;

    public function choiceId(): array;

    public function findIdsFromSlugs(): array;

    public function findForListWhereSql(): array;
}
