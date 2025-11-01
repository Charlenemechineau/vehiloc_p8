<?php

// ========================================
// DÉCLARATION DU NAMESPACE
// ========================================
// Définit l'emplacement de ce fichier dans l'arborescence du projet
// Tous les contrôleurs sont dans App\Controller
namespace App\Controller;

// ========================================
// IMPORTS DES CLASSES NÉCESSAIRES
// ========================================
// J'importe toutes les classes dont j'ai besoin dans ce contrôleur
use App\Entity\Voiture;                           // L'entité Voiture (représente la table en base)
use App\Form\VoitureType;                         // Le formulaire pour créer/modifier une voiture
use App\Repository\VoitureRepository;             // Pour récupérer les voitures depuis la base
use Doctrine\ORM\EntityManagerInterface;          // Pour sauvegarder/supprimer en base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base des contrôleurs
use Symfony\Component\HttpFoundation\Request;     // Pour gérer les requêtes HTTP (GET, POST, etc.)
use Symfony\Component\HttpFoundation\Response;    // Pour renvoyer une réponse (page HTML, redirection)
use Symfony\Component\Routing\Annotation\Route;   // Pour définir les routes avec des annotations

// ========================================
// CLASSE CONTRÔLEUR DES VOITURES
// ========================================
// Ce contrôleur gère toutes les actions liées aux voitures :
// - Afficher la liste
// - Afficher le détail d'une voiture
// - Ajouter une nouvelle voiture
// - Supprimer une voiture
class VoituresController extends AbstractController
{
    // ========================================
    // ROUTE 1 : PAGE D'ACCUEIL (LISTE DES VOITURES)
    // ========================================
    // Cette route affiche la liste complète de toutes les voitures
    // URL : / (racine du site)
    // Nom de la route : 'app_accueil' (utilisé pour générer des liens avec path())
    #[Route('/', name: 'app_accueil')]
    // J'injecte VoitureRepository pour accéder aux voitures en base
    // La méthode retourne une Response (la page HTML)
    public function accueil(VoitureRepository $voitureRepository): Response
    {
        // ÉTAPE 1 : Je récupère TOUTES les voitures depuis la base de données
        // findAll() fait une requête SQL : SELECT * FROM voiture
        // $voitures contient un tableau d'objets Voiture
        $voitures = $voitureRepository->findAll();

        // ÉTAPE 2 : J'affiche le template Twig 'accueil.html.twig'
        // Je lui passe la variable 'voitures' pour qu'elle soit accessible dans le template
        // Dans le Twig, je pourrai faire : {% for voiture in voitures %}
        return $this->render('accueil.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    // ========================================
    // ROUTE 2 : AJOUTER UNE NOUVELLE VOITURE
    // ========================================
    // IMPORTANT : Cette route DOIT être AVANT /voiture/{id}
    // Sinon Symfony confond "ajouter" avec un id et appelle la méthode detail()
    // URL : /voiture/ajouter
    #[Route('/voiture/ajouter', name: 'app_voiture_ajouter',)]
    // J'injecte 2 paramètres :
    // - Request : pour récupérer les données du formulaire soumis
    // - EntityManagerInterface : pour sauvegarder la nouvelle voiture en base
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        // ÉTAPE 1 : Je crée un nouvel objet Voiture vide
        // Cet objet sera rempli automatiquement par le formulaire
        $voiture = new Voiture();

        // ÉTAPE 2 : Je crée le formulaire
        // - VoitureType::class : le type de formulaire (défini dans src/Form/VoitureType.php)
        // - $voiture : l'objet à remplir avec les données du formulaire
        // Symfony va automatiquement lier les champs du formulaire aux propriétés de $voiture
        $form = $this->createForm(VoitureType::class, $voiture);

        // ÉTAPE 3 : Je demande au formulaire d'analyser la requête HTTP
        // Si l'utilisateur a soumis le formulaire (cliqué sur "Enregistrer"),
        // Symfony remplit automatiquement l'objet $voiture avec les données saisies
        $form->handleRequest($request);

        // ÉTAPE 4 : Je vérifie si le formulaire a été soumis ET si les données sont valides
        // isSubmitted() : vérifie que le formulaire a bien été envoyé (POST)
        // isValid() : vérifie que les contraintes de validation sont respectées
        if ($form->isSubmitted() && $form->isValid()) {
            // ÉTAPE 5a : Je prépare l'enregistrement de la nouvelle voiture
            // persist() : dit à Doctrine "je veux sauvegarder cet objet"
            // À ce stade, rien n'est encore enregistré en base !
            $em->persist($voiture);

            // ÉTAPE 5b : J'exécute RÉELLEMENT la requête SQL INSERT en base
            // flush() : exécute toutes les opérations en attente
            // SQL généré : INSERT INTO voiture (nom, description, prix...) VALUES (...)
            $em->flush();

            // ÉTAPE 6 : Une fois sauvegardée, je redirige vers la page de détail
            // Je passe l'id de la voiture nouvellement créée dans l'URL
            // Exemple : /voiture/15 (si l'id généré est 15)
            return $this->redirectToRoute('app_voiture_detail', [
                'id' => $voiture->getId(),
            ]);
        }

        // ÉTAPE 7 : Si le formulaire n'est pas encore soumis OU contient des erreurs
        // J'affiche le template avec le formulaire
        // createView() : transforme le formulaire en version affichable dans Twig
        return $this->render('voitures/nouvelle-voiture.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ========================================
    // ROUTE 3 : AFFICHER LE DÉTAIL D'UNE VOITURE
    // ========================================
    // Cette route affiche toutes les informations d'une voiture spécifique
    // URL : /voiture/5 (affiche la voiture avec l'id 5)
    // {id} est un paramètre dynamique qui sera récupéré dans $id
    #[Route('/voiture/{id}', name: 'app_voiture_detail',requirements: ['id' => '\d+'])]
    // J'injecte 2 paramètres :
    // - int $id : l'identifiant de la voiture (vient de l'URL)
    // - VoitureRepository : pour chercher la voiture en base
    public function detail(int $id, VoitureRepository $voitureRepository): Response
    {
        // ÉTAPE 1 : Je récupère la voiture correspondant à l'id dans l'URL
        // find($id) fait une requête SQL : SELECT * FROM voiture WHERE id = ?
        // Si la voiture existe, $voiture contient l'objet Voiture
        // Si elle n'existe pas, $voiture vaut null
        $voiture = $voitureRepository->find($id);

        // ÉTAPE 2 : Je vérifie si la voiture a été trouvée
        // Si $voiture est null (voiture introuvable)
        if (!$voiture) {
            // Je redirige vers la page d'accueil
            // Cela évite une erreur 500 si l'utilisateur tape /voiture/999 (id inexistant)
            return $this->redirectToRoute('app_accueil');
        }

        // ÉTAPE 3 : Si la voiture existe, j'affiche le template de détail
        // Je passe l'objet $voiture au template pour afficher ses informations
        // Dans le Twig : {{ voiture.nom }}, {{ voiture.prix }}, etc.
        return $this->render('voitures/detail.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    // ========================================
    // ROUTE 4 : SUPPRIMER UNE VOITURE
    // ========================================
    // Cette route permet de supprimer définitivement une voiture de la base de données
    // URL : /voiture/5/supprimer (supprime la voiture avec l'id 5)
    #[Route('/voiture/{id}/supprimer', name: 'app_voiture_supprimer')]
    // J'injecte 3 paramètres :
    // - int $id : l'identifiant de la voiture à supprimer (vient de l'URL)
    // - VoitureRepository : pour récupérer la voiture depuis la base de données
    // - EntityManagerInterface : pour effectuer la suppression en base
    public function supprimer(int $id, VoitureRepository $voitureRepository, EntityManagerInterface $em): Response
    {
        // ÉTAPE 1 : Je récupère la voiture correspondant à l'id envoyé dans l'URL
        // find($id) fait une requête SQL : SELECT * FROM voiture WHERE id = ?
        $voiture = $voitureRepository->find($id);

        // ÉTAPE 2 : Je vérifie si la voiture existe vraiment
        // Si aucune voiture ne correspond à cet id, $voiture vaut null
        if (!$voiture) {
            // Je redirige l'utilisateur vers la page d'accueil
            // Cela évite une erreur si quelqu'un tape manuellement une mauvaise URL
            // Exemple : /voiture/999/supprimer (si l'id 999 n'existe pas)
            return $this->redirectToRoute('app_accueil');
        }

        // ÉTAPE 3 : Je supprime la voiture de la base de données
        // remove() : prépare la suppression (Doctrine marque l'objet comme "à supprimer")
        // C'est comme dire : "je veux supprimer cet objet"
        $em->remove($voiture);

        // flush() : exécute RÉELLEMENT la requête SQL DELETE dans la base
        // C'est comme dire : "maintenant, exécute toutes les modifications en attente"
        // SQL généré : DELETE FROM voiture WHERE id = ?
        // Sans flush(), rien ne se passe réellement en base !
        $em->flush();

        // ÉTAPE 4 : Une fois la suppression terminée, je redirige vers l'accueil
        // L'utilisateur verra la liste des voitures mise à jour (sans celle supprimée)
        return $this->redirectToRoute('app_accueil');
    }
}