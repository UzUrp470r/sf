<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository implements PaginatorRepositoryInterface
{
    /**
     * Limit for projects per page/query
     * @var int
     */
    private int $limit;

    public function __construct(ManagerRegistry $registry, int $limit)
    {
        parent::__construct($registry, Task::class);

        $this->limit = $limit;
    }

    /**
     * Find all tasks for a specific project ID
     * @param int $value
     *
     * @return [type]
     */
    public function findTasksByProjectId(int $value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.project = :project')
            ->setParameter('project', $value)
            ->andWhere('t.deleted = :deleted')
            ->setParameter('deleted', 0)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults((int)$this->limit)
            ->getQuery()
            ->getResult()
        ;
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
     * Get the query builder for the tasks
     *
     * @param array $data
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(array $data): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.deleted = :deleted')
            ->setParameter('deleted', 0)
            ->andWhere('t.project = :project')
            ->setParameter('project', $data['project'])
            ->orderBy('t.id', 'DESC');
    }

    /**
     * Get the pagination result on the repository results
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param array $data
     *
     * @return PaginationInterface
     */
    public function getPagination(Request $request, PaginatorInterface $paginator, array $data = []): PaginationInterface
    {
        return $paginator->paginate(
            $this->getQueryBuilder($data),
            $request->query->getInt('page', 1),
            $this->getLimit()
        );
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
