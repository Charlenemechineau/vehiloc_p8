<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoituresController extends AbstractController
{
    // route pour afficher la liste des voitures//
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

    // ⚠️ IMPORTANT : route SPÉCIFIQUE AVANT la route dynamique //
    // route pour ajouter une nouvelle voiture //
    #[Route('/voiture/ajouter', name: 'app_voiture_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        // je crée une nouvelle instance de voiture  //
        $voiture = new Voiture();

        // je crée le formulaire basé sur le VoitureType et lié à l'objet $voiture //
        // cela permet à Symfony de remplir automatiquement les champs lors de la soumission //
        $form = $this->createForm(VoitureType::class, $voiture);

        // je demande au formulaire d'analyser la requête HTTP //
        // -> si le formulaire est soumis, Symfony hydrate automatiquement l'objet $voiture //
        $form->handleRequest($request);

        // je vérifie si le formulaire a été soumis et que les champs sont valides //
        if ($form->isSubmitted() && $form->isValid()) {
            // je demande à Doctrine de préparer l'enregistrement de la nouvelle voiture //
            $em->persist($voiture);

            // j'exécute réellement la requête SQL INSERT pour sauvegarder la voiture en base //
            $em->flush();

            // une fois la voiture enregistrée, je redirige l'utilisateur vers la page de détail //
            return $this->redirectToRoute('app_voiture_detail', [
                'id' => $voiture->getId(),
            ]);
        }

        // si le formulaire n'est pas encore soumis ou contient des erreurs, je l'affiche //
        return $this->render('voitures/nouvelle-voiture.html.twig', [
            'form' => $form->createView(), // j'envoie le formulaire à la vue Twig //
        ]);
    }

    // route pour afficher le détail d'une voiture//
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

    // route pour supprimer une voiture//
    #[Route('/voiture/{id}/supprimer', name: 'app_voiture_supprimer')]
    public function supprimer(int $id, VoitureRepository $voitureRepository, EntityManagerInterface $em): Response
    {
        $voiture = $voitureRepository->find($id);

        if (!$voiture) {
            return $this->redirectToRoute('app_accueil');
        }

        $em->remove($voiture);
        $em->flush();

        return $this->redirectToRoute('app_accueil');
    }
}