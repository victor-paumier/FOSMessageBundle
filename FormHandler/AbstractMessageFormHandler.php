<?php

namespace FOS\MessageBundle\FormHandler;

use FOS\MessageBundle\Composer\ComposerInterface;
use FOS\MessageBundle\FormModel\AbstractMessage;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use FOS\MessageBundle\Sender\SenderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles messages forms, from binding request to sending the message.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageFormHandler
{
    protected $composer;
    protected $sender;
    protected $participantProvider;

    /**
     * @param ComposerInterface            $composer
     * @param SenderInterface              $sender
     * @param ParticipantProviderInterface $participantProvider
     */
    public function __construct(ComposerInterface $composer, SenderInterface $sender, ParticipantProviderInterface $participantProvider)
    {
        $this->composer = $composer;
        $this->sender = $sender;
        $this->participantProvider = $participantProvider;
    }

    /**
     * Processes the form with the request.
     *
     * @param Form $form
     * @param Request $request
     *
     * @return MessageInterface|false the sent message if the form is bound and valid, false otherwise
     */
    public function process(Form $form, Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return false;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $this->composeMessage($form->getData());
            $this->sender->send($message);

            return $message;
        }

        return false;
    }

    /**
     * Composes a message from the form data.
     *
     * @param AbstractMessage $message
     *
     * @return MessageInterface the composed message ready to be sent
     */
    abstract protected function composeMessage(AbstractMessage $message);

    /**
     * Gets the current authenticated user.
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
