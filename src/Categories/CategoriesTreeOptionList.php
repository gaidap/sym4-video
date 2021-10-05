<?php

namespace App\Categories;

use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoriesTreeOptionList extends AbstractCategoriesTree
{

    protected function createCategoryListView(array $categories): void
    {
        $this->categoriesAsOptions = $categories;
    }

    private function formatCategoriesAsOptions(array $categories, int $repeat = 0): array
    {
        $result = [];
        foreach ($categories as $category) {
            $children = [];
            if (!empty($category['children'])) {
                $repeat += 2;
                $children = $this->formatCategoriesAsOptions($category['children'], $repeat);
                $repeat -= 2;
            }
            $result[] = [
                'name' => str_repeat('-', $repeat) . $category['name'],
                'id' => $category['id'],
            ];
           array_push($result, ...$children);
        }

        return $result;
    }

    public function buildCategoryListOptions(): void
    {
        $categoryTree = $this->formatCategoriesAsOptions($this->buildTree());
        $this->createCategoryListView($categoryTree);
    }
}
