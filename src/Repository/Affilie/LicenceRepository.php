<?php

namespace App\Repository\Affilie;

use App\Entity\Affilie\Licence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SaisonRepositoryInterface;
use App\Entity\Jeu\Saison;
//use App\Entity\Club\Section;
//use App\Entity\Affilie\Qualite;
use App\Model\Affilie\AffilieInterface;

/**
 * @extends ServiceEntityRepository<Licence>
 *
 * @method Licence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Licence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Licence[]    findAll()
 * @method Licence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LicenceRepository extends ServiceEntityRepository implements SaisonRepositoryInterface {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Licence::class);
    }

    public function save(Licence $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Licence $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySaison(Saison $saison): array {
        return $this->createQueryBuilder('l')
                        ->where('l.saison = :saison')
                        ->setParameter('saison', $saison->getId())
                        ->orderBy('l.affilie', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

    public function getStatsAffilie(AffilieInterface $affilie): array {
        return $this->createQueryBuilder('l')
                        ->select('MIN(l.saison) AS debut, MAX(l.saison) AS fin, COUNT(distinct l.saison) nb')
                        ->where('l.affilie = :affilie')
                        ->setParameter('affilie', $affilie->getId())
                        ->getQuery()
                        ->getSingleResult();
    }

    public function getNbSaisons(AffilieInterface $affilie, Saison $saison = null): int {
        if (null === $saison) {
            return $this->createQueryBuilder('l')->select('COUNT(distinct l.saison)')
                            ->andWhere('l.affilie = :affilie')
                            ->setParameter('affilie', $affilie->getId())
                            ->getQuery()
                            ->getSingleScalarResult();
        } else {
            return $this->createQueryBuilder('l')->select('COUNT(distinct l.saison)')
                            ->andWhere('l.affilie = :affilie')
                            ->andWhere('l.saison <= :saison')
                            ->setParameter('affilie', $affilie->getId())
                            ->setParameter('saison', $saison->getId())
                            ->getQuery()
                            ->getSingleScalarResult();
        }
    }

    /* public function getNbEducateurs(Section $section, Saison $saison): int {
      return $this->createQueryBuilder('l')->select('COUNT(distinct l.id)')
      ->join(Qualite::class, 'q')
      ->andWhere('l.saison = :saison')
      ->andWhere("q.groupe = 'E'")
      ->setParameter('saison', $saison->getId())
      ->getQuery()
      ->getOneOrNullResult();
      } */
}
