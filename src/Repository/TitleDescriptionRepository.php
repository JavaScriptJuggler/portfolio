<?php

namespace App\Repository;

use App\Entity\TitleDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TitleDescription>
 *
 * @method TitleDescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method TitleDescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method TitleDescription[]    findAll()
 * @method TitleDescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TitleDescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TitleDescription::class);
    }

//    /**
//     * @return TitleDescription[] Returns an array of TitleDescription objects
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

//    public function findOneBySomeField($value): ?TitleDescription
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
