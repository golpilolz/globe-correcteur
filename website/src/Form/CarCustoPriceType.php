<?php

namespace App\Form;

use App\Entity\CarCustoPrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarCustoPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('engine', ChoiceType::class, [
                'choices' => [
                    '0' => 0,
                    '1' => 0.1,
                    '2' => 0.15,
                    '3' => 0.2,
                    '4' => 0.25
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarCustoPrice::class,
        ]);
    }
}
