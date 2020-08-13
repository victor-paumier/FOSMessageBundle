<?php

namespace FOS\MessageBundle\Util;

use FOS\MessageBundle\FormType\RecipientsType;
use InvalidArgumentException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @internal
 *
 * @see https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Util/LegacyFormHelper.php
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
final class LegacyFormHelper
{
    private static $map = [
        'FOS\UserBundle\Form\Type\UsernameFormType' => 'fos_user_username',
        RecipientsType::class => 'recipients_selector',
        EmailType::class => 'email',
        PasswordType::class => 'password',
        RepeatedType::class => 'repeated',
        TextType::class => 'text',
        TextareaType::class => 'textarea',
    ];

    public static function getType($class)
    {
        if (!self::isLegacy()) {
            return $class;
        }

        if (!isset(self::$map[$class])) {
            throw new InvalidArgumentException(sprintf('Form type with class "%s" can not be found. Please check for typos or add it to the map in LegacyFormHelper', $class));
        }

        return self::$map[$class];
    }

    public static function isLegacy()
    {
        return !method_exists(AbstractType::class, 'getBlockPrefix');
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
