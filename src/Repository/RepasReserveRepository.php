<?php

namespace App\Repository;

use App\Entity\RepasReserve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RepasReserve>
 *
 * @method RepasReserve|null find($id, $lockMode = null, $lockVersion = null)
 * @method RepasReserve|null findOneBy(array $criteria, array $orderBy = null)
 * @method RepasReserve[]    findAll()
 * @method RepasReserve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepasReserveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RepasReserve::class);
    }

    //    /**
    //     * @return RepasReserve[] Returns an array of RepasReserve objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RepasReserve
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
