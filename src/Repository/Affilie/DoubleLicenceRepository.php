<?php

namespace App\Repository\Affilie;

use App\Entity\Affilie\DoubleLicence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DoubleLicence>
 *
 * @method DoubleLicence|null find($id, $lockMode = null, $lockVersion = null)
 * @method DoubleLicence|null findOneBy(array $criteria, array $orderBy = null)
 * @method DoubleLicence[]    findAll()
 * @method DoubleLicence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoubleLicenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoubleLicence::class);
    }

    public function save(DoubleLicence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DoubleLicence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DoubleLicence[] Returns an array of DoubleLicence objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DoubleLicence
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
