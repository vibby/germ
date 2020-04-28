<?php

namespace Germ\Domain\Church;


use PommProject\Foundation\Where;

interface ChurchRepositoryInterface
{
    public function findAll(): \Traversable;

    public function choiceSlug(): array;

    public function choiceId(): array;

    public function findIdsFromSlugs(array $slugs): array;

    public function findForListWhereSql(Where $where, $projection = null, $suffix = null): array;
}
