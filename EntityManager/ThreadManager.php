<?php

namespace FOS\MessageBundle\EntityManager;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Exception;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\ModelManager\ThreadManager as BaseThreadManager;

/**
 * Default ORM ThreadManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ThreadManager extends BaseThreadManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var EntityRepository
     */
    protected $metaRepository;

    /**
     * The model class.
     *
     * @var string
     */
    protected $class;

    /**
     * The model class.
     *
     * @var string
     */
    protected $metaClass;

    /**
     * The message class.
     *
     * @var string
     */
    protected $messageClass;

    /**
     * The message manager, required to mark
     * the messages of a thread as read/unread.
     *
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @param EntityManager  $em
     * @param string         $class
     * @param string         $metaClass
     * @param string         $messageClass
     * @param MessageManager $messageManager
     */
    public function __construct(EntityManager $em, string $class, string $metaClass, string $messageClass, MessageManager $messageManager)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->metaRepository = $em->getRepository($metaClass);
        $this->class = $em->getClassMetadata($class)->name;
        $this->metaClass = $em->getClassMetadata($metaClass)->name;
        $this->messageClass = $messageClass;
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadById($id)
    {
        return $this->repository->find($id);
    }

    public function getParticipantThreadsBySubjectAndRecipients(
        string $subject,
        ParticipantInterface $sender,
        ParticipantInterface $participant
    ): QueryBuilder {
        $qb = $this->getThreadBySubjectQueryBuilder($subject);
        $qb
            ->innerJoin('t.metadata', 'tmSender')
            ->innerJoin('tmSender.participant', 'pSender')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')
            ->andWhere('tmSender.isDeleted = :isDeleted')
            ->andWhere('tm.isDeleted = :isDeleted')
            ->andWhere('pSender.id = :sender_id')
            ->andWhere('p.id = :participant_id')
            ->setParameter('sender_id', $sender->getId())
            ->setParameter('participant_id', $participant->getId())
            ->setParameter('isDeleted', false, \PDO::PARAM_BOOL)
        ;

        return $qb;
    }

    public function findParticipantThreadsBySubjectAndRecipients(
        string $subject,
        ParticipantInterface $sender,
        ParticipantInterface $participant
    ): ?ThreadInterface {
        return $this->getParticipantThreadsBySubjectAndRecipients($subject, $sender, $participant)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant)
    {
        $subQueryMessage = $this->em->createQueryBuilder()
            ->select('max(m2.createdAt)')
            ->from($this->messageClass, 'm2')
            ->innerJoin('m2.thread', 't2')
            ->where('t2.id = t.id')
            ->getQuery()->getDQL();

        $qb = $this->repository->createQueryBuilder('t');

        return $qb->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')
            ->leftJoin('t.messages', 'm', 'WITH', $qb->expr()->in('m.createdAt', $subQueryMessage))
            ->andWhere('t.isSpam = :isSpam')
            ->setParameter('isSpam', false, \PDO::PARAM_BOOL)
            ->andWhere('tm.isDeleted = :isDeleted')
            ->setParameter('isDeleted', false, \PDO::PARAM_BOOL)
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())
            ->orderBy('m.createdAt', 'DESC')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantInboxThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantInboxThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantSentThreadsQueryBuilder(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')

            // the participant is in the thread participants
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())

            // the thread does not contain spam or flood
            ->andWhere('t.isSpam = :isSpam')
            ->setParameter('isSpam', false, \PDO::PARAM_BOOL)

            // the thread is not deleted by this participant
            ->andWhere('tm.isDeleted = :isDeleted')
            ->setParameter('isDeleted', false, \PDO::PARAM_BOOL)

            // there is at least one message written by this participant
            ->andWhere('tm.lastParticipantMessageDate IS NOT NULL')

            // sort by date of last message written by this participant
            ->orderBy('tm.lastParticipantMessageDate', 'DESC')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantSentThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantSentThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantDeletedThreadsQueryBuilder(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')

            // the participant is in the thread participants
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())

            // the thread is deleted by this participant
            ->andWhere('tm.isDeleted = :isDeleted')
            ->setParameter('isDeleted', true, \PDO::PARAM_BOOL)

            // sort by date of last message
            ->orderBy('tm.lastMessageDate', 'DESC')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantDeletedThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantDeletedThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantThreadsBySearchQueryBuilder(ParticipantInterface $participant, $search)
    {
        // remove all non-word chars
        $search = preg_replace('/[^\w]/', ' ', trim($search));
        // build a regex like (term1|term2)
        $regex = sprintf('/(%s)/', implode('|', explode(' ', $search)));

        throw new Exception('not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantThreadsBySearch(ParticipantInterface $participant, $search)
    {
        return $this->getParticipantThreadsBySearchQueryBuilder($participant, $search)
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadsCreatedBy(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder('t')
            ->innerJoin('t.createdBy', 'p')

            ->where('p.id = :participant_id')
            ->setParameter('participant_id', $participant->getId())

            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function findMetadataByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant)
    {
        return $this->metaRepository->createQueryBuilder('tm')
            ->innerJoin('tm.thread', 't')
            ->innerJoin('tm.participant', 'p')
            ->where('p.id = :participant_id')
            ->andWhere('t.id = :thread_id')
            ->setParameter('participant_id', $participant->getId())
            ->setParameter('thread_id', $thread->getId())

            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnreadThreads(ParticipantInterface $participant)
    {
        return 0 < $this->repository->createQueryBuilder('t')
            ->select('count(t.id)')
            ->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())
            ->andWhere('tm.isDeleted = :isDeleted')
            ->setParameter('isDeleted', true, \PDO::PARAM_BOOL)
            ->andWhere('tm.isRead = :isRead')
            ->setParameter('isRead', false, \PDO::PARAM_BOOL)
            ->getQuery()
            ->getSingleScalarResult()
         ;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        $readable->setIsReadByParticipant($participant, true);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        $readable->setIsReadByParticipant($participant, false);
    }

    /**
     * {@inheritdoc}
     */
    public function saveThread(ThreadInterface $thread, $andFlush = true)
    {
        $this->denormalize($thread);
        $this->em->persist($thread);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteThread(ThreadInterface $thread)
    {
        $this->em->remove($thread);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified comment thread class name.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreadBySubjectQueryBuilder($subject)
    {
        return $this->repository->createQueryBuilder('t')
            ->distinct()
            ->andWhere('t.isSpam = :isSpam')
            ->andWhere('t.subject = :subject')
            ->setParameter('subject', $subject)
            ->setParameter('isSpam', false, \PDO::PARAM_BOOL)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findSubjectThread($subject)
    {
        return $this->getThreadBySubjectQueryBuilder($subject)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks.
     */
    protected function denormalize(ThreadInterface $thread)
    {
        $this->doMetadata($thread);
        $this->doCreatedByAndAt($thread);
        $this->doDatesOfLastMessageWrittenByOtherParticipant($thread);
    }

    /**
     * Ensures that the thread metadata are up to date.
     */
    protected function doMetadata(ThreadInterface $thread)
    {
        // Participants
        foreach ($thread->getParticipants() as $participant) {
            $meta = $this->findMetadataByThreadAndParticipant($thread, $participant);
            if (!$meta) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($participant);

                $thread->addMetadata($meta);
            }
        }

        // Messages
        foreach ($thread->getMessages() as $message) {
            $meta = $this->findMetadataByThreadAndParticipant($thread, $message->getSender());
            if (!$meta) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($message->getSender());
                $thread->addMetadata($meta);
            }

            $meta->setLastParticipantMessageDate($message->getCreatedAt());
        }
    }

    /**
     * Ensures that the createdBy & createdAt properties are set.
     */
    protected function doCreatedByAndAt(ThreadInterface $thread)
    {
        if (!($message = $thread->getFirstMessage())) {
            return;
        }

        if (!$thread->getCreatedAt()) {
            $thread->setCreatedAt($message->getCreatedAt());
        }

        if (!$thread->getCreatedBy()) {
            $thread->setCreatedBy($message->getSender());
        }
    }

    /**
     * Update the dates of last message written by other participants.
     */
    protected function doDatesOfLastMessageWrittenByOtherParticipant(ThreadInterface $thread)
    {
        foreach ($thread->getAllMetadata() as $meta) {
            $participantId = $meta->getParticipant()->getId();
            $timestamp = 0;

            foreach ($thread->getMessages() as $message) {
                if ($participantId != $message->getSender()->getId()) {
                    $timestamp = max($timestamp, $message->getTimestamp());
                }
            }
            if ($timestamp) {
                $date = new DateTime();
                $date->setTimestamp($timestamp);
                $meta->setLastMessageDate($date);
            }
        }
    }

    protected function createThreadMetadata()
    {
        return new $this->metaClass();
    }
}
