<?php

namespace FOS\MessageBundle\Controller;

use FOS\MessageBundle\Deleter\DeleterInterface;
use FOS\MessageBundle\FormFactory\AbstractMessageFormFactory;
use FOS\MessageBundle\FormHandler\AbstractMessageFormHandler;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;
use FOS\MessageBundle\Search\FinderInterface;
use FOS\MessageBundle\Search\QueryFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractProviderAwareController
{
    /**
     * Displays the authenticated participant inbox.
     *
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->provider->getInboxThreads();

        return $this->render('@FOSMessage/Message/inbox.html.twig', [
            'threads' => $threads,
        ]);
    }

    /**
     * Displays the authenticated participant deleted threads.
     *
     * @return Response
     */
    public function deletedAction()
    {
        $threads = $this->provider->getDeletedThreads();

        return $this->render('@FOSMessage/Message/deleted.html.twig', [
            'threads' => $threads,
        ]);
    }

    /**
     * Displays a thread, also allows to reply to it.
     *
     * @param Request $request,
     * @param AbstractMessageFormFactory $replyMessageFormFactory,
     * @param AbstractMessageFormHandler $formHandler
     * @param int $threadId the thread id
     *
     * @return Response
     */
    public function threadAction(
        Request $request,
        AbstractMessageFormFactory $replyMessageFormFactory,
        AbstractMessageFormHandler $formHandler,
        int $threadId
    ) {
        $thread = $this->provider->getThread($threadId);
        $form = $replyMessageFormFactory->create($thread);

        if ($message = $formHandler->process($form, $request)) {
            return $this->redirectToRoute('fos_message_thread_view', [
                'threadId' => $message->getThread()->getId(),
            ]);
        }

        return $this->render('@FOSMessage/Message/thread.html.twig', [
            'form' => $form->createView(),
            'thread' => $thread,
        ]);
    }

    /**
     * Create a new message thread.
     *
     * @param Request $request
     * @param AbstractMessageFormFactory $formFactory
     * @param AbstractMessageFormHandler $formHandler
     * @return Response
     */
    public function newThreadAction(
        Request $request,
        AbstractMessageFormFactory $formFactory,
        AbstractMessageFormHandler $formHandler
    ) {
        $form = $formFactory->create();

        if ($message = $formHandler->process($form, $request)) {
            return $this->redirectToRoute('fos_message_thread_view', [
                'threadId' => $message->getThread()->getId(),
            ]);
        }

        return $this->render('@FOSMessage/Message/newThread.html.twig', [
            'form' => $form->createView(),
            'data' => $form->getData(),
        ]);
    }

    /**
     * Deletes a thread.
     *
     * @param ThreadManagerInterface $threadManager
     * @param DeleterInterface $deleter
     * @param int $threadId the thread id
     *
     * @return RedirectResponse
     */
    public function deleteAction(ThreadManagerInterface $threadManager, DeleterInterface $deleter, int $threadId)
    {
        $thread = $this->provider->getThread($threadId);
        $deleter->markAsDeleted($thread);
        $threadManager->saveThread($thread);

        return $this->redirectToRoute('fos_message_inbox');
    }

    /**
     * Undeletes a thread.
     *
     * @param ThreadManagerInterface $threadManager
     * @param DeleterInterface $deleter
     * @param int $threadId
     *
     * @return RedirectResponse
     */
    public function undeleteAction(ThreadManagerInterface $threadManager, DeleterInterface $deleter, int $threadId)
    {
        $thread = $this->provider->getThread($threadId);
        $deleter->markAsUndeleted($thread);
        $threadManager->saveThread($thread);

        return $this->redirectToRoute('fos_message_inbox');
    }

    /**
     * Searches for messages in the inbox and sentbox.
     *
     * @param Request $request
     * @param QueryFactoryInterface $queryFactory
     * @param FinderInterface $finder
     *
     * @return Response
     */
    public function searchAction(Request $request, QueryFactoryInterface $queryFactory, FinderInterface $finder)
    {
        $query = $queryFactory->createFromRequest($request);
        $threads = $finder->find($query);

        return $this->render('@FOSMessage/Message/search.html.twig', [
            'query' => $query,
            'threads' => $threads,
        ]);
    }
}
