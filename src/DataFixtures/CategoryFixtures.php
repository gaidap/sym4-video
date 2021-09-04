<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (['clever', 'funny', 'stupid', 'hot'] as $mainCategoryName) {
            $category = new Category();
            $category->setName($mainCategoryName);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
