<?php

namespace App\Repository\Adresse;

use App\Entity\Adresse\Commune;
use App\Entity\Adresse\Adresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commune>
 *
 * @method Commune|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commune|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commune[]    findAll()
 * @method Commune[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommuneRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Commune::class);
    }

    public function save(Commune $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commune $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByAdresse(Adresse $adresse): ?Commune {
        if (null === $adresse->getVille()) {
            return null;
        }
        $adresses = $this->createQueryBuilder('c')
                ->andWhere('c.canonizedNom = :nom')
                ->setParameter('nom', Commune::canonizeNom($adresse->getVille()))
                ->getQuery()
                ->getResult()
        ;
        if (empty($adresses) || count($adresses) > 1) {
            return null;
        } else {
            return $adresses[0];
        }
        //
        /* $exprBuilder = Criteria::expr();
          $expr = $exprBuilder->andX(
          $exprBuilder->eq('canonized_nom', Commune::canonizeNom($adresse->getVille())),
          );
          $result = $adresses->matching(new Criteria($expr)); */
    }

    public function findByNom(string $nom): ?Commune {
        if (empty($nom)) {
            return null;
        }
        $canonizedNom = Commune::canonizeNom($nom);
        return $this->findOneBy(['canonizedNom' => $canonizedNom]);
    }

}
