<?php

namespace App\Repository\Contact;

use App\Entity\Contact\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Contact[] Returns an array of Contact objects
     */
    public function findAllForSearch(): array {
        return $this->getEntityManager()
                        ->createQuery(
                                'SELECT c, UPPER(c.nom) canonized_nom, UPPER(c.prenom) canonized_prenom FROM App\Entity\Contact\Contact c order by c.nom ASC, c.prenom ASC'
                        )
                        ->getScalarResult();
    }

}
