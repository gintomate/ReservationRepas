<?php

namespace App\Repository;

use App\Entity\JourReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JourReservation>
 *
 * @method JourReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method JourReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method JourReservation[]    findAll()
 * @method JourReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JourReservation::class);
    }

//    /**
//     * @return JourReservation[] Returns an array of JourReservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JourReservation
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
