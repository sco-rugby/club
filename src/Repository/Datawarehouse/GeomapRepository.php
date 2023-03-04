<?php

namespace App\Repository\Datawarehouse;

use App\Entity\Jeu\Saison;
use App\Entity\Datawarehouse\Geomap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Geomap>
 *
 * @method Geomap|null find($id, $lockMode = null, $lockVersion = null)
 * @method Geomap|null findOneBy(array $criteria, array $orderBy = null)
 * @method Geomap[]    findAll()
 * @method Geomap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeomapRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Geomap::class);
    }

    public function save(Geomap $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Geomap $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteSaison(Saison $saison, bool $flush = false) {
        $nb = $this->_em->createQueryBuilder()
                ->delete($this->_entityName, 'g')
                ->where('g.saison = :saison')
                ->setParameter('saison', $saison->getId())
                ->getQuery()
                ->execute();
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $nb;
    }

}
