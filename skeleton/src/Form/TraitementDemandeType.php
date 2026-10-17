<?php

namespace App\Form;

use App\Entity\CustomerRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TraitementDemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Changer le statut',
                'choices' => array_combine(CustomerRequest::STATUTS, CustomerRequest::STATUTS),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('reponse', TextareaType::class, [
                'label' => 'Réponse au client',
                'required' => false,
                'attr' => ['rows' => 5, 'placeholder' => 'Disponibilité, devis, informations complémentaires...'],
                'constraints' => [
                    new Length(max: 10000),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerRequest::class,
        ]);
    }
}
