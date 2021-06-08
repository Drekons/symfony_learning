<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false)
    {
        $qb = $this->withArticles($this->latest());

        if ($search) {
            $qb
                ->andWhere('t.name LIKE :search OR t.slug LIKE :search OR a.title LIKE :search')
                ->setParameter('search', '%' . $search . '%')
            ;
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb;
    }

    private function latest(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->orderBy('t.createdAt', 'DESC');
    }

    private function withArticles(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->leftJoin('t.articles', 'a')
            ->addSelect('a');
    }

    /**
     * @param QueryBuilder|null $qb
     *
     * @return QueryBuilder
     */
    protected function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('t');
    }
}
