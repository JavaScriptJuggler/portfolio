<?php

namespace App\Repository;

use App\Entity\OfficeExperiences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OfficeExperiences>
 *
 * @method OfficeExperiences|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfficeExperiences|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfficeExperiences[]    findAll()
 * @method OfficeExperiences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeExperiencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfficeExperiences::class);
    }

//    /**
//     * @return OfficeExperiences[] Returns an array of OfficeExperiences objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OfficeExperiences
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
