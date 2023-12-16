<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du client',
                'attr' => [
                    'placeholder' => 'Nom du client',
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone du client',
                'attr' => [
                    'placeholder' => 'Téléphone du client',
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de client',
                'choices' => [
                    'Particulier' => 'particulier',
                    'Enterprise' => 'entreprise'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
