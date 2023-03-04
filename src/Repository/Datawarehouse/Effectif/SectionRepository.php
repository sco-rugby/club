<?php

namespace App\Repository\Datawarehouse\Effectif;

use App\Entity\Jeu\Saison;
use App\Entity\Datawarehouse\Effectif\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Effectif>
 *
 * @method Effectif|null find($id, $lockMode = null, $lockVersion = null)
 * @method Effectif|null findOneBy(array $criteria, array $orderBy = null)
 * @method Effectif[]    findAll()
 * @method Effectif[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Section::class);
    }

    public function save(Section $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Section $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteSaison(Saison $saison, bool $flush = false) {
        $nb = $this->_em->createQueryBuilder()
                ->delete($this->_entityName, 's')
                ->where('s.saison = :saison')
                ->setParameter('saison', $saison->getId())
                ->getQuery()
                ->execute();
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return $nb;
    }

}
