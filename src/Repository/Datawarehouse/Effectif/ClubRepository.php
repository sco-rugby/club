<?php

namespace App\Repository\Datawarehouse\Effectif;

use App\Entity\Jeu\Saison;
use App\Entity\Datawarehouse\Effectif\Club;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Club>
 *
 * @method Club|null find($id, $lockMode = null, $lockVersion = null)
 * @method Club|null findOneBy(array $criteria, array $orderBy = null)
 * @method Club[]    findAll()
 * @method Club[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Club::class);
    }

    public function save(Club $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Club $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteSaison(Saison $saison, bool $flush = false) {
        $nb = $this->_em->createQueryBuilder()
                ->delete($this->_entityName, 'c')
                ->where('c.saison = :saison')
                ->setParameter('saison', $saison->getId())
                ->getQuery()
                ->execute();
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $nb;
    }

}
