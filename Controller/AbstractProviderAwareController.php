<?php

namespace FOS\MessageBundle\Controller;

use FOS\MessageBundle\Provider\ProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AbstractProviderAwareController
 */
abstract class AbstractProviderAwareController extends AbstractController
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * MessageController constructor.
     *
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }
}