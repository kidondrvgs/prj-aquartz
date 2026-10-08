<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;

// Formulaire de la page "Nouvelle demande"
class CustomerRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Prestations écrites en dur pour l'instant
            // À gauche le texte affiché, à droite la valeur envoyée
            ->add('prestation', ChoiceType::class, [
                'label' => 'Prestation *',
                'placeholder' => 'Choisir une prestation',
                'choices' => [
                    'Terrassement général' => 'terrassement',
                    'Assainissement' => 'assainissement',
                    'Tranchées et réseaux' => 'tranchees',
                    'Démolition' => 'demolition',
                    'Aménagement extérieur' => 'amenagement-exterieur',
                    'Location de pelle avec chauffeur' => 'location-pelle',
                ],
                'constraints' => [
                    new NotBlank(message: 'Choisissez une prestation.'),
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description de votre besoin *',
                'attr' => [
                    'placeholder' => 'Décrivez votre chantier : ce que vous souhaitez faire, contraintes, échéances...',
                    'rows' => 4,
                ],
                'constraints' => [
                    new NotBlank(message: 'Décrivez votre besoin.'),
                ],
            ])

            // single_text = un seul champ date (jj/mm/aaaa)
            ->add('dateRequest', DateType::class, [
                'label' => 'Date souhaitée *',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(message: 'Choisissez une date.'),
                    new GreaterThanOrEqual('today', message: 'La date doit être aujourd\'hui ou plus tard.'),
                ],
            ])

            // Pas obligatoire
            ->add('surface', IntegerType::class, [
                'label' => 'Surface concernée (m²)',
                'required' => false,
                'attr' => ['placeholder' => 'Ex : 250'],
                'constraints' => [
                    new Positive(message: 'La surface doit être un nombre positif.'),
                ],
            ])

            // Adresse complète : numéro, rue, code postal (5 chiffres) puis ville
            // \p{L} = une lettre (accents compris, pour Évreux par exemple)
            ->add('location', TextType::class, [
                'label' => 'Adresse du chantier *',
                'attr' => ['placeholder' => 'N° et rue, code postal, ville'],
                'constraints' => [
                    new NotBlank(message: 'Indiquez l\'adresse du chantier.'),
                    new Regex('/^[0-9]+.*\p{L}.*[0-9]{5} *\p{L}/u', message: 'Indiquez l\'adresse complète : numéro, rue, code postal et ville (ex : 12 rue des Lilas, 76000 Rouen).'),
                ],
            ])
        ;
    }
}
