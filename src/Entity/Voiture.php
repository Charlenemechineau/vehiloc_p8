<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// Cette ligne permet d'utiliser le composant Validator de Symfony.//
// Grâce à lui, on peut ajouter des règles de validation sur les champs de l'entité,//
// comme "le nom ne doit pas être vide" ou "le prix doit être positif".//
use Symfony\Component\Validator\Constraints as Assert;

// cette classe représente une voiture dans la base de données
// chaque propriété = une colonne dans la table voiture
#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    // l'id de la voiture 
    // il est généré automatiquement par la base de données
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    // le nom de la voiture //
    #[ORM\Column(length: 255)]
    private ?string $nom = null;


    // la marque de la voiture 
    // longueur max 255 caractères
    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    // la description de la voiture (texte long)
    // nullable true = peut être vide
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    // le prix pour louer la voiture à la journée
    #[ORM\Column]
    private ?int $prix_journalier = null;

    // le prix pour louer la voiture au mois
    #[ORM\Column]
    private ?int $prix_mensuel = null;

    // le nombre de places dans la voiture
    #[ORM\Column]
    private ?int $nbPlaces = null;

    // le type de boite de vitesse (Manuelle ou Automatique)
    // longueur max 50 caractères
    #[ORM\Column(length: 50)]
    private ?string $boite_vitesse = null;

    // où se trouve la voiture (ville, adresse...)
    #[ORM\Column(length: 255)]
    private ?string $localisation = null;


    // getter pour récupérer l'id
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    // setter pour modifier le nom
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    // getter pour récupérer le nom
    public function getMarque(): ?string
    {
        return $this->marque;
    }

    // setter pour modifier le nom
    // return $this permet de chaîner les setters
    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    // getter pour récupérer la description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // setter pour modifier la description
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    // getter pour récupérer le prix journalier
    public function getPrixJournalier(): ?int
    {
        return $this->prix_journalier;
    }

    // setter pour modifier le prix journalier
    public function setPrixJournalier(int $prix_journalier): static
    {
        $this->prix_journalier = $prix_journalier;

        return $this;
    }

    // getter pour récupérer le prix mensuel
    public function getPrixMensuel(): ?int
    {
        return $this->prix_mensuel;
    }

    // setter pour modifier le prix mensuel
    public function setPrixMensuel(int $prix_mensuel): static
    {
        $this->prix_mensuel = $prix_mensuel;

        return $this;
    }

    // getter pour récupérer le nombre de places
    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    // setter pour modifier le nombre de places
    public function setNbPlaces(int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    // getter pour récupérer le type de boite
    public function getBoiteVitesse(): ?string
    {
        return $this->boite_vitesse;
    }

    // setter pour modifier le type de boite
    public function setBoiteVitesse(string $boite_vitesse): static
    {
        $this->boite_vitesse = $boite_vitesse;

        return $this;
    }

    // getter pour récupérer la localisation
    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    // setter pour modifier la localisation
    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }
}