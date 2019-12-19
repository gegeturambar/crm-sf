<?php

namespace App\EventListener;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderSubscriber implements EventSubscriberInterface
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return array( KernelEvents::VIEW => ['encodePassword' , EventPriorities::PRE_WRITE ]);
    }

    public function encodePassword(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if($entity instanceof User && $event->getRequest()->getMethod() == 'POST'){
            /** User  */
            $entity->setPassword($this->encoder->encodePassword($entity,$entity->getPassword()));
        }
    }
}