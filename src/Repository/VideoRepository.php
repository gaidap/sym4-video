<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);
        $this->paginator = $paginator;
    }


    public function findAllInPage($page): PaginationInterface
    {
        $query = $this->createQueryBuilder('v')->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    public function findByChildIds(array $ids, int $page = 1, ?string $order = 'ASC'): PaginationInterface
    {
        $order = $order === 'RATING' ? 'ASC' : $order; // TODO implement order by rating
        $query = $this
            ->createQueryBuilder('v')
            ->andWhere('v.category IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy("v.title", $order)
            ->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    public function findByTitle(string $searchQuery, int $page = 1, ?string $order = 'ASC'): PaginationInterface
    {
        $order = $order === 'RATING' ? 'ASC' : $order; // TODO implement order by rating
        $query = $this->createQueryBuilder('v');
        foreach ($this->prepareSearchQuery($searchQuery) as $index => $searchTerm) {
            $query
                ->orWhere("v.title like :t_$index")
                ->setParameter("t_$index", '%' . trim($searchTerm) . '%')
            ;
        }
        $query = $query
            ->orderBy("v.title", $order)
            ->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    private function prepareSearchQuery(string $searchQuery): array
    {
        return explode(' ', $searchQuery);
    }
}
