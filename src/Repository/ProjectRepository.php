<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository implements PaginatorRepositoryInterface
{
    /**
     * Use a constant for simplisity
     * Statuses should be the same in all environments
     */
    public const STATUSES = [
        'new' => 1,
        'pending' => 2,
        'failed' => 3,
        'done' => 4,
    ];

    /**
     * Limit for projects per page/query
     * @var int
     */
    private int $limit;

    public function __construct(ManagerRegistry $registry, int $limit)
    {
        parent::__construct($registry, Project::class);

        $this->limit = $limit;
    }

    /**
     * Get repository limit
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Get the query builder for the projects
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.deleted = :deleted')
            ->setParameter('deleted', 0)
            ->orderBy('p.id', 'DESC');
    }

    /**
     * Get the pagination result on the repository results
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param array $data
     *
     * @return PaginationInterface
     */
    public function getPagination(Request $request, PaginatorInterface $paginator, array $data = []): PaginationInterface
    {
        return $paginator->paginate(
            $this->getQueryBuilder(),
            $request->query->getInt('page', 1),
            $this->getLimit()
        );
    }

    // /**
    //  * @return Project[] Returns an array of Project objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
