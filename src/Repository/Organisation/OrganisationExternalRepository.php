<?php

namespace App\Repository\Organisation;

use App\Entity\Organisation\OrganisationExternal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganisationExternal>
 *
 * @method OrganisationExternal|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganisationExternal|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganisationExternal[]    findAll()
 * @method OrganisationExternal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganisationExternalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganisationExternal::class);
    }

    public function save(OrganisationExternal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrganisationExternal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OrganisationExternal[] Returns an array of OrganisationExternal objects
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

//    public function findOneBySomeField($value): ?OrganisationExternal
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
