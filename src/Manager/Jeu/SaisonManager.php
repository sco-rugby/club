<?php

namespace App\Manager\Jeu;

use App\Manager\AbstractResourceManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Jeu\Saison;
use App\Repository\Jeu\SaisonRepository;
use App\Model\ManagedResource;
use App\Model\Affilie\AffilieInterface;
use \App\Command\SaisonUtility;

final class SaisonManager extends AbstractResourceManager {

    use SaisonUtility {
        convertirSaison as public;
    }

    private ?int $annee = null;

    public function __construct(SaisonRepository $repository, private ContainerBagInterface $params, ?EventDispatcherInterface $dispatcher = null, ?ValidatorInterface $validator = null, ?FormFactoryInterface $formFactory = null) {
        parent::__construct($repository, $dispatcher, $validator, $formFactory);
    }

    public function getAnnee(): int {
        if (null === $this->annee) {
            $this->annee = (new \DateTime())->format('Y');
        }
        return $this->annee;
    }

    public function setAnnee(int $annee) {
        $this->annee = $annee;
    }

    private function getParams() {
        return $this->params->get('saison');
    }

    private function getParam($key) {
        $container = $this->getParams();
        if (array_key_exists($key, $container)) {
            return $container[$key];
        }
        return false;
    }

    public function getDateDebut(): ?\DateTimeInterface {
        $date = new \DateTime();
        list($j, $m) = explode('/', $this->getParam('debut'));
        $date->setDate($this->getAnnee(), $m, $j);
        return $date;
    }

    public function getDateFin(): ?\DateTimeInterface {
        return $this->getDateDebut()
                        ->add(new \DateInterval('P1Y'))
                        ->sub(new \DateInterval('P1D'));
    }

    public static function determinerSaison(\DateTimeInterface $date): int {
        $annee = (int) $date->format('Y');
        $mois = (int) $date->format('m');
        list($j, $m) = explode('/', $this->getParam('debut'));
        if ($mois < int_val($m)) {
            $annee--;
        }
        return $annee;
    }

    public function create($properties): ManagedResource {
        if (is_int($properties)) {
            $annee = $properties;
        } else {
            $annee = $this->getAnnee();
        }
        $saison = new Saison($annee);
        $saison->setDebut($this->getDateDebut());
        $saison->setFin($this->getDateFin());
        return parent::create($saison);
    }

    protected function dispatchException(\Exception $ex) {
        return;
    }

    protected function dispatchEvent(string $name) {
        return;
    }

    public function calculerAge(AffilieInterface $affilie): int {
        $this->checkForAction('calculerAge');
        if (null == $affilie->getDateNaissance()) {
            $date = new \DateTime(sprintf('%s-%s-01', substr($affilie->getId(), 3, 2), substr($affilie->getId(), 0, 4)));
        } else {
            $date = $affilie->getDateNaissance();
        }
        $interval = $date->diff($this->getResource()->getDebut());
        return (int) $interval->format('%y');
    }

}
