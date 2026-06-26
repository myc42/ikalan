<?php

namespace App\Repository;

use App\Entity\UserModuleProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserModuleProgress>
 */
class UserModuleProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserModuleProgress::class);
    }

    //    /**
    //     * @return UserModuleProgress[] Returns an array of UserModuleProgress objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserModuleProgress
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


     
    /**
     * Trouve le premier module dans la fenêtre SRS ouverte aujourd'hui.
     * Priorité au module dont la fenêtre expire le plus tôt (risque d'oubli).
     */
    public function findOneInWindow(mixed $user): ?UserModuleProgress
    {
        $today = new \DateTimeImmutable('today');
 
        return $this->createQueryBuilder('p')
            ->where('p.userId = :user')
            ->andWhere('p.windowStartAt <= :today')
            ->andWhere('p.windowEndAt >= :today')
            ->andWhere('p.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->setParameter('statuses', ['active', 'review'])
            ->orderBy('p.windowEndAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
