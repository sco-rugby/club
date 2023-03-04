<?php

namespace App\Service\Datawarehouse;

use App\Entity\Jeu\Saison;

final class BuildDatawarehouse extends AbstractBuildService implements DatawarehouseBuilderInterface {

    private $services = [],
            $exec = [];

    public function init(Saison $saison): void {
        //
        $this->services[] = new BuildLicence($this->em);
        $this->services[] = new BuildEffectif($this->em);
        $this->services[] = new BuildGeomap($this->em);
        foreach ($this->services as $service) {
            $service->init($saison);
        }
    }

    public function build(): bool {
        foreach ($this->services as $service) {
            $status = $service->build();
        }
        return true;
    }

    public function shutdown(): void {
        foreach ($this->services as $service) {
            $service->shutdown();
        }
    }

}
