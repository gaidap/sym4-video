<?php

namespace App\Categories;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractCategoriesTree
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    abstract public function getCategoryList(array $categories);

    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
