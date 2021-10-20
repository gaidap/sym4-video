<?php

namespace App\Tests\Utils;

use App\Categories\CategoriesTreeAdminPage;
use App\Categories\CategoriesTreeFrontPage;
use App\Categories\CategoriesTreeOptionList;
use App\Twig\AppExtension;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{


    /**
     * @var CategoriesTreeFrontPage|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mockedCategoriesTreeFrontPage;

    /**
     * @var CategoriesTreeAdminPage|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mockedCategoriesTreeAdminPage;

    /**
     * @var CategoriesTreeOptionList|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mockedCategoriesTreeOptionList;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlGenerator = $kernel->getContainer()->get('router');
        foreach ([
                     CategoriesTreeFrontPage::class,
                     CategoriesTreeAdminPage::class,
                     CategoriesTreeOptionList::class,
                 ] as $treeClass) {
            $name = 'mocked' . explode('\\', $treeClass)[2];
            $this->$name = $this
                ->getMockBuilder($treeClass)
                ->disableOriginalConstructor()
                ->addMethods([])
                ->getMock();
            $this->$name->setUrlGenerator($urlGenerator);
        }
    }

    /**
     * @dataProvider dataForCategoryTreeFrontPage
     */
    public function testCategoryTreeFrontPage($expectedHtml, $categories, $id): void
    {
        $this->mockedCategoriesTreeFrontPage->setCategories($categories);
        $this->mockedCategoriesTreeFrontPage->setSlugger(new AppExtension());
        $mainParentId = $this->mockedCategoriesTreeFrontPage->findRootParent($id)['id'];
        $this->mockedCategoriesTreeFrontPage->buildCategoryListWithParent($mainParentId);
        $this->assertSame($expectedHtml, $this->mockedCategoriesTreeFrontPage->getCategoryListHtml());
    }

    public function dataForCategoryTreeFrontPage(): Generator
    {
        yield [
            "<ul><li><a href='/video-list/category/babies,8'>babies</a></li>" .
            "<li><a href='/video-list/category/cats,9'>cats</a></li>" .
            "<li><a href='/video-list/category/red-pandas,10'>red-pandas</a></li></ul>",
            [
                ["id" => 2, "name" => "funny", "parent_id" => null],
                ["id" => 8, "name" => "babies", "parent_id" => 2],
                ["id" => 9, "name" => "cats", "parent_id" => 2],
                ["id" => 10, "name" => "red-pandas", "parent_id" => 2],
            ],
            2,
        ];
    }

    /**
     * @dataProvider dataForCategoryTreeAdminPage
     */
    public function testCategoryTreeAdminPage($expectedHtml, $categories): void
    {
        $this->mockedCategoriesTreeAdminPage->setCategories($categories);
        $this->mockedCategoriesTreeAdminPage->setSlugger(new AppExtension());
        $this->mockedCategoriesTreeAdminPage->buildCategoryList();
        $this->assertSame($expectedHtml, $this->mockedCategoriesTreeAdminPage->getCategoryListHtml());
    }

    public function dataForCategoryTreeAdminPage(): Generator
    {
        yield [
            "<ul class=\"fa-ul text-left\"><li><i class='fa-li fa fa-arrow-right'></i>funny <a href='/admin/edit-category/2'>edit</a> <a
                    onclick='return confirm(\"Are you sure?\");'
                    href='/admin/delete-category/2'>delete</a><ul class=\"fa-ul text-left\"><li><i class='fa-li fa fa-arrow-right'></i>babies <a href='/admin/edit-category/8'>edit</a> <a
                    onclick='return confirm(\"Are you sure?\");'
                    href='/admin/delete-category/8'>delete</a></li><li><i class='fa-li fa fa-arrow-right'></i>cats <a href='/admin/edit-category/9'>edit</a> <a
                    onclick='return confirm(\"Are you sure?\");'
                    href='/admin/delete-category/9'>delete</a></li><li><i class='fa-li fa fa-arrow-right'></i>red-pandas <a href='/admin/edit-category/10'>edit</a> <a
                    onclick='return confirm(\"Are you sure?\");'
                    href='/admin/delete-category/10'>delete</a></li></ul></li></ul>",
            [
                ["id" => 2, "name" => "funny", "parent_id" => null],
                ["id" => 8, "name" => "babies", "parent_id" => 2],
                ["id" => 9, "name" => "cats", "parent_id" => 2],
                ["id" => 10, "name" => "red-pandas", "parent_id" => 2],
            ],
        ];
    }

    /**
     * @dataProvider dataForCategoryTreeOptionList
     */
    public function testCategoryTreeOptionList($expectedOptions, $categories): void
    {
        $this->mockedCategoriesTreeOptionList->setCategories($categories);
        $this->mockedCategoriesTreeOptionList->setSlugger(new AppExtension());
        $this->mockedCategoriesTreeOptionList->buildCategoryListOptions();
        $this->assertSame($expectedOptions, $this->mockedCategoriesTreeOptionList->getCategoriesAsOptions());
    }

    public function dataForCategoryTreeOptionList(): Generator
    {
        yield [
            [
                [
                    'name' => 'funny',
                    'id' => 2,
                ],
                [
                    'name' => '--babies',
                    'id' => 8,
                ],
                [
                    'name' => '--cats',
                    'id' => 9,
                ],
                [
                    'name' => '--red-pandas',
                    'id' => 10,
                ],
            ],
            [
                ["id" => 2, "name" => "funny", "parent_id" => null],
                ["id" => 8, "name" => "babies", "parent_id" => 2],
                ["id" => 9, "name" => "cats", "parent_id" => 2],
                ["id" => 10, "name" => "red-pandas", "parent_id" => 2],
            ],
        ];
    }
}
