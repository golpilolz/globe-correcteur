<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $now = new \DateTime();

        $builder
            ->add('month', ChoiceType::class, [
                'choices' => [
                    'Janvier' => '01',
                    'Février' => '02',
                    'Mars' => '03',
                    'Avril' => '04',
                    'Mai' => '05',
                    'Juin' => '06',
                    'Juillet' => '07',
                    'Août' => '08',
                    'Septembre' => '09',
                    'Octobre' => '10',
                    'Novembre' => '11',
                    'Décembre' => '12',
                ],
                'label' => 'Mois',
                'data' => $now->format('m'),
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control',
                ],
            ])
            ->add('year', ChoiceType::class, [
                'choices' => [
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                    '2026' => '2026',
                    '2027' => '2027',
                    '2028' => '2028',
                    '2029' => '2029',
                    '2030' => '2030',
                ],
                'label' => 'Année',
                'data' => $now->format('Y'),
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
