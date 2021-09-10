<?php

namespace App\Categories;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractCategoriesTree
{
    private EntityManagerInterface $entityManager;

    private UrlGeneratorInterface $urlGenerator;

    private array $categories;

    protected static bool $categoriesFetched = false;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->categories = $this->fetchCategories();
    }

    abstract public function getCategoryList(array $categories);

    /**
     * @return array
     */
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
        $query = $qb->addSelect('*')->from('category', 'c')->getQuery();

        return $query->getArrayResult();
    }
}
