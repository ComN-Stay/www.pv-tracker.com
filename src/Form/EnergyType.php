<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Energy;

class EnergyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de production',
                'data' => $options['date']
            ])
            ->add('production', TextType::class, [
                'label' => 'Production (Kwh)'
            ])
            ->add('import', TextType::class, [
                'label' => 'Consommation réseau (Kwh)'
            ])
            ->add('export', TextType::class, [
                'label' => 'Revente OA (Kwh)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Energy::class,
            'date' => null
        ]);
    }
}
