<?php

namespace FOS\MessageBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\ModelManager\MessageManager as BaseMessageManager;

/**
 * Default ORM MessageManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageManager extends BaseMessageManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $em->getClassMetadata($class)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
        $this->em->persist($message);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }
}
