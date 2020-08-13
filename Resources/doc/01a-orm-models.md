Concrete classes for Doctrine ORM
=================================

This page lists some example implementations of FOSMessageBundle models for the Doctrine
ORM.

Given the examples below with their namespaces and class names, you need to configure
FOSMessageBundle to tell them about these classes.

Add the following to your `app/config/config.yml` file.

```yaml
# app/config/config.yml

fos_message:
    db_driver: orm
    thread_class: AppBundle\Entity\Thread
    message_class: AppBundle\Entity\Message
```

[Continue with the installation][]

Message class
-------------

```php
<?php
// src/AppBundle/Entity/Message.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Entity\Message as BaseMessage;use FOS\MessageBundle\Model\ParticipantInterface;use FOS\MessageBundle\Model\ThreadInterface;

/**
 * @ORM\Entity
 */
class Message extends BaseMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *   targetEntity="AppBundle\Entity\Thread",
     *   inversedBy="messages"
     * )
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\MessageMetadata",
     *   mappedBy="message",
     *   cascade={"all"}
     * )
     * @var MessageMetadata[]|Collection
     */
    protected $metadata;
}
```

MessageMetadata class
---------------------

```php
<?php
// src/AppBundle/Entity/MessageMetadata.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\MessageMetadata as BaseMessageMetadata;use FOS\MessageBundle\Model\MessageInterface;use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 */
class MessageMetadata extends BaseMessageMetadata
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *   targetEntity="AppBundle\Entity\Message",
     *   inversedBy="metadata"
     * )
     * @var MessageInterface
     */
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var ParticipantInterface
     */
    protected $participant;
}
```

Thread class
------------

```php
<?php
// src/AppBundle/Entity/Thread.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Entity\Thread as BaseThread;use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 */
class Thread extends BaseThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var ParticipantInterface
     */
    protected $createdBy;

    /**
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\Message",
     *   mappedBy="thread"
     * )
     * @var Message[]|Collection
     */
    protected $messages;

    /**
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\ThreadMetadata",
     *   mappedBy="thread",
     *   cascade={"all"}
     * )
     * @var ThreadMetadata[]|Collection
     */
    protected $metadata;
}
```

ThreadMetadata class
--------------------

```php
<?php
// src/AppBundle/Entity/ThreadMetadata.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;use FOS\MessageBundle\Model\ParticipantInterface;use FOS\MessageBundle\Model\ThreadInterface;

/**
 * @ORM\Entity
 */
class ThreadMetadata extends BaseThreadMetadata
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *   targetEntity="AppBundle\Entity\Thread",
     *   inversedBy="metadata"
     * )
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var ParticipantInterface
     */
    protected $participant;
}
```

[Continue with the installation]: 01-installation.md
