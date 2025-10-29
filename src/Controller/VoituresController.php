<?php

namespace App\Controller;

// Permet d'accéder aux voitures dans la base//
use App\Repository\VoitureRepository;
// Permet de gérer les opérations en base//
// (ajout, modification, suppression)//
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
// j'injecte le VoitureRepository pour récupérer la voiture à supprimer//
// j'injecte aussi l'EntityManagerInterface pour faire la suppression//
// j'indique que l'id dans l'URL est de type int//
// la méthode retourne une Response//
public function supprimer(int $id, VoitureRepository $voitureRepository, EntityManagerInterface $em): Response
{
    // je récupère la voiture correspondant à l'id envoyé dans l'URL//
    $voiture = $voitureRepository->find($id);

    // Si aucune voiture ne correspond à cet id, on redirige vers la page d'accueil//
    // Cela évite une erreur si l'utilisateur tape manuellement une mauvaise URL //
    if (!$voiture) {
        return $this->redirectToRoute('app_accueil');
    }

    // je demande à Doctrine de SUPPRIMER cette voiture
    // - remove() prépare la suppression (Doctrine marque l’objet comme “à supprimer”)
    // - flush() exécute réellement la requête SQL DELETE en base
    $em->remove($voiture);
    $em->flush();

    //  Une fois la suppression faite, on redirige l’utilisateur vers la page d’accueil//
    // Ainsi, il voit la liste des voitures à jour (sans celle qui vient d’être supprimée)//
    return $this->redirectToRoute('app_accueil');
}

}
