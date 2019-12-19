<?php

namespace App\EventListener;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Customer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/** lier le customer au user courant */
class CustomerUserSubscriber implements EventSubscriberInterface
{

    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return array( KernelEvents::VIEW => ['setUser', EventPriorities::PRE_VALIDATE ]);
    }

    public function setUser(ViewEvent $event)
    {
        $entity = $event->getControllerResult();

        if($entity instanceof Customer && $event->getRequest()->getMethod() == 'POST'){
            /** Customer  */
            $entity->setUser( $this->security->getUser());
        }
    }
}