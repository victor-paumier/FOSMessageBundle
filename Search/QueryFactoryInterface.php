<?php

namespace FOS\MessageBundle\Search;

use Symfony\Component\HttpFoundation\Request;

/**
 * Gets the search term from the request and prepares it.
 */
interface QueryFactoryInterface
{
    /**
     * Gets the search term.
     *
     * @param Request $request
     *
     * @return Query the term object
     */
    public function createFromRequest(Request $request);
}
