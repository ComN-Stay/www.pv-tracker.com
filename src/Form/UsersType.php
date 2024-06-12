<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Users;
use App\Entity\UserStatuses;
use App\Entity\Genders;
use App\Entity\Companies;

class UsersType extends AbstractType
{

    private $security;
    private $currentUser;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->currentUser = $this->security->getUser();
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fk_gender', EntityType::class, [
                'class' => Genders::class,
                'choice_label' => 'name',
                'label' => 'Civilité'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('mobile', TextType::class, [
                'label' => 'Mobile',
                'required' => false,
            ]);
            if(!empty(array_intersect($this->currentUser->getRoles(), ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']))) {
                $builder->add('fk_status', EntityType::class, [
                    'class' => UserStatuses::class,
                    'choice_label' => 'name',
                    'label' => 'Statut'
                ])
                ->add('fk_company', EntityType::class, [
                    'class' => Companies::class,
                    'choice_label' => 'name',
                    'label' => 'Société',
                    'attr' => [
                        'class' => 'select2'
                    ]
                ]);
            }
            if(!empty(array_intersect($this->currentUser->getRoles(), ['ROLE_CLIENT', 'ROLE_ADMIN_CLIENT']))) {
                if($builder->getData()->getId() != null) {
                    $builder->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les mots de passe ne correspondent pas.',
                        'options' => [
                            'attr' => [
                                'class' => 'password-field',
                                'autocomplete' => 'new-password'
                                ]
                            ],
                        'required' => false,
                        'first_options'  => [
                            'label' => 'Mot de passe'
                        ],
                        'second_options' => [
                            'label' => 'Confirmation du mot de passe'
                        ],
                        'help' => 'Le mot de passe doit contenir au minimum 10 caractères avec au minimum 1 minuscule, 1 majuscule, 1 chiffre et un caractère spécial parmis @ ! ? * + - _ ~',
                        'empty_data' => ''
                    ]);
                }
                if(!empty(array_intersect($this->currentUser->getRoles(), ['ROLE_ADMIN_CLIENT']))) {
                    $builder->add('alert_invoice', CheckboxType::class, [
                        'label' => 'Recevoir une alerte lorsqu\'une facture est disponible sur l\'espace client',
                        'required' => false
                    ])
                    ->add('alert_estimate', CheckboxType::class, [
                        'label' => 'Recevoir une alerte lorsqu\'un devis est disponible sur l\'espace client',
                        'required' => false
                    ])
                    ->add('alert_refunds', CheckboxType::class, [
                        'label' => 'Recevoir une alerte lorsqu\'un avoir est disponible sur l\'espace client',
                        'required' => false
                    ])
                    ->add('access_documents', CheckboxType::class, [
                        'label' => 'Autoriser l\'accès aux documents (factures, devis...)',
                        'required' => false
                        ])
                    ->add('access_projects', CheckboxType::class, [
                        'label' => 'Autoriser l\'accès aux suivis de projets',
                        'required' => false
                        ])
                    ->add('access_users', CheckboxType::class, [
                            'label' => 'Autoriser l\'accès à la gestion des administrateurs',
                            'required' => false
                        ])
                    ->add('access_profile', CheckboxType::class, [
                        'label' => 'Autoriser l\'accès au profil société',
                        'required' => false
                        ])
                    ->add('fk_status', EntityType::class, [
                        'class' => UserStatuses::class,
                        'choice_label' => 'name',
                        'label' => 'Statut'
                        ])
                    ;
                }
            
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'allow_extra_fields' => true,
            'user_type' => null
        ]);
    }
}
