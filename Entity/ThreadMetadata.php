<?php

namespace FOS\MessageBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ThreadMetadata as BaseThreadMetadata;

abstract class ThreadMetadata extends BaseThreadMetadata
{
    protected $id;
    protected $thread;

    /**
     * @ORM\Column(name="is_read", type="boolean", nullable=false)
     *
     * @var bool
     */
    protected $isRead = false;

    /**
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
     *
     * @var bool
     */
    protected $isDeleted = false;

    /**
     * Gets the thread map id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }

    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }
}
