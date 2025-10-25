<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoituresController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function accueil(VoitureRepository $voitureRepository): Response
    {
        // 1. Récupérer la liste des voitures depuis la base
        $voitures = $voitureRepository->findAll();

        // 2. Afficher le template accueil.html.twig
        // 3. Lui passer la liste des voitures
        return $this->render('accueil.html.twig', [
            'voitures' => $voitures,
        ]);
    }
}
