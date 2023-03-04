<?php

namespace App\Repository\Datawarehouse\Effectif;

use App\Entity\Jeu\Saison;
use App\Entity\Datawarehouse\Effectif\Duree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Duree>
 *
 * @method Duree|null find($id, $lockMode = null, $lockVersion = null)
 * @method Duree|null findOneBy(array $criteria, array $orderBy = null)
 * @method Duree[]    findAll()
 * @method Duree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DureeRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Duree::class);
    }

    public function save(Duree $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Duree $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteSaison(Saison $saison, bool $flush = false) {
        $nb = $this->_em->createQueryBuilder()
                ->delete($this->_entityName, 'd')
                ->where('d.saison = :saison')
                ->setParameter('saison', $saison->getId())
                ->getQuery()
                ->execute();
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $nb;
    }

//    /**
//     * @return Duree[] Returns an array of Duree objects
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
//    public function findOneBySomeField($value): ?Duree
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
