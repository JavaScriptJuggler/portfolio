<?php

namespace App\Repository;

use App\Entity\ExperienceOverView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExperienceOverView>
 *
 * @method ExperienceOverView|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExperienceOverView|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExperienceOverView[]    findAll()
 * @method ExperienceOverView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExperienceOverViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExperienceOverView::class);
    }

//    /**
//     * @return ExperienceOverView[] Returns an array of ExperienceOverView objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ExperienceOverView
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
