<?php

namespace App\Repository\Datawarehouse\Licence;

use App\Entity\Jeu\Saison;
use App\Entity\Datawarehouse\Licence\LicenceSaison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SaisonRepositoryInterface;

/**
 * @extends ServiceEntityRepository<LicenceSaison>
 *
 * @method LicenceSaison|null find($id, $lockMode = null, $lockVersion = null)
 * @method LicenceSaison|null findOneBy(array $criteria, array $orderBy = null)
 * @method LicenceSaison[]    findAll()
 * @method LicenceSaison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LicenceSaisonRepository extends ServiceEntityRepository implements SaisonRepositoryInterface {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, LicenceSaison::class);
    }

    public function save(LicenceSaison $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LicenceSaison $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteSaison(Saison $saison, bool $flush = false) {
        $nb = $this->_em->createQueryBuilder()
                ->delete($this->_entityName, 'ls')
                ->where('ls.saison = :saison')
                ->setParameter('saison', $saison->getId())
                ->getQuery()
                ->execute();
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $nb;
    }

    public function findBySaison(Saison $saison): array {
        return $this->createQueryBuilder('l')
                        ->where('l.saison = :saison')
                        ->setParameter('saison', $saison->getId())
                        ->orderBy('l.licence', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

//    /**
//     * @return LicenceSaison[] Returns an array of LicenceSaison objects
//     */
    public function findByExampleField($value): array {
        return $this->createQueryBuilder('l')
                        ->andWhere('l.exampleField = :val')
                        ->setParameter('val', $value)
                        ->orderBy('l.id', 'ASC')
                        ->setMaxResults(10)
                        ->getQuery()
                        ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?LicenceSaison
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
