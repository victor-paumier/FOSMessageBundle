<?php

namespace FOS\MessageBundle\Model;

use DateTime;

/**
 * Abstract message model.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Message implements MessageInterface
{
    /**
     * Unique id of the message.
     *
     * @var mixed
     */
    protected $id;

    /**
     * User who sent the message.
     *
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * Text body of the message.
     *
     * @var string
     */
    protected $body;

    /**
     * Date when the message was sent.
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * Thread the message belongs to.
     *
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * {@inheritdoc}
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * {@inheritdoc}
     */
    public function setSender(ParticipantInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Gets the created at timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->getCreatedAt()->getTimestamp();
    }
}
