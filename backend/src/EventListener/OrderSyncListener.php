<?php

// src/EventListener/OrderSyncListener.php
namespace App\EventListener;

use App\Document\MenuStats;
use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\DocumentManager;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: OrderItem::class)]
class OrderSyncListener
{
    public function __construct(private DocumentManager $dm) {}

    public function postPersist(OrderItem $orderItem, LifecycleEventArgs $event): void
    {
        $stats = new MenuStats();
        $stats->setMenuId($orderItem->getMenu()->getId());
        $stats->setMenuName($orderItem->getMenu()->getTitleMenu());
        $stats->setPrice((float) $orderItem->getQuantity() * (float) $orderItem->getPriceUnit());
        $stats->setCreatedAt(new \DateTimeImmutable());

        $this->dm->persist($stats);
        $this->dm->flush();
    }
}