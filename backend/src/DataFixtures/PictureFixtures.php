<?php

namespace App\DataFixtures;


use App\Entity\{Picture, Restaurant};
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;


class PictureFixtures extends Fixture implements DependentFixtureInterface
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {

            $picture = (new Picture())
                ->setTitle("Image n°$i")
                ->setSlug("slug-article-title")
                ->setRestaurant(
                    $this->getReference(RestaurantFixtures::RESTAURANT_REFERENCE . random_int(1, 20) , Restaurant::class)
                )
                ->setCreatedAt(new DateTimeImmutable());
            $manager->persist($picture);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class
        ];
    }
}



