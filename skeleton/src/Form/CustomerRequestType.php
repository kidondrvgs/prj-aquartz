<?php

namespace App\Form;

use App\Entity\CustomerRequest;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;

// Formulaire de la page "Nouvelle demande"
class CustomerRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Liste des prestations prise dans la table service
            // mapped false car la prestation est ajoutée dans le contrôleur
            ->add('prestation', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'title',
                'mapped' => false,
                'label' => 'Prestations *',
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new Count(min: 1, minMessage: 'Choisissez au moins une prestation.'),
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

            // Un seul champ pour la date
            ->add('dateRequest', DateType::class, [
                'label' => 'Date souhaitée *',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(message: 'Choisissez une date.'),
                    new GreaterThanOrEqual('today', message: 'La date doit être aujourd\'hui ou plus tard.'),
                ],
            ])

            ->add('surface', IntegerType::class, [
                'label' => 'Surface concernée (m²) *',
                'attr' => ['placeholder' => 'Ex : 250'],
                'constraints' => [
                    new NotBlank(message: 'Indiquez la surface.'),
                    new Positive(message: 'La surface doit être un nombre positif.'),
                ],
            ])

            // Adresse complète : numéro, rue, code postal (5 chiffres) puis ville
            // \p{L} = une lettre, même avec un accent
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

    // Le formulaire remplit une demande
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerRequest::class,
        ]);
    }
}
