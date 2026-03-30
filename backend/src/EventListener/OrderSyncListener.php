<?php
// src/EventListener/OrderSyncListener.php
namespace App\EventListener;


use App\Document\MenuStats;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\DocumentManager;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
class OrderSyncListener
{
    public function __construct(private DocumentManager $dm) {}

    public function postPersist(Order $order, LifecycleEventArgs $event): void
    {
       
        foreach ($order->getOrderItems() as $contient) {
            $stats = new MenuStats();
            $stats->setMenuId($contient->getMenu()->getId());
            $stats->setMenuName($contient->getMenu()->getTitleMenu());
            // Calcul du CA pour cette ligne
            $stats->setPrice($contient->getQuantity() * $contient->getPriceUnit());
            $stats->setCreatedAt(new \DateTimeImmutable()); // Date de création du document

            $this->dm->persist($stats);
        }
        $this->dm->flush(); // Envoi vers MongoDB
    }
}