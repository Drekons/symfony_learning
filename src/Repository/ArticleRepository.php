<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false)
    {
        $qb = $this->withAuthor($this->latest());

        if ($search) {
            $qb
                ->andWhere(
                    'a.title LIKE :search OR av.firstName LIKE :search OR a.body LIKE :search '
                    . 'OR a.description LIKE :search'
                )
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb;
    }

    private function withAuthor(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->leftJoin('a.author', 'av')
            ->addSelect('av');
    }

    /**
     * @param  QueryBuilder|null  $qb
     *
     * @return QueryBuilder
     */
    protected function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('a');
    }

    private function latest(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->orderBy('a.publishedAt', 'DESC');
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findLatestPublishedWithCommentsWithTags()
    {
        return $this->published($this->withComments($this->withTags($this->latest())))
            ->getQuery()
            ->getResult();
    }

    private function published(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere("a.publishedAt <= :now")
            ->setParameter('now', (new \DateTime())->format('Y-m-d H:i:s'));
    }

    private function withComments(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->leftJoin('a.comments', 'c')
            ->addSelect("c");
    }

    private function withTags(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->leftJoin('a.tags', 't')
            ->addSelect('t');
    }

    public function findAllPublishedLastWeek()
    {
        return $this->published($this->withAuthor($this->latest()))
            ->andWhere('a.publishedAt >= :week_ago')
            ->setParameter('week_ago', new \DateTime('-1 week'))
            ->getQuery()
            ->getResult();
    }

    public function findAllPublishedByPeriod(\DateTime $dateFrom, \DateTime $dateTo)
    {
        return $this->published($this->withAuthor($this->latest()))
            ->select('count(a.id)')
            ->andWhere('a.publishedAt >= :from AND a.publishedAt <= :to')
            ->setParameter('from', $dateFrom)
            ->setParameter('to', $dateTo)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllCreatedByPeriod(\DateTime $dateFrom, \DateTime $dateTo)
    {
        return $this->published($this->withAuthor($this->latest()))
            ->select('count(a.id)')
            ->andWhere('a.createdAt >= :from AND a.createdAt <= :to')
            ->setParameter('from', $dateFrom)
            ->setParameter('to', $dateTo)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
