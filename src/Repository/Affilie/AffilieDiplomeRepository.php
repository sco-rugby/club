<?php

namespace App\Repository\Affilie;

use App\Entity\Affilie\AffilieDiplome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AffilieDiplome>
 *
 * @method AffilieDiplome|null find($id, $lockMode = null, $lockVersion = null)
 * @method AffilieDiplome|null findOneBy(array $criteria, array $orderBy = null)
 * @method AffilieDiplome[]    findAll()
 * @method AffilieDiplome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffilieDiplomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AffilieDiplome::class);
    }

    public function save(AffilieDiplome $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AffilieDiplome $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AffilieDiplome[] Returns an array of AffilieDiplome objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AffilieDiplome
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
