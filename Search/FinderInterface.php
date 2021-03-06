<?php

namespace FOS\MessageBundle\Search;

use Doctrine\ORM\QueryBuilder;
use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Finds threads of a participant, matching a given query.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface FinderInterface
{
    /**
     * Finds threads of a participant, matching a given query.
     *
     * @param Query $query
     *
     * @return ThreadInterface[]
     */
    public function find(Query $query);

    /**
     * Finds threads of a participant, matching a given query.
     *
     * @param Query $query
     *
     * @return QueryBuilder a query builder suitable for pagination
     */
    public function getQueryBuilder(Query $query);
}
