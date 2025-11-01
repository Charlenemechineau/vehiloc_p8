<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// classe pour créer le formulaire d'ajout de voiture
class VoitureType extends AbstractType
{
    // méthode qui construit le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // champ pour la marque de la voiture (texte simple)
            ->add('marque', TextType::class, [
                'label' => 'Marque de la voiture',
            ])
            
            // champ pour la description (texte long sur plusieurs lignes)
            // required false = pas obligatoire
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            
            // champ pour le prix à la journée (nombre entier)
            ->add('prix_journalier', IntegerType::class, [
                'label' => 'Prix journalier (€)',
            ])
            
            // champ pour le prix au mois (nombre entier)
            ->add('prix_mensuel', IntegerType::class, [
                'label' => 'Prix mensuel (€)',
            ])
            
            // champ pour le nombre de places
            ->add('nbPlaces', IntegerType::class, [
                'label' => 'Nombre de places',
            ])
            
            // liste déroulante pour choisir le type de boite
            // choices = les options disponibles dans la liste
            ->add('boite_vitesse', ChoiceType::class, [
                'label' => 'Boîte de vitesses',
                'choices' => [
                    'Manuelle' => 'Manuelle',
                    'Automatique' => 'Automatique',
                ],
            ])
            
            // champ pour indiquer où se trouve la voiture
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
            ])
        ;
    }

    // méthode pour lier le formulaire à l'entité Voiture
    // ça permet à Symfony de remplir automatiquement l'objet Voiture avec les données du form
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}