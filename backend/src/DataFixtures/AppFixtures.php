<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Menu;
use App\Entity\Dish;
use App\Entity\Regime;
use App\Entity\ThemeMenu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 1. Création de l'instance utilisateur
        $admin = new User();

        // 2. Définition des informations de base
        $admin->setEmail('josé.admin@gmail.com')
              ->setFirstName('Admin')
              ->setLastName('ViteGourmand')
              ->setRoles(['ROLE_ADMIN']);

        // 3. Hachage du mot de passe
        // On passe l'objet $admin et le mot de passe en clair
        $hashedPassword = $this->hasher->hashPassword(
            $admin,
            'Q7AxG7!p'
        );
        $admin->setPassword($hashedPassword);

        // 4. On demande à Doctrine de préparer l'insertion
        $manager->persist($admin);

       // Tableau de données pour nos menus
        $menusData = [
            [
                'title' => 'Menu Express',
                'desc' => 'Entrée + Plat ou Plat + Dessert.',
                'price' => '15.50',
                'qty' => 100
            ],
            [
                'title' => 'Menu Gourmand',
                'desc' => 'La totale : Entrée, Plat, Fromage et Dessert.',
                'price' => '32.00',
                'qty' => 50
            ],
            [
                'title' => 'Menu Enfant',
                'desc' => 'Petit burger, frites maison et glace.',
                'price' => '10.00',
                'qty' => 30
            ],
            [
                'title' => 'Menu Festif',
                'desc' => 'Idéal pour les grandes tablées et événements.',
                'price' => '55.00',
                'qty' => 20
            ],
        ];

        // Boucle pour créer les menus automatiquement
        foreach ($menusData as $data) {
            $menu = new Menu();
            $menu->setTitleMenu($data['title'])
                 ->setDescription($data['desc'])
                 ->setPriceMenu($data['price'])
                 ->setRemainingQuantity($data['qty'])
                 ->setMinimumNumberOfPersons(1) // Valeur par défaut
                 ->setPrecautionMenu('Aucune')
                 ->setStoragePrecautions('Frais');

            $manager->persist($menu);
        }
        // 5. On exécute la requête SQL
        $manager->flush();
    }
}