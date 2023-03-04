<?php

namespace App\Repository\Contact;

use App\Entity\Contact\ContactTelephone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactTelephone>
 *
 * @method ContactTelephone|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactTelephone|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactTelephone[]    findAll()
 * @method ContactTelephone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactTelephoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactTelephone::class);
    }

    public function save(ContactTelephone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactTelephone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ContactTelephone[] Returns an array of ContactTelephone objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ContactTelephone
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
