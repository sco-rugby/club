<?php

namespace App\Repository\Club;

use App\Entity\Club\FonctionClub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Jeu\Saison;
use App\Entity\Club\Fonction;

/**
 * @extends ServiceEntityRepository<FonctionClub>
 *
 * @method FonctionClub|null find($id, $lockMode = null, $lockVersion = null)
 * @method FonctionClub|null findOneBy(array $criteria, array $orderBy = null)
 * @method FonctionClub[]    findAll()
 * @method FonctionClub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FonctionClubRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, FonctionClub::class);
    }

    public function save(FonctionClub $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FonctionClub $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNbBenevoles(Saison $saison): int {
        return $this->createQueryBuilder('f')->select('COUNT(distinct f.contact)')
                        ->andWhere('f.saison <= :saison')
                        ->andWhere("f.fonction = 'BENE'")
                        ->setParameter('saison', $saison->getId())
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    public function findBenevolesBySaison(Saison $saison): array {
        $fonction = new Fonction();
        $fonction->setId('BENE');
        return $this->findByFonctionAndSaison($fonction, $saison);
    }

    public function findByFonctionAndSaison(Fonction $fonction, Saison $saison): array {
        return $this->findBy(['fonction' => $fonction->getId(), 'saison' => $saison->getId()]);
    }

    public function findBySaison(Saison $saison): array {
        return $this->findBy(['saison' => $saison->getId()]);
    }

}
