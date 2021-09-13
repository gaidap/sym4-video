<?php

namespace App\Categories;

class CategoriesTreeFrontPage extends AbstractCategoriesTree
{

    private const UL_BEGIN = '<ul>';
    private const UL_CLOSE = '</ul>';
    private const LI_BEGIN = '<li>';
    private const LI_CLOSE = '</li>';

    public function createCategoryListHtml(array $categories): string
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

        return $this->categoryListHtml;
    }

    private function createLinkHtml($category): string
    {
        $id = $category['id'];
        $name = $category['name'];
        $url = $this->getUrlGenerator()->generate(
            'video_list',
            ['name' => $name, 'id' => $id]
        );

        return "<a href='${url}'>${name}</a>";
    }
}
