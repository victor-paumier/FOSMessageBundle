<?php

namespace FOS\MessageBundle\Twig\Extension;

use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Provider\ProviderInterface;
use FOS\MessageBundle\Security\AuthorizerInterface;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use Twig\Extension\AbstractExtension;

class MessageExtension extends AbstractExtension
{
    protected $participantProvider;
    protected $provider;
    protected $authorizer;

    protected $hasUnreadThreadsCache;

    public function __construct(ParticipantProviderInterface $participantProvider, ProviderInterface $provider, AuthorizerInterface $authorizer)
    {
        $this->participantProvider = $participantProvider;
        $this->provider = $provider;
        $this->authorizer = $authorizer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('fos_message_is_read', array($this, 'isRead')),
            new \Twig_SimpleFunction('fos_message_has_unread', array($this, 'hasUnread')),
            new \Twig_SimpleFunction('fos_message_can_delete_thread', array($this, 'canDeleteThread')),
            new \Twig_SimpleFunction('fos_message_deleted_by_participant', array($this, 'isThreadDeletedByParticipant')),
        );
    }

    /**
     * Tells if this readable (thread or message) is read by the current user.
     *
     * @return bool
     */
    public function isRead(ReadableInterface $readable)
    {
        return $readable->isReadByParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * Checks if the participant can mark a thread as deleted.
     *
     * @param ThreadInterface $thread
     *
     * @return bool true if participant can mark a thread as deleted, false otherwise
     */
    public function canDeleteThread(ThreadInterface $thread)
    {
        return $this->authorizer->canDeleteThread($thread);
    }

    /**
     * Checks if the participant has marked the thread as deleted.
     *
     * @param ThreadInterface $thread
     *
     * @return bool true if participant has marked the thread as deleted, false otherwise
     */
    public function isThreadDeletedByParticipant(ThreadInterface $thread)
    {
        return $thread->isDeletedByParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * Has unread thread for the current user
     *
     * @return bool
     */
    public function hasUnread()
    {
        if (null === $this->hasUnreadThreadsCache) {
            $this->hasUnreadThreadsCache = $this->provider->hasUnreadThreads();
        }

        return $this->hasUnreadThreadsCache;
    }

    /**
     * Gets the current authenticated user.
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fos_message';
    }
}
