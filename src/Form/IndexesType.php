<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Indexes;

class IndexesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('production', TextType::class, [
                'label' => 'Index de production (Kwh)',
                'data' => 0
            ])
            ->add('resale', TextType::class, [
                'label' => 'Index de revente OA (Kwh)',
                'data' => 0
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Indexes::class,
        ]);
    }
}
