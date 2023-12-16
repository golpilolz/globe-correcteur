<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', EntityType::class, [
                'label' => 'Client',
                'class' => 'App\Entity\Customer',
                'choice_label' => 'name',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Texte de la commande',
                'attr' => [
                    'placeholder' => 'Texte de la commande',
                ]
            ])
            ->add('words', IntegerType::class, [
                'label' => 'Nombre de mots',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('errors', IntegerType::class, [
                'label' => 'Nombre d\'erreurs',
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
