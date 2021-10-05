<?php

namespace App\Categories;

use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoriesTreeAdminPage extends AbstractCategoriesTree
{

    private const UL_BEGIN = '<ul class="fa-ul text-left">';
    private const UL_CLOSE = '</ul>';
    private const LI_BEGIN = '<li>';
    private const LI_CLOSE = '</li>';

    protected function createCategoryListView(array $categories): void
    {
        $this->categoryListHtml .= self::UL_BEGIN;
        foreach ($categories as $category) {
            $this->categoryListHtml .= self::LI_BEGIN;
            $this->categoryListHtml .= $this->createListItemHtml($category);
            if (!empty($category['children'])) {
                $this->createCategoryListView($category['children']);
            }
            $this->categoryListHtml .= self::LI_CLOSE;
        }
        $this->categoryListHtml .= self::UL_CLOSE;
    }

    private function createListItemHtml($category): string
    {
        $id = $category['id'];
        $name = $this->slugger->slugify($category['name']);
        $editUrl = $this->getUrlGenerator()->generate(
            'edit_category',
            ['id' => $id]
        );
        $deleteUrl = $this->getUrlGenerator()->generate(
            'delete_category',
            ['id' => $id]
        );

        return "<i class='fa-li fa fa-arrow-right'></i>${name} <a href='${editUrl}'>edit</a> <a
                    onclick='return confirm(\"Are you sure?\");'
                    href='${deleteUrl}'>delete</a>";
    }

    public function buildCategoryList(): void
    {
        $categoryTree = $this->buildTree();
        $this->createCategoryListView($categoryTree);
    }
}
