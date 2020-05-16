<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RoleType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminUserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('slug')
            ->add('picture')
            ->add('description')
            ->add(
                'userRoles',
                CollectionType::class,
                [
                    'entry_type'     => RoleType::class,
                    'mapped'         => false,
                    'required'       => false,
                    'allow_add'      => true,
                    'allow_delete'   => true
                ],

                EntityType::class,
                [
                    'class' => Role::class
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
