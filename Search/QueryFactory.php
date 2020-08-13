<?php

namespace FOS\MessageBundle\Search;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gets the search term from the request and prepares it.
 */
class QueryFactory implements QueryFactoryInterface
{
    /**
     * The query parameter containing the search term.
     *
     * @var string
     */
    protected $queryParameter;

    /**
     * Instanciates a new TermGetter.
     *
     * @param string               $queryParameter
     */
    public function __construct($queryParameter)
    {
        $this->queryParameter = $queryParameter;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(Request $request)
    {
        $original = $request->query->get($this->queryParameter);
        $original = trim($original);

        $escaped = $this->escapeTerm($original);

        return new Query($original, $escaped);
    }

    /**
     * Sets: the query parameter containing the search term.
     *
     * @param string $queryParameter
     */
    public function setQueryParameter($queryParameter)
    {
        $this->queryParameter = $queryParameter;
    }

    protected function escapeTerm($term)
    {
        return $term;
    }
}
