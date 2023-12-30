<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\CartPosition;
use App\Entity\Product;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppTestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * Test products.
         */
        $productBoat = new Product();
        $productBoat
            ->setName('Boat')
            ->setDescription('A time-honored boat in a rustic ambience.')
            ->setImageUrl('https://picsum.photos/id/211/300/200.jpg')
            ->setPrice(9999999)
            ->setCreated(new DateTime('2020-02-02 02:02:02'))
            ->setStatus(true);
        $manager->persist($productBoat);

        $productBike = new Product();
        $productBike
            ->setName('Balance bike')
            ->setDescription('Get the little ones going! The unique balance bike of the brand Eigenbau is only available in our store.')
            ->setImageUrl('https://picsum.photos/id/146/300/200.jpg')
            ->setPrice(29900)
            ->setCreated(new DateTime('2022-01-01 12:00:00'))
            ->setStatus(true);
        $manager->persist($productBike);

        $productCableCar = new Product();
        $productCableCar
            ->setName('Cable car')
            ->setDescription('Your very own cable car. Not everyone can say that about themselves! Impress your neighbors and show everyone what you can afford. No mountain at hand? Use our XXS model to get to the second floor. Never climb stairs again!')
            ->setImageUrl('https://picsum.photos/id/328/300/200.jpg')
            ->setPrice(999999999)
            ->setCreated(new DateTime('2023-12-31 00:00:00'))
            ->setStatus(false);
        $manager->persist($productCableCar);

        /**
         * Test carts.
         */
        $cart = new Cart();
        $cart
            ->setComment('Cart for testing')
            ->addPosition((new CartPosition)
                ->setProduct($productBike)
                ->setQuantity(2))
            ->addPosition((new CartPosition)
                ->setProduct($productCableCar)
                ->setQuantity(1));
        $manager->persist($cart);

        $cart = new Cart();
        $cart->setComment('Empty cart');
        $manager->persist($cart);

        $manager->flush();
    }
}
