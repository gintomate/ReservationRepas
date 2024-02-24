<?php

namespace App\Repository;

use App\Entity\SemaineReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SemaineReservation>
 *
 * @method SemaineReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SemaineReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SemaineReservation[]    findAll()
 * @method SemaineReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SemaineReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SemaineReservation::class);
    }

    //    /**
    //     * @return SemaineReservation[] Returns an array of SemaineReservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SemaineReservation
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
