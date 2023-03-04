<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ImportType;
use App\Model\RapportOvale;
use App\Event\ImportFichierEvent;

#[Route('/import', name: 'import')]
class ImportController extends AbstractController {

    #[Route('', name: '_index')]
    public function index(Request $request, EventDispatcherInterface $dispatcher, EntityManagerInterface $em): Response {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();
            $type = $form->get('rapport')->getData();
            $rapport = RapportOvale::$type;
            $service = $rapport->createService($em);
            try {
                // Start import
                $service->load($fichier->getPathname());
                $log = $service->import();
                // Dispatch event
                $event = new ImportFichierEvent($rapport, $fichier);
                $dispatcher->dispatch($event, ImportFichierEvent::NAME);
                // End import
                $service->shutdown();
                $this->addFlash('notice', $log);
            } catch (Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
                $service->shutdown();
            }
            return $this->renderForm('import/index.html.twig', ['form' => $this->createForm(ImportType::class)]);
        }
        return $this->renderForm('import/index.html.twig', ['form' => $form,]);
    }

}
