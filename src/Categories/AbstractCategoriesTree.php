<?php

namespace App\Categories;

use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractCategoriesTree
{
    protected static bool $categoriesFetched = false;

    protected string $categoryListHtml = '';

    protected string $rootParentName = '';

    protected string $currentCategoryName = '';

    protected int $rootParentId;

    protected array $categoriesAsOptions = [];

    protected array $categories;

    protected AppExtension $slugger;

    private EntityManagerInterface $entityManager;

    private UrlGeneratorInterface $urlGenerator;


    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->categories = $this->fetchCategories();
        $this->slugger = new AppExtension();
    }

    public function getCategoryListHtml(): string
    {
        return $this->categoryListHtml;
    }

    public function getRootParentName(): string
    {
        return $this->rootParentName;
    }

    public function getCurrentCategoryName(): string
    {
        return $this->currentCategoryName;
    }

    public function getRootParentId(): int
    {
        return $this->rootParentId;
    }

    public function getCategoriesAsOptions(): array
    {
        return $this->categoriesAsOptions;
    }

    abstract protected function createCategoryListView(array $categories): void;

    protected function buildTree(int $parentId = null): array
    {
        $treeArr = [];
        foreach ($this->getCategories() as $category) {
            if ($category['parent_id'] === $parentId) {
                $children = $this->buildTree($category['id']);
                if ($children !== null) {
                    $category['children'] = $children;
                }
                $treeArr[] = $category;
            }
        }

        return $treeArr;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    private function fetchCategories(): array
    {
        if (self::$categoriesFetched) {
            return $this->categories;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->addSelect('c.id')
            ->addSelect('c.name')
            ->addSelect('p.id as parent_id')
            ->from('App:Category', 'c')
            ->leftJoin('c.parent', 'p', 'p.id = c.patent_id')
            ->getQuery();

        return $query->getArrayResult();
    }
}
