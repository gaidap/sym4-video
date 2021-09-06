<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $mainCategories = $this->loadMainCategories($manager);

        $cleverSubCategories = $this->loadSubCategories(
            ['informatics', 'mathematics', 'biology'],
            $mainCategories['clever'],
            $manager
        );

        $funnySubCategories = $this->loadSubCategories(
            ['babies', 'cats', 'red pandas'],
            $mainCategories['funny'],
            $manager
        );

        $stupidSubCategories = $this->loadSubCategories(
            ['homer', 'cartman'],
            $mainCategories['stupid'],
            $manager
        );

        $homerSubCategories = $this->loadSubCategories(
            ['bart', 'do\''],
            $stupidSubCategories['homer'],
            $manager
        );

        $hotSubCategories = $this->loadSubCategories(
            ['motorcycles', 'game consoles'],
            $mainCategories['hot'],
            $manager
        );
    }

    /**
     * @param ObjectManager $manager
     * @return array
     */
    private function loadMainCategories(ObjectManager $manager): array
    {
        $mainCategories = [];
        foreach (['clever', 'funny', 'stupid', 'hot'] as $mainCategoryName) {
            $category = new Category();
            $category->setName($mainCategoryName);
            $manager->persist($category);
            $mainCategories[$mainCategoryName] = $category;
        }
        $manager->flush();

        return $mainCategories;
    }

    private function loadSubCategories($subCategoryNames, $parentCategory, ObjectManager $manager): array
    {
        $subCategories = [];

        foreach ($subCategoryNames as $subCategoryName) {
            $category = new Category();
            $category
                ->setParent($parentCategory)
                ->setName($subCategoryName)
            ;
            $manager->persist($category);
            $subCategories[$subCategoryName] = $category;
        }
        $manager->flush();

        return $subCategories;
    }
}
