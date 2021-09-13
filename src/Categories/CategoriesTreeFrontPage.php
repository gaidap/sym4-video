<?php

namespace App\Categories;

use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoriesTreeFrontPage extends AbstractCategoriesTree
{

    private const UL_BEGIN = '<ul>';
    private const UL_CLOSE = '</ul>';
    private const LI_BEGIN = '<li>';
    private const LI_CLOSE = '</li>';

    private AppExtension $slugger;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($entityManager, $urlGenerator);
        $this->slugger = new AppExtension();
    }

    protected function createCategoryListHtml(array $categories): void
    {
        $this->categoryListHtml .= self::UL_BEGIN;
        foreach ($categories as $category) {
            $this->categoryListHtml .= self::LI_BEGIN;
            $this->categoryListHtml .= $this->createLinkHtml($category);
            if (!empty($category['children'])) {
                $this->createCategoryListHtml($category['children']);
            }
            $this->categoryListHtml .= self::LI_CLOSE;
        }
        $this->categoryListHtml .= self::UL_CLOSE;
    }

    private function createLinkHtml($category): string
    {
        $id = $category['id'];
        $name = $this->slugger->slugify($category['name']);
        $url = $this->getUrlGenerator()->generate(
            'video_list',
            ['name' => $name, 'id' => $id]
        );

        return "<a href='${url}'>${name}</a>";
    }

    public function createCategoryListWithParentHtml(int $id): void
    {
        $parentData = $this->findRootParent($id);
        $this->rootParentId = $parentData['id'];
        $this->rootParentName = $parentData['name'];

        $categories = $this->getCategories();
        $key = $this->findKey($id, $categories);
        $this->currentCategoryName = $categories[$key]['name'];

        $categoryTree = $this->buildTree($this->rootParentId);
        $this->createCategoryListHtml($categoryTree);
    }

    private function findRootParent(int $id): array
    {
        $categories = $this->getCategories();
        $key = $this->findKey($id, $categories);
        $parent_id = $categories[$key]['parent_id'];

        if ($parent_id !== null) {
            return $this->findRootParent($parent_id);
        }

        return [
            'id' => $categories[$key]['id'],
            'name' => $categories[$key]['name'],
        ];
    }

    private function findKey(int $id, array $categories)
    {
        return array_search($id, array_column($categories, 'id'), true);
    }


}
