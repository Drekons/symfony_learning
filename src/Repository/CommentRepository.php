<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false)
    {
        $qb = $this->latest($this->withArticle());

        if ($search) {
            $qb
                ->andWhere('c.content LIKE :search OR c.authorName LIKE :search OR a.title LIKE :search')
                ->setParameter('search', '%' . $search . '%')
            ;
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb;
    }

    public function findLatestWithArticle(int $count)
    {
        return $this->latest($this->withArticle())
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    private function latest(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->orderBy('c.createdAt', 'DESC');
    }

    /**
     * @param QueryBuilder|null $qb
     *
     * @return QueryBuilder
     */
    protected function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('c');
    }

    private function withArticle(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->innerJoin('c.article', 'a')
            ->addSelect('a');
    }

}
