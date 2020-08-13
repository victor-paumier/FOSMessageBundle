<?php

namespace FOS\MessageBundle\Sender;

use FOS\MessageBundle\Event\FOSMessageEvents;
use FOS\MessageBundle\Event\MessageEvent;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\ModelManager\MessageManagerInterface;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Sends messages.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Sender implements SenderInterface
{
    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(MessageManagerInterface $messageManager, ThreadManagerInterface $threadManager, EventDispatcherInterface $dispatcher)
    {
        $this->messageManager = $messageManager;
        $this->threadManager = $threadManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message)
    {
        $this->threadManager->saveThread($message->getThread(), false);
        $this->messageManager->saveMessage($message, false);

        $this->dispatcher->dispatch(new MessageEvent($message), FOSMessageEvents::POST_SEND);
    }
}
