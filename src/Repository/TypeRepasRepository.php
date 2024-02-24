<?php

namespace App\Repository;

use App\Entity\TypeRepas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeRepas>
 *
 * @method TypeRepas|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeRepas|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeRepas[]    findAll()
 * @method TypeRepas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRepasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRepas::class);
    }

    //    /**
    //     * @return TypeRepas[] Returns an array of TypeRepas objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TypeRepas
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
