<?php

namespace FOS\MessageBundle\ModelManager;

use Doctrine\ORM\QueryBuilder;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ThreadMetadata;

/**
 * Interface to be implemented by comment thread managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comment threads should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ThreadManagerInterface extends ReadableManagerInterface
{
    /**
     * Finds a thread by its ID.
     *
     * @return ThreadInterface or null
     */
    public function findThreadById($id);

    /**
     * Finds not deleted threads for a participant,
     * containing at least one message not written by this participant,
     * ordered by last message not written by this participant in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $participant
     *
     * @return QueryBuilder a query builder suitable for pagination
     */
    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant);

    /**
     * Finds not deleted threads for a participant,
     * containing at least one message not written by this participant,
     * ordered by last message not written by this participant in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $participant
     *
     * @return ThreadInterface[]
     */
    public function findParticipantInboxThreads(ParticipantInterface $participant);

    /**
     * Finds deleted threads from a participant,
     * ordered by last message date.
     *
     * @param ParticipantInterface $participant
     *
     * @return QueryBuilder a query builder suitable for pagination
     */
    public function getParticipantDeletedThreadsQueryBuilder(ParticipantInterface $participant);

    /**
     * Finds deleted threads from a participant,
     * ordered by last message date.
     *
     * @param ParticipantInterface $participant
     *
     * @return ThreadInterface[]
     */
    public function findParticipantDeletedThreads(ParticipantInterface $participant);

    /**
     * Finds not deleted threads for a participant,
     * matching the given search term
     * ordered by last message not written by this participant in reverse order.
     *
     * @param ParticipantInterface $participant
     * @param string               $search
     *
     * @return QueryBuilder a query builder suitable for pagination
     */
    public function getParticipantThreadsBySearchQueryBuilder(ParticipantInterface $participant, $search);

    /**
     * Finds not deleted threads for a participant,
     * matching the given search term
     * ordered by last message not written by this participant in reverse order.
     *
     * @param ParticipantInterface $participant
     * @param string               $search
     *
     * @return ThreadInterface[]
     */
    public function findParticipantThreadsBySearch(ParticipantInterface $participant, $search);

    /**
     * Find threadMetadata by thread and participant to avoid iteration
     *
     * @param ThreadInterface $thread
     * @param ParticipantInterface $participant
     * @return ThreadMetadata|null
     */
    public function findMetadataByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant);

    /**
     * Gets threads created by a participant.
     *
     * @param ParticipantInterface $participant
     *
     * @return ThreadInterface[]
     */
    public function findThreadsCreatedBy(ParticipantInterface $participant);

    /**
     * Creates an empty comment thread instance.
     *
     * @return ThreadInterface
     */
    public function createThread();

    /**
     * Saves a thread.
     *
     * @param ThreadInterface $thread
     * @param bool            $andFlush Whether to flush the changes (default true)
     */
    public function saveThread(ThreadInterface $thread, $andFlush = true);

    /**
     * Deletes a thread
     * This is not participant deletion but real deletion.
     *
     * @param ThreadInterface $thread the thread to delete
     */
    public function deleteThread(ThreadInterface $thread);

    /**
     * Check if participant has unreadThread
     *
     * @param ParticipantInterface $participant
     * @return bool
     */
    public function hasUnreadThreads(ParticipantInterface $participant);

    /**
     * Find thread by subject
     *
     * @param string $subject
     *
     * @return QueryBuilder a query builder
     */
    public function getThreadBySubjectQueryBuilder($subject);

    /**
     * Get thread by subject
     *
     * @param string $subject
     *
     * @return ThreadInterface|null
     */
    public function findSubjectThread($subject);

    public function getParticipantThreadsBySubjectAndRecipients(
        string $subject,
        ParticipantInterface $sender,
        ParticipantInterface $participant
    ): QueryBuilder;

    public function findParticipantThreadsBySubjectAndRecipients(
        string $subject,
        ParticipantInterface $sender,
        ParticipantInterface $participant
    ): ?ThreadInterface;
}
