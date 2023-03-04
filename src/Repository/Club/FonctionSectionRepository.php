<?php

namespace App\Repository\Club;

use App\Entity\Club\FonctionSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Jeu\Saison;
use App\Entity\Club\Section;

/**
 * @extends ServiceEntityRepository<FonctionSection>
 *
 * @method FonctionSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method FonctionSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method FonctionSection[]    findAll()
 * @method FonctionSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FonctionSectionRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, FonctionSection::class);
    }

    public function save(FonctionSection $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FonctionSection $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNbEducateurs(Section $section, Saison $saison, bool $parent = false): int {
        if (false == $parent) {
            return $this->createQueryBuilder('fs')
                            ->select('COUNT(distinct fs.contact)')
                            ->andWhere('fs.section = :section')
                            ->andWhere('fs.saison = :saison')
                            ->andWhere("fs.fonction = 'EDU'")
                            ->setParameter('section', $section->getId())
                            ->setParameter('saison', $saison->getId())
                            ->getQuery()
                            ->getSingleScalarResult();
        } else {
            return $this->createQueryBuilder('fs')
                            ->select('COUNT(distinct fs.contact)')
                            ->join('fs.section', 's')
                            ->andWhere('s.parent = :section')
                            ->andWhere('fs.saison = :saison')
                            ->andWhere("fs.fonction = 'EDU'")
                            ->setParameter('section', $section->getId())
                            ->setParameter('saison', $saison->getId())
                            ->getQuery()
                            ->getSingleScalarResult();
        }
    }

}
