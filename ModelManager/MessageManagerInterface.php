<?php

namespace FOS\MessageBundle\ModelManager;

use FOS\MessageBundle\Model\MessageInterface;

/**
 * Interface to be implemented by message managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to messages should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MessageManagerInterface
{
    /**
     * Creates an empty message instance.
     *
     * @return MessageInterface
     */
    public function createMessage();

    /**
     * Saves a message.
     *
     * @param MessageInterface $message
     * @param bool             $andFlush Whether to flush the changes (default true)
     */
    public function saveMessage(MessageInterface $message, $andFlush = true);

    /**
     * Returns the message's fully qualified class MessageManagerInterface.
     *
     * @return string
     */
    public function getClass();
}
