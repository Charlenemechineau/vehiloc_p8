<?php

namespace App\Controller;

// Permet d'accéder aux voitures dans la base//
use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoituresController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function accueil(VoitureRepository $voitureRepository): Response
    {
        // 1. Récupérer la liste des voitures depuis la base//
        $voitures = $voitureRepository->findAll();

        // 2. Afficher le template accueil.html.twig//
        // 3. Lui passer la liste des voitures//
        return $this->render('accueil.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    #[Route('/voiture/{id}', name: 'app_voiture_detail')]
    public function detail(int $id, VoitureRepository $voitureRepository): Response
    {
        // Récupérer la voiture par son id dans l'URL//
        $voiture = $voitureRepository->find($id);

        // Si pas trouvée -> retour à l'accueil//
        if (!$voiture) {
            return $this->redirectToRoute('app_accueil');
        }

        // Sinon, afficher le template détail//
        return $this->render('voitures/detail.html.twig', [
            'voiture' => $voiture,
        ]);
    }

}
