<?php

namespace FOS\MessageBundle\Security;

use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Provides the authenticated participant.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ParticipantProvider implements ParticipantProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $securityContext;

    public function __construct(TokenStorageInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedParticipant()
    {
        $participant = $this->securityContext->getToken()->getUser();

        if (!$participant instanceof ParticipantInterface) {
            throw new AccessDeniedException('Must be logged in with a ParticipantInterface instance');
        }

        return $participant;
    }
}
