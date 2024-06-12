<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Prices;

class PricesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_start', null, [
                'widget' => 'single_text',
                'label' => 'Date de début d\'affectation',
                'data' => $options['date']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Tarif achat Kwh' => 'consumption',
                    'Tarif de vente TTC' => 'resale',
                    'Abonnement' => 'subscription'
                ]
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Montant TTC'
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'Actif',
                'choices' => [
                    'Oui' => '1',
                    'Non' => '0'
                ],
                'expanded' => true,
                'data' => $options['active']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prices::class,
            'active' => null,
            'date' => null
        ]);
    }
}
