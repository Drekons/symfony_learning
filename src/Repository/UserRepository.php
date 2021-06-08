<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * UserRepository constructor.
     *
     * @param  ManagerRegistry  $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return array
     */
    public function findAllSortedByName(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.firstName', 'ASC')
            ->getQuery()
            ->getResult() ?: [];
    }

    /**
     * @return array
     */
    public function findAllActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult() ?: [];
    }

    /**
     * @return array
     */
    public function getAdminsList(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'."ROLE_ADMIN".'"%')
            ->getQuery()
            ->getResult() ?: [];
    }

    /**
     * @param  \DateTime  $dateFrom
     * @param  \DateTime  $dateTo
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCountUsersByPeriod(\DateTime $dateFrom, \DateTime $dateTo): int
    {
        return (int)$this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.createdAt >= :from')
            ->andWhere('u.createdAt <= :to')
            ->setParameter('from', $dateFrom)
            ->setParameter('to', $dateTo)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
