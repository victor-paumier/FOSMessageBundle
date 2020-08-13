<?php

namespace FOS\MessageBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\Thread as BaseThread;
use FOS\MessageBundle\Model\ThreadMetadata as ModelThreadMetadata;
use InvalidArgumentException;
use Traversable;

abstract class Thread extends BaseThread
{
    /**
     * Messages contained in this thread.
     *
     * @var Collection|MessageInterface[]
     */
    protected $messages;

    /**
     * Users participating in this conversation.
     *
     * @var Collection|ParticipantInterface[]
     */
    protected $participants;

    /**
     * Thread metadata.
     *
     * @var Collection|ModelThreadMetadata[]
     */
    protected $metadata;

    /**
     * All text contained in the thread messages
     * Used for the full text search.
     *
     * @var string
     */
    protected $keywords = '';

    /**
     * @ORM\Column(name="subject", type="string", nullable=false)
     *
     * @var string
     */
    protected $subject;

    /**
     * @ORM\Column(name="is_spam", type="boolean", nullable=false)
     *
     * @var bool
     */
     protected $isSpam = false;

    /**
     * Participant that created the thread.
     *
     * @var ParticipantInterface
     */
    protected $createdBy;

    /**
     * Date this thread was created at.
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * {@inheritdoc}
     */
    public function getParticipants()
    {
        return $this->getParticipantsCollection()->toArray();
    }

    /**
     * Gets the users participating in this conversation.
     *
     * Since the ORM schema does not map the participants collection field, it
     * must be created on demand.
     *
     * @return ArrayCollection|ParticipantInterface[]
     */
    protected function getParticipantsCollection()
    {
        if (null === $this->participants) {
            $this->participants = new ArrayCollection();

            foreach ($this->metadata as $data) {
                $this->participants->add($data->getParticipant());
            }
        }

        return $this->participants;
    }

    /**
     * {@inheritdoc}
     */
    public function addParticipant(ParticipantInterface $participant)
    {
        if (!$this->isParticipant($participant)) {
            $this->getParticipantsCollection()->add($participant);
        }
    }

    /**
     * Adds many participants to the thread.
     *
     * @param array|Traversable
     *
     * @return Thread
     *@throws InvalidArgumentException
     *
     */
    public function addParticipants($participants)
    {
        if (!is_array($participants) && !$participants instanceof Traversable) {
            throw new InvalidArgumentException('Participants must be an array or instance of Traversable');
        }

        foreach ($participants as $participant) {
            $this->addParticipant($participant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isParticipant(ParticipantInterface $participant)
    {
        return $this->getParticipantsCollection()->contains($participant);
    }

    /**
     * Get the collection of ModelThreadMetadata.
     *
     * @return Collection
     */
    public function getAllMetadata()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function addMetadata(ModelThreadMetadata $meta)
    {
        $meta->setThread($this);
        parent::addMetadata($meta);
    }
}
