<?php

namespace App\Repository;

use App\Entity\Modules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;


/**
 * @extends ServiceEntityRepository<Modules>
 */
class ModulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modules::class);
    }

    //    /**
    //     * @return Modules[] Returns an array of Modules objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Modules
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    /**
     * Retourne le premier module que l'apprenant n'a jamais commencé.
     * "Pas commencé" = aucune ligne dans user_module_progress pour cet user.
     *
     * On respecte l'ordre pédagogique : chapter_order ASC, module_order ASC.
     */

    public function findNextForUser(User $user): ?Modules
    {
        // ── 1. Module en cours dans Progression ──────────────────────────────
        $inProgress = $this->createQueryBuilder('m')
            ->innerJoin(
                \App\Entity\Progression::class,
                'p',
                'WITH',
                'p.moduleId = m AND p.userId = :user AND p.completedAt IS NULL'
            )
            ->setParameter('user', $user)
            ->orderBy('p.startAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($inProgress !== null) {
            return $inProgress;
        }

        // ── 2. Premier module jamais commencé ─────────────────────────────────
        return $this->createQueryBuilder('m')
            ->innerJoin('m.chapterId', 'c')
            ->where('m.id NOT IN (
                SELECT IDENTITY(p2.moduleId)
                FROM App\Entity\Progression p2
                WHERE p2.userId = :user
            )')
            ->andWhere('m.id NOT IN (
                SELECT IDENTITY(ump.moduleId)
                FROM App\Entity\UserModuleProgress ump
                WHERE ump.userId = :user
            )')
            ->setParameter('user', $user)
            ->orderBy('c.chapter_order', 'ASC')
            ->addOrderBy('m.module_order', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
