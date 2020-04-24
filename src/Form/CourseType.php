<?php

namespace App\Form;

use App\Entity\Course;
use App\Form\VideoType;
//use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CourseType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration("Titre :", "Choisissez un titre pour votre cours")
            )
            ->add(
                'slug',
                TextType::class,
                $this->getConfiguration(
                    "Adresse web :",
                    "Tapez l'adresse web(automatique)",
                    [
                        'required' => false
                    ]
                )
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration(
                    "Question :",
                    "A quelle question répondez-vous ?"
                )
            )
            ->add(
                'content',
                TextareaType::class,
                $this->getConfiguration(
                    "Contenu :",
                    "Contenu explicatif d'un cours(facultatif)",
                    [
                        'required' => false
                    ]
                )
            )
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfiguration("Url de l'image principale :", "Donnez l'Url de l'image d'un cours")
            )
            ->add(
                'videos',
                CollectionType::class,
                [
                    'entry_type'     => VideoType::class,
                    'allow_add'      => true,
                    'allow_delete'   => true
                ]
            )

            ->add(
                'level',
                TextType::class,
                $this->getConfiguration("Niveau du cours :", "Donnez le niveau du cours")
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
