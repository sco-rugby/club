<?php

namespace App\Repository\Jeu;

use App\Entity\Jeu\Saison;
use Doctrine\DBAL\Types\Types;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Saison>
 *
 * @method Saison|null find($id, $lockMode = null, $lockVersion = null)
 * @method Saison|null findOneBy(array $criteria, array $orderBy = null)
 * @method Saison[]    findAll()
 * @method Saison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaisonRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Saison::class);
    }

    public function save(Saison $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Saison $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return null|Saison Retourne la saison en cours
     */
    public function findEnCours(): ?Saison {
        return $this->findByDate(new \DateTimeImmutable());
    }

    public function findByDate(\DateTimeInterface $date): ?Saison {
        $qb = $this->createQueryBuilder('s');
        return $qb->select('s')
                        ->andWhere($qb->expr()->lte('s.debut', ':date'))
                        ->andWhere($qb->expr()->gte('s.fin', ':date'))
                        ->setParameter('date', $date, Types::DATETIME_MUTABLE)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

}
