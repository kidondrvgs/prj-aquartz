<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class PrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom de la prestation',
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4],
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 10000),
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Tarif indicatif du forfait',
                'currency' => 'EUR',
                'input' => 'integer',
                'divisor' => 100,
                'required' => false,
                'help' => 'Laisser vide pour afficher Sur devis. Tarifs de démonstration.',
                'constraints' => [
                    new Range(min: 0, max: 100000000),
                ],
            ])
            ->add('image', ChoiceType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'placeholder' => 'Image par défaut',
                'choices' => $options['images'],
                'data' => $options['image_actuelle'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
            'images' => [],
            'image_actuelle' => null,
        ]);
    }
}
