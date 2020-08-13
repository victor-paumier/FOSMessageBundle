<?php

namespace FOS\MessageBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\Message as BaseMessage;

abstract class Message extends BaseMessage
{
    /**
     * @ORM\Column(name="body", type="text", nullable=false)
     *
     * @var string
     */
    protected $body;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    protected $createdAt;
}
