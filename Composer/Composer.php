<?php

namespace FOS\MessageBundle\Composer;

use FOS\MessageBundle\MessageBuilder\AbstractMessageBuilder;
use FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder;
use FOS\MessageBundle\MessageBuilder\ReplyMessageBuilder;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\ModelManager\MessageManagerInterface;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;

/**
 * Factory for message builders.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Composer implements ComposerInterface
{
    /**
     * Message manager.
     *
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * Thread manager.
     *
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    public function __construct(MessageManagerInterface $messageManager, ThreadManagerInterface $threadManager)
    {
        $this->messageManager = $messageManager;
        $this->threadManager = $threadManager;
    }

    /**
     * {@inheritDoc}
     *
     * @return NewThreadMessageBuilder
     */
    public function newThread(): AbstractMessageBuilder
    {
        $thread = $this->threadManager->createThread();
        $message = $this->messageManager->createMessage();

        return new NewThreadMessageBuilder($message, $thread);
    }

    /**
     * {@inheritDoc}
     *
     * @return ReplyMessageBuilder
     */
    public function reply(ThreadInterface $thread): AbstractMessageBuilder
    {
        $message = $this->messageManager->createMessage();

        return new ReplyMessageBuilder($message, $thread);
    }
}
