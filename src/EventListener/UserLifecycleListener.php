<?php

namespace App\EventListener;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;

// #[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
class UserLifecycleListener
{
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    // public function postUpdate(User $user, PostUpdateEventArgs $event): void
    // {
    //     $em = $event->getObjectManager();

    //     if(!$user->getUserDetails()) {
    //         $userDetails =  new UserDetails();
    //         $user->setUserDetails($userDetails);
    //         $em->persist($userDetails);
    //         $em->persist($user);
    //         $em->flush();
    //     }
    // }

    public function postPersist(User $user, PostPersistEventArgs $event): void
    {
        $em = $event->getObjectManager();
        
        if(!$user->getCart()) {
            $cart =  new Cart();
            $cart->setUser($user);
            $em->persist($cart);
            $em->flush();
        }
    }
}